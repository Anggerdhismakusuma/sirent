<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    /**
     * Scope: unread notifications only.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope: read notifications only.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Mark this notification as read.
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }

    /**
     * Return a human-readable label for the notification type.
     */
    public function typeLabel(): string
    {
        return match ($this->type) {
            'App\Notifications\RentalRequestStatusChanged' => __('ui.notif_rental_status'),
            'App\Notifications\PaymentStatusChanged'       => __('ui.notif_payment_status'),
            'App\Notifications\NewMessageReceived'         => __('ui.notif_new_message'),
            'App\Notifications\NewRentalRequest'           => __('ui.notif_new_rental'),
            'App\Notifications\DisputeStatusChanged'       => __('ui.notif_dispute_status'),
            default => __('ui.notif_general'),
        };
    }

    /**
     * Return the icon class for the notification type.
     */
    public function iconClass(): string
    {
        return match ($this->type) {
            'App\Notifications\RentalRequestStatusChanged' => 'bi bi-box-arrow-in-right',
            'App\Notifications\PaymentStatusChanged'       => 'bi bi-credit-card',
            'App\Notifications\NewMessageReceived'         => 'bi bi-chat-dots',
            'App\Notifications\NewRentalRequest'           => 'bi bi-inbox',
            'App\Notifications\DisputeStatusChanged'       => 'bi bi-shield-exclamation',
            default => 'bi bi-bell',
        };
    }

    /**
     * Return a URL this notification should link to (if any).
     */
    public function linkUrl(): ?string
    {
        $data = $this->data;

        return match ($this->type) {
            'App\Notifications\RentalRequestStatusChanged' => isset($data['rental_id'])
                ? route('borrower.dashboard', ['tab' => 'activity'])
                : null,
            'App\Notifications\PaymentStatusChanged' => isset($data['rental_id'])
                ? route('borrower.dashboard', ['tab' => 'activity'])
                : null,
            'App\Notifications\NewRentalRequest' => isset($data['rental_id'])
                ? route('borrower.dashboard', ['tab' => 'incoming'])
                : null,
            'App\Notifications\DisputeStatusChanged' => isset($data['dispute_id'])
                ? route('borrower.dashboard', ['tab' => 'activity'])
                : null,
            'App\Notifications\NewMessageReceived' => isset($data['conversation_id'])
                ? route('chat.show', ['conversation' => $data['conversation_id']])
                : null,
            default => null,
        };
    }
}
