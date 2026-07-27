<?php

namespace App\Http\Controllers;

use App\Models\RentalRequest;
use App\Notifications\PaymentStatusChanged;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(
        private MidtransService $midtransService
    ) {}

    /**
     * Handle Midtrans payment notification (webhook).
     *
     * POST /api/midtrans/webhook
     *
     * Hardened flow:
     * 1. Verify SHA512 signature — abort 403 on mismatch (anti-spoofing).
     * 2. Parse notification via Midtrans SDK.
     * 3. Look up RentalRequest by order_ref (canonical lookup, no string parsing).
     * 4. Map transaction_status + fraud_status to internal payment states.
     * 5. Always return 200 OK to prevent Midtrans from retrying.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        // ── 1. Mandatory SHA512 signature verification ──
        if (! $this->midtransService->verifySignature($payload)) {
            Log::warning('Midtrans webhook: signature verification failed', [
                'order_id' => $payload['order_id'] ?? 'unknown',
                'remote'   => $request->ip(),
            ]);

            return response('Signature mismatch', 403);
        }

        // ── 2. Parse the notification ──
        try {
            $notification = $this->midtransService->verifyNotification();
        } catch (\Exception $e) {
            Log::error('Midtrans webhook: failed to parse notification', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);

            return response('OK'); // 200 — stop retries
        }

        $transactionStatus = $notification->transaction_status;
        $fraudStatus       = $notification->fraud_status ?? null;
        $orderId           = $notification->order_id;

        // ── 3. Look up by order_ref (bulletproof — no string parsing) ──
        $rentalRequest = RentalRequest::where('order_ref', $orderId)->first();

        if (! $rentalRequest) {
            Log::warning('Midtrans webhook: rental not found by order_ref', [
                'order_id' => $orderId,
            ]);

            return response('OK'); // 200 — stop retries
        }

        // Store the Midtrans transaction_id for audit trail
        $rentalRequest->transaction_id = $notification->transaction_id;

        // ── 4. Map statuses to internal payment_status ──
        match (true) {
            // Credit card: capture + accept fraud check
            $transactionStatus === 'capture' && $fraudStatus === 'accept'
                => $this->settlePayment($rentalRequest),

            // Credit card: capture + challenge (manual review needed)
            $transactionStatus === 'capture' && $fraudStatus === 'challenge'
                => $this->markPending($rentalRequest),

            // All other payment methods: settlement = paid
            $transactionStatus === 'settlement'
                => $this->settlePayment($rentalRequest),

            // Payment is still pending (bank transfer waiting for user, etc.)
            $transactionStatus === 'pending'
                => $this->markPending($rentalRequest),

            // Payment denied or cancelled by user/system
            in_array($transactionStatus, ['deny', 'cancel'], true)
                => $this->failPayment($rentalRequest),

            // Payment link/QR expired
            $transactionStatus === 'expire'
                => $this->expirePayment($rentalRequest),

            // Refund (full or partial)
            in_array($transactionStatus, ['refund', 'partial_refund'], true)
                => $this->refundPayment($rentalRequest),

            // Unknown status — log but still return 200
            default => Log::info('Midtrans webhook: unhandled transaction status', [
                'order_id'           => $orderId,
                'transaction_status' => $transactionStatus,
                'fraud_status'       => $fraudStatus,
            ]),
        };

        return response('OK');
    }

    /**
     * Mark payment as successfully settled.
     */
    private function settlePayment(RentalRequest $rental): void
    {
        $rental->update([
            'payment_status' => RentalRequest::PAYMENT_PAID,
            'paid_at'        => now(),
        ]);

        // Notify borrower
        $rental->load('product');
        $rental->borrower?->notify(new PaymentStatusChanged(
            rentalId: $rental->id,
            productName: $rental->product->title ?? 'Produk',
            paymentStatus: 'paid',
        ));

        Log::info('Midtrans webhook: payment settled', [
            'rental_id'       => $rental->id,
            'order_ref'       => $rental->order_ref,
            'transaction_id'  => $rental->transaction_id,
        ]);
    }

    /**
     * Mark payment as pending (awaiting user action).
     */
    private function markPending(RentalRequest $rental): void
    {
        $rental->update([
            'payment_status' => RentalRequest::PAYMENT_PENDING,
        ]);
    }

    /**
     * Mark payment as failed.
     */
    private function failPayment(RentalRequest $rental): void
    {
        $rental->update([
            'payment_status' => RentalRequest::PAYMENT_FAILED,
        ]);

        // Notify borrower
        $rental->load('product');
        $rental->borrower?->notify(new PaymentStatusChanged(
            rentalId: $rental->id,
            productName: $rental->product->title ?? 'Produk',
            paymentStatus: 'failed',
        ));

        Log::info('Midtrans webhook: payment failed', [
            'rental_id'      => $rental->id,
            'order_ref'      => $rental->order_ref,
        ]);
    }

    /**
     * Mark payment as expired and cancel the rental request.
     */
    private function expirePayment(RentalRequest $rental): void
    {
        $rental->update([
            'payment_status' => RentalRequest::PAYMENT_EXPIRED,
            'status'         => RentalRequest::STATUS_CANCELLED,
        ]);

        // Notify borrower
        $rental->load('product');
        $rental->borrower?->notify(new PaymentStatusChanged(
            rentalId: $rental->id,
            productName: $rental->product->title ?? 'Produk',
            paymentStatus: 'expired',
        ));

        Log::info('Midtrans webhook: payment expired, rental cancelled', [
            'rental_id' => $rental->id,
            'order_ref' => $rental->order_ref,
        ]);
    }

    /**
     * Mark payment as refunded.
     */
    private function refundPayment(RentalRequest $rental): void
    {
        $rental->update([
            'payment_status' => RentalRequest::PAYMENT_REFUNDED,
        ]);

        Log::info('Midtrans webhook: payment refunded', [
            'rental_id'      => $rental->id,
            'order_ref'      => $rental->order_ref,
        ]);
    }
}
