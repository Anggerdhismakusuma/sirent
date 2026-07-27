<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewMessageReceived extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly int $conversationId,
        public readonly string $senderName,
        public readonly string $preview,
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
            'conversation_id' => $this->conversationId,
            'sender_name'     => $this->senderName,
            'preview'         => $this->preview,
            'message'         => __('ui.notif_new_message_from', ['name' => $this->senderName]),
        ];
    }
}
