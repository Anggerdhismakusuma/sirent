<?php

namespace App\Services;

use App\Models\RentalRequest;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');

        // Optional: override notification URL per-transaction if needed
        // Config::$overrideNotifUrl = route('midtrans.webhook');
    }

    /**
     * Generate a Midtrans Snap token for the given rental request.
     *
     * Generates a unique order_ref, constructs item_details and transaction_details,
     * calls Midtrans Snap API, and persists the order_ref + snap_token on the rental.
     *
     * @param  RentalRequest  $rentalRequest
     * @return string         The Snap token to be used by snap.js on the frontend.
     *
     * @throws \Exception     When the Midtrans API call fails.
     */
    public function createSnapToken(RentalRequest $rentalRequest): string
    {
        $product  = $rentalRequest->product;
        $borrower = $rentalRequest->borrower;

        // ── Generate a canonical, unique order_ref ──
        $orderRef = sprintf(
            'SIRENT-%s-%d',
            strtoupper(substr(uniqid(), -8)),
            $rentalRequest->id
        );

        // ── Item details for Midtrans ──
        //     Prices must be integers (IDR), so we round the already-rounded
        //     total_price (stored as decimal) and the fixed service fee.
        $rentalItemPrice = (int) round((float) $rentalRequest->total_price);
        $serviceFee      = 2000; // fixed service fee in IDR

        $itemDetails = [
            [
                'id'       => 'RENT-' . $rentalRequest->id,
                'price'    => $rentalItemPrice,
                'quantity' => 1,
                'name'     => 'Sewa: ' . mb_substr($product->title, 0, 50),
            ],
            [
                'id'       => 'SVC-' . $rentalRequest->id,
                'price'    => $serviceFee,
                'quantity' => 1,
                'name'     => 'Biaya Layanan',
            ],
        ];

        $grossAmount = $rentalItemPrice + $serviceFee;

        // ── Build Snap transaction params ──
        $params = [
            'transaction_details' => [
                'order_id'     => $orderRef,
                'gross_amount' => $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => $borrower->name,
                'email'      => $borrower->email,
                'phone'      => $borrower->phone ?? '',
            ],
            'callbacks' => [
                'finish' => route('borrower.dashboard', ['tab' => 'activity'], false),
            ],
        ];

        // ── Hit Midtrans Snap API ──
        $snapToken = Snap::getSnapToken($params);

        // ── Persist the order_ref and snap_token ──
        $rentalRequest->update([
            'order_ref'  => $orderRef,
            'snap_token' => $snapToken,
        ]);

        return $snapToken;
    }

    /**
     * Verify the SHA512 signature of an incoming Midtrans webhook payload.
     *
     * Midtrans signs every notification with:
     *   SHA512(order_id . status_code . gross_amount . server_key)
     *
     * This method recomputes the hash and compares it against the
     * `signature_key` sent in the payload to prevent spoofed requests.
     *
     * @param  array  $payload  The raw notification payload (decoded JSON).
     * @return bool             True if the signature matches.
     */
    public function verifySignature(array $payload): bool
    {
        $orderId      = $payload['order_id'] ?? '';
        $statusCode   = (string) ($payload['status_code'] ?? '');
        $grossAmount  = (string) ($payload['gross_amount'] ?? '');
        $serverKey    = config('midtrans.server_key');

        $signatureInput = $orderId . $statusCode . $grossAmount . $serverKey;
        $computedHash   = hash('sha512', $signatureInput);
        $receivedHash   = $payload['signature_key'] ?? '';

        return hash_equals($computedHash, $receivedHash);
    }

    /**
     * Create a Midtrans Notification instance from the current request.
     *
     * The Midtrans PHP SDK's Notification class reads from php://input,
     * which is the raw POST body sent by Midtrans.
     *
     * @return Notification
     */
    public function verifyNotification(): Notification
    {
        return new Notification();
    }
}
