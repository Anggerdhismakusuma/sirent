<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class RentalRequestStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly int $rentalId,
        public readonly string $productName,
        public readonly string $status, // 'approved' | 'rejected'
        public readonly ?string $reason = null,
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
        $message = $this->status === 'approved'
            ? __('ui.notif_rental_approved', ['product' => $this->productName])
            : __('ui.notif_rental_rejected', ['product' => $this->productName]);

        return [
            'rental_id'    => $this->rentalId,
            'product_name' => $this->productName,
            'status'       => $this->status,
            'reason'       => $this->reason,
            'message'      => $message,
        ];
    }
}
