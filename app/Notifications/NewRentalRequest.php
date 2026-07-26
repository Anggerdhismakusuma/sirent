<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewRentalRequest extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly int $rentalId,
        public readonly string $productName,
        public readonly string $borrowerName,
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
        return [
            'rental_id'     => $this->rentalId,
            'product_name'  => $this->productName,
            'borrower_name' => $this->borrowerName,
            'message'       => __('ui.notif_new_rental_request', [
                'product'  => $this->productName,
                'borrower' => $this->borrowerName,
            ]),
        ];
    }
}
