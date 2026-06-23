<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\ChatMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    /**
     * F-CHT-04: Daftar percakapan pengguna.
     * GET /pesan
     */
    public function index(): View
    {
        $userId = auth()->id();
        $conversations = $this->getConversationList($userId);
        $totalUnread = $conversations->sum('unread_count');

        return view('chat.index', compact('conversations', 'totalUnread'));
    }

    /**
     * F-CHT-04: Membuka satu percakapan.
     * GET /pesan/{conversation}
     */
    public function show(Conversation $conversation): View|JsonResponse
    {
        $userId = auth()->id();

        // Authorization: hanya participant yang boleh akses
        if ($conversation->borrower_id !== $userId && $conversation->owner_id !== $userId) {
            abort(403, 'Anda bukan participant dalam percakapan ini.');
        }

        // Tandai semua pesan dari lawan sebagai sudah dibaca
        $conversation->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Load data
        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at')
            ->get()
            ->map(function (Message $m) use ($userId) {
                return [
                    'id' => $m->id,
                    'sender_id' => $m->sender_id,
                    'body' => $m->body,
                    'attachment' => $m->attachment,
                    'attachment_url' => $m->attachment ? url('storage/' . $m->attachment) : null,
                    'is_read' => $m->is_read,
                    'created_at' => $m->created_at->toISOString(),
                    'time_formatted' => $m->created_at->format('H:i'),
                    'date_formatted' => $m->created_at->format('d M Y'),
                    'is_mine' => $m->sender_id === $userId,
                    'sender_name' => $m->sender->name,
                    'sender_avatar' => $m->sender->avatar,
                ];
            });

        $otherUser = $conversation->borrower_id === $userId
            ? $conversation->owner
            : $conversation->borrower;

        $conversation->load('product.primaryImage');

        // Jika AJAX, return JSON
        if (request()->wantsJson()) {
            return response()->json([
                'conversation' => [
                    'id' => $conversation->id,
                    'product' => $conversation->product ? [
                        'title' => $conversation->product->title,
                        'slug' => $conversation->product->slug,
                        'price_per_day' => $conversation->product->price_per_day,
                        'image_url' => $conversation->product->primaryImage?->image_path
                            ? url('storage/' . $conversation->product->primaryImage->image_path)
                            : null,
                    ] : null,
                ],
                'messages' => $messages,
                'other_user' => [
                    'id' => $otherUser->id,
                    'name' => $otherUser->name,
                    'avatar' => $otherUser->avatar,
                    'is_verified' => $otherUser->verification_status === \App\Models\User::VERIFICATION_VERIFIED,
                ],
                'user_id' => $userId,
            ]);
        }

        // Full page load: sertakan juga daftar conversation untuk panel kiri
        $conversations = $this->getConversationList($userId);
        $totalUnread = $conversations->sum('unread_count');

        return view('chat.index', compact('conversations', 'totalUnread', 'conversation', 'messages', 'otherUser'));
    }

    /**
     * F-CHT-02: Mengirim pesan baru.
     * POST /pesan/{conversation}
     */
    public function send(ChatMessageRequest $request, Conversation $conversation): JsonResponse
    {
        $userId = auth()->id();

        // Authorization
        if ($conversation->borrower_id !== $userId && $conversation->owner_id !== $userId) {
            abort(403, 'Anda bukan participant dalam percakapan ini.');
        }

        $data = $request->validated();
        $attachmentPath = null;

        // Simpan attachment jika ada
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat-attachments', 'public');
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'body' => $data['body'] ?? null,
            'attachment' => $attachmentPath,
            'is_read' => false,
        ]);

        // Update timestamp conversation
        $conversation->update(['last_message_at' => now()]);

        // Broadcast event (ke participant lain, bukan ke pengirim)
        broadcast(new MessageSent($message))->toOthers();

        $message->load('sender');

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'body' => $message->body,
                'attachment' => $message->attachment,
                'attachment_url' => $message->attachment
                    ? url('storage/' . $message->attachment)
                    : null,
                'is_read' => $message->is_read,
                'created_at' => $message->created_at->toISOString(),
                'time_formatted' => $message->created_at->format('H:i'),
                'date_formatted' => $message->created_at->format('d M Y'),
                'is_mine' => true,
                'sender_name' => $message->sender->name,
            ],
        ], 201);
    }

    /**
     * F-CHT-01: Memulai percakapan baru dari halaman detail produk.
     * POST /pesan/mulai/{product}
     */
    public function start(Product $product): JsonResponse
    {
        $userId = auth()->id();

        // Tidak bisa chat dengan diri sendiri
        if ($product->owner_id === $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat memulai chat dengan diri sendiri.',
            ], 422);
        }

        // Cari conversation yang sudah ada, atau buat baru
        $conversation = Conversation::firstOrCreate(
            [
                'borrower_id' => $userId,
                'owner_id' => $product->owner_id,
                'product_id' => $product->id,
            ],
            [
                'last_message_at' => null,
            ]
        );

        return response()->json([
            'success' => true,
            'data' => [
                'conversation_id' => $conversation->id,
                'redirect_url' => route('chat.show', $conversation->id),
            ],
        ]);
    }

    /**
     * Helper: Total unread count untuk navbar badge.
     * GET /pesan/unread/count
     */
    public function unreadCount(): JsonResponse
    {
        $userId = auth()->id();

        $count = Conversation::where('borrower_id', $userId)
            ->orWhere('owner_id', $userId)
            ->get()
            ->sum(function (Conversation $conv) use ($userId) {
                return $conv->messages()
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count();
            });

        return response()->json(['count' => $count]);
    }

    /**
     * Build conversation list array untuk view.
     * Dipakai oleh index() dan show() supaya panel kiri selalu terisi.
     */
    private function getConversationList(int $userId): \Illuminate\Support\Collection
    {
        return Conversation::where('borrower_id', $userId)
            ->orWhere('owner_id', $userId)
            ->with(['borrower', 'owner', 'product.primaryImage'])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function (Conversation $conv) use ($userId): array {
                $otherUser = $conv->borrower_id === $userId
                    ? $conv->owner
                    : $conv->borrower;

                $lastMessage = $conv->messages()->latest()->first();

                $unreadCount = $conv->messages()
                    ->where('sender_id', '!=', $userId)
                    ->where('is_read', false)
                    ->count();

                // Plain array — agar @json() Blade menyertakan semua field
                return [
                    'id' => $conv->id,
                    'borrower_id' => $conv->borrower_id,
                    'owner_id' => $conv->owner_id,
                    'product_id' => $conv->product_id,
                    'last_message_at' => $conv->last_message_at,
                    'created_at' => $conv->created_at,
                    'other_user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'avatar' => $otherUser->avatar,
                    ],
                    'last_message_preview' => $lastMessage?->body
                        ? mb_substr($lastMessage->body, 0, 50)
                        : ($lastMessage?->attachment ? '[Gambar]' : null),
                    'last_message_time' => $lastMessage?->created_at?->toISOString(),
                    'unread_count' => $unreadCount,
                ];
            });
    }
}
