<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRentalRequest;
use App\Models\Product;
use App\Models\RentalRequest;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class RentalController extends Controller
{
    /**
     * AJUKAN PEMINJAMAN — POST /peminjaman (PRD section 16.3)
     *
     * Business rules (PRD section 14):
     * - Tolak jika tanggal overlap dengan blocked_date di product_availabilities
     * - Tolak jika tanggal overlap dengan rental_requests berstatus approved/ongoing
     * - total_days dan total_price dihitung otomatis dari price_per_day produk
     */
    public function store(StoreRentalRequest $request): JsonResponse
    {
        // Only verified users can rent
        if (auth()->user()->verification_status !== \App\Models\User::VERIFICATION_VERIFIED) {
            return response()->json([
                'success' => false,
                'message' => __('ui.rental_restricted_unverified'),
            ], 403);
        }

        $data    = $request->validated();
        $product = Product::where('status', Product::STATUS_ACTIVE)->findOrFail($data['product_id']);

        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end   = Carbon::parse($data['end_date'])->startOfDay();

        $totalDays = (int) $start->diffInDays($end) + 1; // inclusive both ends
        $totalPrice = $totalDays * (float) $product->price_per_day;

        $rental = RentalRequest::create([
            'borrower_id' => auth()->id(),
            'product_id'  => $product->id,
            'owner_id'    => $product->owner_id,
            'start_date'  => $start->toDateString(),
            'end_date'    => $end->toDateString(),
            'total_days'  => $totalDays,
            'total_price' => round($totalPrice, 2),
            'notes'       => $data['notes'] ?? null,
            'status'      => RentalRequest::STATUS_PENDING,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil diajukan.',
            'data'    => $rental->fresh(['product.primaryImage', 'owner']),
        ], 201);
    }

    /**
     * BATALKAN PEMINJAMAN — POST /peminjaman/{id}/batal (PRD section 16.3)
     *
     * Business rule (PRD section 14):
     * - HANYA bisa jika status masih 'pending'
     */
    public function cancel(string $id): JsonResponse
    {
        $rental = RentalRequest::where('borrower_id', auth()->id())->findOrFail($id);

        if ($rental->status !== RentalRequest::STATUS_PENDING) {
            return response()->json([
                'success' => false,
                'message' => 'Peminjaman hanya dapat dibatalkan saat status masih pending.',
            ], 422);
        }

        $rental->update(['status' => RentalRequest::STATUS_CANCELLED]);

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dibatalkan.',
            'data'    => $rental->fresh(),
        ]);
    }
}
