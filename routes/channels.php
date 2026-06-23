<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

// ── Private channel: conversation.{id} ──
// Only participants (borrower or owner) can subscribe
Broadcast::channel('conversation.{id}', function ($user, $id) {
    $conversation = Conversation::find($id);

    if (!$conversation) {
        return false;
    }

    return (int) $user->id === (int) $conversation->borrower_id
        || (int) $user->id === (int) $conversation->owner_id;
});

// ── Private channel: user.{id} ──
// User-scoped channel for unread badge updates
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
