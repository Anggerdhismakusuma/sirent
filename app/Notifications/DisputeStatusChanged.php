<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DisputeStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly int $disputeId,
        public readonly string $productName,
        public readonly string $status, // 'submitted' | 'resolved' | 'rejected'
        public readonly ?string $resolution = null,
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
        $message = match ($this->status) {
            'submitted' => __('ui.notif_dispute_submitted', ['product' => $this->productName]),
            'resolved'  => __('ui.notif_dispute_resolved', ['product' => $this->productName]),
            'rejected'  => __('ui.notif_dispute_rejected', ['product' => $this->productName]),
            default     => __('ui.notif_dispute_updated', ['product' => $this->productName]),
        };

        return [
            'dispute_id'   => $this->disputeId,
            'product_name' => $this->productName,
            'status'       => $this->status,
            'resolution'   => $this->resolution,
            'message'      => $message,
        ];
    }
}
