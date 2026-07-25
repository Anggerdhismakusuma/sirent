<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductAvailability;
use App\Models\RentalRequest;
use App\Models\User;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private MidtransService $midtransService
    ) {}

    /**
     * STEP 1 — Initialize checkout session.
     *
     * POST /checkout/init
     *
     * Validates rental params, calculates totals, checks availability,
     * and stores the order in the session with a 32-byte hex token.
     * No database record is created yet.
     */
    public function init(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::where('status', Product::STATUS_ACTIVE)
            ->findOrFail($validated['product_id']);

        $start     = Carbon::parse($validated['start_date'])->startOfDay();
        $end       = Carbon::parse($validated['end_date'])->startOfDay();
        $totalDays = (int) $start->diffInDays($end) + 1; // inclusive
        $quantity  = (int) $validated['quantity'];

        $totalPrice = $totalDays * (float) $product->price_per_day * $quantity;
        $serviceFee = 2000;
        $grandTotal = round($totalPrice + $serviceFee, 2);

        // ── Validate availability (blocked dates + overlapping rentals) ──
        $this->checkAvailability($product->id, $start, $end);

        // ── Generate secure session token (64 hex chars) ──
        $token = bin2hex(random_bytes(32));

        session()->put("checkout.{$token}", [
            'product_id'  => $product->id,
            'start_date'  => $start->toDateString(),
            'end_date'    => $end->toDateString(),
            'quantity'    => $quantity,
            'total_days'  => $totalDays,
            'total_price' => round($totalPrice, 2),
            'service_fee' => $serviceFee,
            'grand_total' => $grandTotal,
            'expires_at'  => now()->addMinutes(30)->timestamp,
        ]);

        return response()->json([
            'success'      => true,
            'redirect_url' => route('checkout.index', ['token' => $token]),
        ]);
    }

    /**
     * STEP 2 — Display the checkout page.
     *
     * GET /checkout/{token}
     *
     * Reads session data, validates expiry, and renders the order summary.
     */
    public function index(string $token): View
    {
        $data = session("checkout.{$token}");

        abort_if(! $data, 404, 'Checkout session not found or already used.');
        abort_if(($data['expires_at'] ?? 0) < now()->timestamp,
            410,
            'Checkout session has expired. Please start again.'
        );

        $product = Product::with(['primaryImage', 'owner', 'category'])
            ->findOrFail($data['product_id']);

        return view('checkout.index', compact('data', 'product', 'token'));
    }

    /**
     * STEP 3 — Process payment: create RentalRequest + get Snap token.
     *
     * POST /checkout/{token}/pay
     *
     * Re-validates availability, creates the RentalRequest with
     * payment_status=pending, generates a Snap token via Midtrans,
     * clears the checkout session, and returns the token to the frontend.
     */
    public function pay(string $token): JsonResponse
    {
        $data = session("checkout.{$token}");

        abort_if(! $data, 404, 'Checkout session not found or already used.');
        abort_if(($data['expires_at'] ?? 0) < now()->timestamp,
            410,
            'Checkout session has expired. Please start again.'
        );

        $user = auth()->user();

        abort_if(
            $user->verification_status !== User::VERIFICATION_VERIFIED,
            403,
            __('ui.rental_restricted_unverified')
        );

        $product = Product::where('status', Product::STATUS_ACTIVE)
            ->findOrFail($data['product_id']);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->startOfDay();

        // ── Re-validate availability (prevents race conditions) ──
        $this->checkAvailability($product->id, $start, $end);

        // ── Create RentalRequest inside a transaction ──
        $rentalRequest = DB::transaction(function () use ($data, $product, $start, $end, $user) {
            // Pessimistic lock on the product row to prevent concurrent bookings
            Product::where('id', $product->id)->lockForUpdate()->first();

            // Double-check availability under lock
            $this->checkAvailability($product->id, $start, $end);

            return RentalRequest::create([
                'borrower_id'    => $user->id,
                'product_id'     => $product->id,
                'owner_id'       => $product->owner_id,
                'start_date'     => $start->toDateString(),
                'end_date'       => $end->toDateString(),
                'total_days'     => $data['total_days'],
                'quantity'       => $data['quantity'],
                'total_price'    => $data['total_price'],
                'status'         => RentalRequest::STATUS_PENDING,
                'payment_status' => RentalRequest::PAYMENT_PENDING,
            ]);
        });

        // ── Generate Midtrans Snap token ──
        try {
            $snapToken = $this->midtransService->createSnapToken($rentalRequest);

            $rentalRequest->update([
                'payment_method' => 'midtrans',
            ]);

            // Clear the checkout session
            session()->forget("checkout.{$token}");

            return response()->json([
                'success'    => true,
                'snap_token' => $snapToken,
                'rental_id'  => $rentalRequest->id,
                'message'    => 'Payment initiated.',
            ]);
        } catch (\Exception $e) {
            logger()->error('Midtrans Snap token generation failed', [
                'rental_id' => $rentalRequest->id,
                'error'     => $e->getMessage(),
            ]);

            $rentalRequest->update([
                'payment_status' => RentalRequest::PAYMENT_FAILED,
            ]);

            // Still clear the session so they can't retry the same token
            session()->forget("checkout.{$token}");

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again.',
            ], 500);
        }
    }

    /**
     * Validate product availability (blocked dates + overlapping rentals).
     *
     * Throws 422 on failure so the frontend can display the error.
     */
    private function checkAvailability(int $productId, Carbon $start, Carbon $end): void
    {
        // ── Blocked dates check ──
        $blockedExists = ProductAvailability::where('product_id', $productId)
            ->whereBetween('blocked_date', [$start->toDateString(), $end->toDateString()])
            ->exists();

        abort_if(
            $blockedExists,
            422,
            'Tanggal yang dipilih tidak tersedia. Ada tanggal yang diblokir oleh pemilik produk.'
        );

        // ── Overlapping rental check ──
        //     Exclude: cancelled, rejected, payment-expired, payment-failed
        $overlapExists = RentalRequest::where('product_id', $productId)
            ->whereIn('status', [
                RentalRequest::STATUS_PENDING,
                RentalRequest::STATUS_APPROVED,
                RentalRequest::STATUS_ONGOING,
            ])
            ->whereNotIn('payment_status', [
                RentalRequest::PAYMENT_EXPIRED,
                RentalRequest::PAYMENT_FAILED,
            ])
            ->where(function ($q) use ($start, $end) {
                $q->where('start_date', '<=', $end->toDateString())
                    ->where('end_date', '>=', $start->toDateString());
            })
            ->exists();

        abort_if(
            $overlapExists,
            422,
            'Tanggal yang dipilih bentrok dengan peminjaman lain yang sudah disetujui.'
        );
    }
}
