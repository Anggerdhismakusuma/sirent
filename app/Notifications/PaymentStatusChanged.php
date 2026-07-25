<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly int $rentalId,
        public readonly string $productName,
        public readonly string $paymentStatus, // 'paid' | 'failed' | 'expired'
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation for database storage.
     */
    public function toArray(object $notifiable): array
    {
        $message = match ($this->paymentStatus) {
            'paid'    => __('ui.notif_payment_paid', ['product' => $this->productName]),
            'failed'  => __('ui.notif_payment_failed', ['product' => $this->productName]),
            'expired' => __('ui.notif_payment_expired', ['product' => $this->productName]),
            default   => __('ui.notif_payment_updated', ['product' => $this->productName]),
        };

        return [
            'rental_id'      => $this->rentalId,
            'product_name'   => $this->productName,
            'payment_status' => $this->paymentStatus,
            'message'        => $message,
        ];
    }
}
