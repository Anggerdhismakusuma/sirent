{{-- SI-RENT Chat — F-CHT-01 s.d. F-CHT-05, Figma node 1860-3538 --}}
@extends('layouts.app')

@section('title', __('ui.chat_title'))

@php
// Pre-compute complex data untuk menghindari nested ternary di @json()
$chatConfig = [
    'conversations' => $conversations ?? [],
    'totalUnread' => $totalUnread ?? 0,
    'userId' => auth()->id(),
    'activeConversationId' => isset($conversation) ? $conversation->id : null,
    'initialMessages' => $messages ?? [],
    'initialOtherUser' => null,
    'initialConversation' => null,
];

if (isset($otherUser)) {
    $chatConfig['initialOtherUser'] = [
        'id' => $otherUser->id,
        'name' => $otherUser->name,
        'avatar' => $otherUser->avatar,
        'is_verified' => $otherUser->verification_status === App\Models\User::VERIFICATION_VERIFIED,
    ];
}

if (isset($conversation)) {
    $productData = null;
    if ($conversation->product) {
        $imagePath = $conversation->product->primaryImage?->image_path;
        $productData = [
            'title' => $conversation->product->title,
            'slug' => $conversation->product->slug,
            'price_per_day' => $conversation->product->price_per_day,
            'image_url' => $imagePath ? url('storage/' . $imagePath) : null,
        ];
    }
    $chatConfig['initialConversation'] = [
        'id' => $conversation->id,
        'product' => $productData,
    ];
}
@endphp

@section('content')
<div class="chat-container" x-data='chatApp(@json($chatConfig))'>

    {{-- ========== PANEL 1: Conversation List (391px) ========== --}}
    <div class="chat-panel-left" :class="{ 'd-none d-md-flex': activeConversationId }">
        {{-- Logo + Back --}}
        <div class="d-flex align-items-center gap-2 px-3 pt-3 pb-2">
            <a href="{{ url('/') }}" class="text-decoration-none d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo-sirent.svg') }}" alt="SI-RENT" width="52" height="43">
                <span class="font-logo text-primary" style="font-size:32px;">SI-RENT</span>
            </a>
        </div>

        {{-- Search Bar --}}
        <div class="px-3 pb-2">
            <div class="input-group">
                <span class="input-group-text bg-white" style="border-radius:5px 0 0 5px; border-color:#d4d4d4;">
                    <i class="bi bi-search" style="color:#888; font-size:12px;"></i>
                </span>
                <input type="text" x-model="searchQuery" placeholder="{{ __('ui.search_conversations') }}"
                       class="form-control border-start-0"
                       style="border-color:#d4d4d4; border-radius:0 5px 5px 0; font-family:'Mona Sans',sans-serif; font-size:12px;">
            </div>
        </div>

        {{-- Filter Tabs --}}
        <div class="d-flex gap-2 px-3 pb-3">
            <button class="btn btn-sm" :class="filterTab === 'all' ? 'btn-primary' : 'btn-light border'"
                    style="font-family:'Mona Sans',sans-serif; font-size:12px; border-radius:5px;"
                    :style="filterTab === 'all' ? 'background:#0031e1; border-color:#0031e1;' : 'border-color:#d4d4d4; color:#545353; background:#fff;'"
                    @click="filterTab = 'all'">{{ __('ui.all_chat') }}</button>
            <button class="btn btn-sm position-relative" :class="filterTab === 'unread' ? 'btn-primary' : 'btn-light border'"
                    style="font-family:'Mona Sans',sans-serif; font-size:12px; border-radius:5px;"
                    :style="filterTab === 'unread' ? 'background:#0031e1; border-color:#0031e1;' : 'border-color:#d4d4d4; color:#545353; background:#fff;'"
                    @click="filterTab = 'unread'">
                {{ __('ui.unread') }}
                <span x-show="unreadBadgeCount > 0 && filterTab !== 'unread'" class="badge rounded-pill ms-1"
                      style="background:#0031e1; font-size:10px;" x-text="unreadBadgeCount"></span>
            </button>
            <button class="btn btn-sm" :class="filterTab === 'favourite' ? 'btn-primary' : 'btn-light border'"
                    style="font-family:'Mona Sans',sans-serif; font-size:12px; border-radius:5px;"
                    :style="filterTab === 'favourite' ? 'background:#0031e1; border-color:#0031e1;' : 'border-color:#d4d4d4; color:#545353; background:#fff;'"
                    @click="filterTab = 'favourite'">{{ __('ui.favourite') }}</button>
        </div>

        {{-- Conversation List --}}
        <div class="chat-conversation-list" style="flex:1; overflow-y:auto;">
            {{-- Empty state --}}
            <template x-if="filteredConversations.length === 0">
                <div class="text-center py-5">
                    <i class="bi bi-chat-dots d-block mb-2" style="font-size:40px; color:#ccc;"></i>
                    <p class="text-muted" style="font-family:'Mona Sans',sans-serif; font-size:13px;">
                        {{ __('ui.no_conversations') }}
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary"
                       style="font-family:'Mona Sans',sans-serif; font-size:12px; border-radius:5px;">
                        {{ __('ui.find_products') }}
                    </a>
                </div>
            </template>

            {{-- Conversation rows --}}
            <template x-for="conv in filteredConversations" :key="conv.id">
                <div class="chat-conversation-row d-flex align-items-center gap-3 px-3 py-2"
                     style="cursor:pointer; transition:background 0.15s;"
                     :class="{ 'chat-conversation-active': conv.id === activeConversationId }"
                     :style="conv.id === activeConversationId ? 'background:#ecf2fd; border-left:2px solid #0031e1;' : 'border-left:2px solid transparent;'"
                     @click="selectConversation(conv.id)">
                    {{-- Avatar — image with @error fallback to initials --}}
                    <div class="flex-shrink-0 position-relative" style="width:60px; height:60px;">
                        <img :src="conv.other_user?.avatar ? '/storage/' + conv.other_user.avatar : ''"
                             x-on:error="$el.style.display='none'; $el.nextElementSibling.style.display=''"
                             class="rounded-circle object-fit-cover"
                             style="width:60px; height:60px;" alt="">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:60px; height:60px; font-size:20px; display:none;"
                             x-text="avatarInitial(conv.other_user?.name)"></div>
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold text-truncate"
                                  style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#000;"
                                  x-text="conv.other_user?.name || 'User'"></span>
                            <small style="font-family:'Mona Sans',sans-serif; font-size:12px; color:#888; flex-shrink:0;"
                                   x-text="conv.last_message_time ? formatTime(conv.last_message_time) : ''"></small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-1">
                            <small class="text-truncate"
                                   style="font-family:'Mona Sans',sans-serif; font-size:12px; color:#888; max-width:200px;"
                                   x-text="conv.last_message_preview || 'Mulai percakapan...'"></small>
                            <span x-show="conv.unread_count > 0"
                                  class="badge rounded-pill flex-shrink-0"
                                  style="background:#0031e1; font-size:10px; color:#fff; min-width:18px;"
                                  x-text="conv.unread_count"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ========== PANEL 2: Chat Messages (flex:1) ========== --}}
    <div class="chat-panel-center" :class="{ 'd-none d-md-flex': !activeConversationId }">
        {{-- Empty state: belum pilih conversation --}}
        <template x-if="!activeConversationId">
            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center px-4">
                <i class="bi bi-chat-dots d-block mb-3" style="font-size:64px; color:#ccc;"></i>
                <h5 class="fw-semibold" style="font-family:'Mona Sans',sans-serif; color:#545353;">
                    {{ __('ui.select_conversation') }}
                </h5>
                <p class="text-muted" style="font-family:'Mona Sans',sans-serif; font-size:13px; max-width:350px;">
                    {{ __('ui.select_conversation_desc') }}
                </p>
                <button class="btn btn-outline-primary d-md-none mt-2"
                        style="font-family:'Mona Sans',sans-serif; font-size:14px; border-radius:5px;"
                        @click="activeConversationId = null">
                    <i class="bi bi-arrow-left me-1"></i> {{ __('ui.view_conversations') }}
                </button>
            </div>
        </template>

        {{-- Active conversation --}}
        <template x-if="activeConversationId">
            <div class="d-flex flex-column h-100">
                {{-- Header --}}
                <div class="chat-header d-flex align-items-center gap-3 px-4"
                     style="height:81px; border-bottom:1px solid #d4d4d4; flex-shrink:0;">
                    {{-- Mobile back button --}}
                    <button class="btn btn-link text-primary p-0 d-md-none me-1" @click="activeConversationId = null">
                        <i class="bi bi-chevron-left" style="font-size:20px;"></i>
                    </button>
                    {{-- Avatar --}}
                    <div class="flex-shrink-0 position-relative" style="width:60px; height:60px;">
                        <img :src="otherUser?.avatar ? '/storage/' + otherUser.avatar : ''"
                             x-on:error="$el.style.display='none'; $el.nextElementSibling.style.display=''"
                             class="rounded-circle object-fit-cover"
                             style="width:60px; height:60px;" alt="">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width:60px; height:60px; font-size:20px; display:none;"
                             x-text="avatarInitial(otherUser?.name)"></div>
                    </div>
                    {{-- Info --}}
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:16px; color:#000;"
                                  x-text="otherUser?.name || 'User'"></span>
                            <span x-show="otherUser?.is_verified"
                                  class="d-inline-flex align-items-center gap-1 px-2 py-0 rounded-pill"
                                  style="background:#ecf2fd; font-family:'Mona Sans',sans-serif; font-size:11px; color:#204be5; border:1px solid #c2d8ff;">
                                <i class="bi bi-patch-check-fill" style="font-size:11px;"></i> Verified
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle" style="width:8px; height:8px; background:#0de400;"></div>
                            <span style="font-family:'Mona Sans',sans-serif; font-size:12px; color:#545353;">{{ __('ui.online') }}</span>
                        </div>
                    </div>
                    {{-- Details toggle (desktop) --}}
                    <button class="btn btn-link text-secondary ms-auto d-none d-md-block p-0"
                            @click="showDetails = !showDetails"
                            title="Conversation details">
                        <i class="bi bi-info-circle" style="font-size:18px;"></i>
                    </button>
                </div>

                {{-- Messages --}}
                <div class="chat-messages-container" x-ref="messageContainer" style="flex:1; overflow-y:auto; padding:16px;">
                    {{-- Date divider --}}
                    <template x-if="messages.length > 0">
                        <div class="text-center mb-3">
                            <span class="px-3 py-1 rounded-pill"
                                  style="background:#f0f0f0; font-family:'Mona Sans',sans-serif; font-size:11px; color:#545353;"
                                  x-text="messages[0]?.date_formatted || ''"></span>
                        </div>
                    </template>

                    {{-- Message list --}}
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="d-flex mb-3" :class="msg.is_mine ? 'justify-content-end' : 'justify-content-start'">
                            {{-- Other user avatar --}}
                            <div x-show="!msg.is_mine" class="flex-shrink-0 me-2 position-relative" style="width:37px; height:37px;">
                                <img :src="otherUser?.avatar ? '/storage/' + otherUser.avatar : ''"
                                     x-on:error="$el.style.display='none'; $el.nextElementSibling.style.display=''"
                                     class="rounded-circle object-fit-cover"
                                     style="width:37px; height:37px;" alt="">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                                     style="width:37px; height:37px; font-size:14px; display:none;"
                                     x-text="avatarInitial(otherUser?.name)"></div>
                            </div>

                            <div class="d-flex flex-column" :class="msg.is_mine ? 'align-items-end' : 'align-items-start'" style="max-width:70%;">
                                {{-- Sender name (other user only) --}}
                                <small x-show="!msg.is_mine" class="mb-1 fw-semibold"
                                       style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#545353;"
                                       x-text="msg.sender_name"></small>

                                {{-- Bubble --}}
                                <div :style="msg.is_mine
                                    ? 'background:#0031e1; border-radius:5px 5px 0 5px; color:#fff; padding:8px 14px; font-family:\'Mona Sans\',sans-serif; font-size:14px; word-wrap:break-word;'
                                    : 'background:#ebebeb; border-radius:0 10px 10px 10px; color:#000; padding:8px 14px; font-family:\'Mona Sans\',sans-serif; font-size:14px; word-wrap:break-word;'">
                                    <p x-show="msg.body" class="mb-0" x-text="msg.body"></p>
                                    <img x-show="msg.attachment_url" :src="msg.attachment_url" alt="attachment"
                                         class="img-fluid rounded-2 mt-1" style="max-height:200px; cursor:pointer;"
                                         @click="window.open($el.src, '_blank')">
                                </div>

                                {{-- Meta --}}
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span style="font-family:'Mona Sans',sans-serif; font-size:10px;"
                                          :style="msg.is_mine ? 'color:rgba(0,0,0,0.5);' : 'color:#888;'"
                                          x-text="msg.time_formatted"></span>
                                    <span x-show="msg.is_sending" class="spinner-border spinner-border-sm"
                                          style="width:10px; height:10px;" role="status"></span>
                                    <i x-show="msg.is_mine && !msg.is_sending"
                                       :class="msg.is_read ? 'bi bi-check-all' : 'bi bi-check'"
                                       :style="msg.is_read ? 'font-size:12px; color:#0031e1;' : 'font-size:12px; color:#888;'"></i>
                                    <button x-show="msg.is_error" class="btn btn-link text-danger p-0"
                                            style="font-size:10px;" @click="retryMessage(msg)" title="Retry">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Input Area --}}
                <div class="chat-input-area px-3 py-2" style="border-top:1px solid #e0e0e0; flex-shrink:0;">
                    <form @submit.prevent="sendMessage" class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-link text-secondary p-0" @click="$refs.attachmentInput.click()"
                                style="font-size:18px;" title="Lampirkan foto">
                            <i class="bi bi-paperclip"></i>
                        </button>
                        <input type="file" x-ref="attachmentInput" class="d-none"
                               accept="image/jpeg,image/png,image/jpg"
                               @change="handleAttachmentSelect">

                        <input type="text" x-model="messageBody"
                               class="form-control border-0 shadow-none flex-grow-1"
                               placeholder="{{ __('ui.type_message') }}"
                               style="font-family:'Mona Sans',sans-serif; font-size:12px; background:transparent;"
                               @keydown.enter="sendMessage">

                        <button type="submit" class="btn d-flex align-items-center justify-content-center border-0 flex-shrink-0"
                                :disabled="(!messageBody || !messageBody.trim()) && !attachment"
                                :style="((messageBody && messageBody.trim()) || attachment)
                                    ? 'background:#0031e1; color:#fff; width:31px; height:31px; border-radius:5px;'
                                    : 'background:#ccc; color:#fff; width:31px; height:31px; border-radius:5px;'">
                            <i class="bi bi-send" style="font-size:14px;"></i>
                        </button>
                    </form>
                </div>
            </div>
        </template>
    </div>

    {{-- ========== PANEL 3: Details Sidebar (413px, toggleable) ========== --}}
    <div class="chat-panel-right" x-show="showDetails && activeConversationId" x-cloak
         style="overflow-y:auto; flex-shrink:0;">
        <div class="p-4">
            <h6 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:16px; color:#000;">
                {{ __('ui.conversation_details') }}
            </h6>

            {{-- Profile --}}
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="flex-shrink-0 position-relative" style="width:60px; height:60px;">
                    <img :src="otherUser?.avatar ? '/storage/' + otherUser.avatar : ''"
                         x-on:error="$el.style.display='none'; $el.nextElementSibling.style.display=''"
                         class="rounded-circle object-fit-cover"
                         style="width:60px; height:60px;" alt="">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                         style="width:60px; height:60px; font-size:20px; display:none;"
                         x-text="avatarInitial(otherUser?.name)"></div>
                </div>
                <div>
                    <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#000;"
                         x-text="otherUser?.name || 'User'"></div>
                    <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#9092a1;">
                        <span>{{ __('ui.respon_rate_label') }}</span>
                    </div>
                </div>
            </div>

            <button class="btn btn-outline-primary w-100 mb-4"
                    style="font-family:'Mona Sans',sans-serif; font-size:13px; font-weight:600; border-radius:5px; border-color:#3058e6; color:#204be5;"
                    @click="viewProfile">
                {{ __('ui.view_profile') }}
            </button>

            {{-- Renting Details (jika ada product) --}}
            <template x-if="currentConversation?.product">
                <div class="mb-4">
                    <h6 class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:16px; color:#000;">
                        {{ __('ui.renting_details') }}
                    </h6>
                    <div class="p-3 rounded-3" style="border:1px solid #d4d4d4; border-radius:10px;">
                        <div class="d-flex gap-3 align-items-center">
                            <div class="flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center"
                                 style="width:82px; height:82px; background:#dde7fc; border-radius:20px;">
                                <img x-show="currentConversation.product.image_url"
                                     :src="currentConversation.product.image_url"
                                     class="rounded-3 object-fit-cover"
                                     style="width:82px; height:82px; border-radius:20px;" alt="">
                                <i x-show="!currentConversation.product.image_url" class="bi bi-box"
                                   style="font-size:24px; color:#0031e1;"></i>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1" style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#000;"
                                     x-text="currentConversation.product.title"></div>
                                <div class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:15px; color:#0031e1;">
                                    <span x-text="'Rp ' + formatPrice(currentConversation.product.price_per_day)"></span>
                                    <span style="font-size:12px; color:#000; font-weight:400;"> / day</span>
                                </div>
                            </div>
                        </div>
                        <a :href="'/produk/' + currentConversation.product.slug"
                           class="d-block mt-2 text-decoration-none"
                           style="font-family:'Mona Sans',sans-serif; font-size:13px; font-weight:600; color:#204be5;">
                            {{ __('ui.view_details') }} <i class="bi bi-arrow-right ms-1" style="font-size:10px;"></i>
                        </a>
                    </div>
                </div>
            </template>

            {{-- Quick Actions --}}
            <div class="mb-4">
                <h6 class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:16px; color:#000;">
                    {{ __('ui.shortcuts') }}
                </h6>
                <div class="d-flex flex-column gap-2">
                    <a href="#" class="text-decoration-none d-flex align-items-center gap-2"
                       style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#888; font-weight:500;">
                        <i class="bi bi-trash" style="font-size:16px;"></i> {{ __('ui.delete_chat') }}
                    </a>
                    <a href="#" class="text-decoration-none d-flex align-items-center gap-2"
                       style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#888; font-weight:500;">
                        <i class="bi bi-flag" style="font-size:16px;"></i> {{ __('ui.report') }}
                    </a>
                    <a href="#" class="text-decoration-none d-flex align-items-center gap-2"
                       style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#ff0000; font-weight:500;">
                        <i class="bi bi-slash-circle" style="font-size:16px;"></i> {{ __('ui.block') }}
                    </a>
                </div>
            </div>

            {{-- Security Notice --}}
            <div class="p-3 rounded-3 d-flex gap-3 align-items-start"
                 style="background:#ecf2fd; border-radius:10px;">
                <i class="bi bi-shield-lock" style="font-size:24px; color:#0031e1;"></i>
                <div>
                    <div class="fw-semibold mb-1" style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#001d83;">
                        {{ __('ui.secure_encrypted') }}
                    </div>
                    <p class="mb-0" style="font-family:'Mona Sans',sans-serif; font-size:12px; color:#000;">
                        {{ __('ui.secure_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function chatApp(config) {
    return {
        // ── State ──
        conversations: config.conversations || [],
        totalUnread: config.totalUnread || 0,
        userId: config.userId,
        activeConversationId: config.activeConversationId || null,
        messages: config.initialMessages || [],
        messagesLoaded: !!(config.activeConversationId && config.initialConversation),
        otherUser: config.initialOtherUser || null,
        currentConversation: config.initialConversation || null,
        showDetails: false,
        messageBody: '',
        filterTab: 'all',
        searchQuery: '',
        attachment: null,
        attachmentPreview: null,

        // ── Computed ──
        get filteredConversations() {
            let list = this.conversations.slice();
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(function (c) {
                    return c.other_user && c.other_user.name &&
                        c.other_user.name.toLowerCase().indexOf(q) !== -1;
                });
            }
            if (this.filterTab === 'unread') {
                list = list.filter(function (c) {
                    return c.unread_count > 0;
                });
            }
            list.sort(function (a, b) {
                var aTime = a.last_message_time || a.last_message_at || a.created_at;
                var bTime = b.last_message_time || b.last_message_at || b.created_at;
                return new Date(bTime) - new Date(aTime);
            });
            return list;
        },

        get unreadBadgeCount() {
            var self = this;
            return this.conversations.reduce(function (sum, c) {
                return sum + (c.unread_count || 0);
            }, 0);
        },

        // ── Helpers ──
        avatarInitial: function (name) {
            if (!name) return 'U';
            return name.charAt(0).toUpperCase();
        },

        formatTime: function (dateStr) {
            if (!dateStr) return '';
            var d = new Date(dateStr);
            var now = new Date();
            var diffDays = Math.floor((now - d) / (1000 * 60 * 60 * 24));
            if (diffDays === 0) {
                return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            } else if (diffDays === 1) {
                return 'Yesterday';
            } else {
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            }
        },

        formatPrice: function (price) {
            return new Intl.NumberFormat('id-ID').format(price);
        },

        // ── Methods ──
        selectConversation: function (convId) {
            if (this.activeConversationId === convId) return;
            this.activeConversationId = convId;
            this.showDetails = false;
            this.messages = [];
            this.messagesLoaded = false;
            this.otherUser = null;
            this.currentConversation = null;
            this.fetchMessages(convId);
        },

        fetchMessages: async function (convId) {
            var self = this;
            try {
                var res = await fetch('/pesan/' + convId, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!res.ok) {
                    if (res.status === 403) {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: '{{ __('ui.access_denied_chat') }}', confirmButtonColor: '#0031e1' });
                        self.activeConversationId = null;
                        return;
                    }
                    throw new Error('Failed to fetch messages');
                }
                var data = await res.json();
                self.messages = data.messages || [];
                self.messagesLoaded = true;
                self.otherUser = data.other_user;
                self.currentConversation = data.conversation;
                // Update conversation in list
                var idx = self.conversations.findIndex(function (c) { return c.id === convId; });
                if (idx >= 0) {
                    self.conversations[idx].unread_count = 0;
                    self.updateNavbarBadge();
                }
                self.$nextTick(function () { self.scrollToBottom(); });
                self.subscribeToConversation(convId);
            } catch (e) {
                console.error('Failed to load messages:', e);
                self.messages = [];
                self.messagesLoaded = true;
            }
        },

        sendMessage: async function () {
            var self = this;
            var body = (this.messageBody || '').trim();
            if (!body && !this.attachment) return;
            if (!this.activeConversationId) return;

            var formData = new FormData();
            if (body) formData.append('body', body);
            if (this.attachment) {
                formData.append('attachment', this.attachment);
            }

            // Optimistic add
            var tempId = 'temp-' + Date.now();
            var optimisticMsg = {
                id: tempId,
                sender_id: this.userId,
                body: body || null,
                attachment: null,
                attachment_url: this.attachmentPreview,
                is_read: false,
                created_at: new Date().toISOString(),
                time_formatted: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                date_formatted: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                is_mine: true,
                is_sending: true,
                sender_name: '{{ __('ui.you') }}',
            };
            this.messages.push(optimisticMsg);
            var bodySnapshot = body;
            this.messageBody = '';
            this.clearAttachment();
            this.$nextTick(function () { self.scrollToBottom(); });

            try {
                var csrfToken = document.querySelector('meta[name="csrf-token"]');
                var res = await fetch('/pesan/' + this.activeConversationId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                var data = await res.json();

                if (res.ok && data.success) {
                    var idx = this.messages.findIndex(function (m) { return m.id === tempId; });
                    if (idx >= 0) {
                        this.messages[idx] = Object.assign({}, data.message, { is_sending: false });
                    }
                    this.updateConversationInList(this.activeConversationId, bodySnapshot || '[Gambar]');
                } else {
                    var idx = this.messages.findIndex(function (m) { return m.id === tempId; });
                    if (idx >= 0) {
                        this.messages[idx].is_error = true;
                        this.messages[idx].is_sending = false;
                    }
                    if (data.errors) {
                        var firstKey = Object.keys(data.errors)[0];
                        var firstError = (data.errors[firstKey] && data.errors[firstKey][0]) || '{{ __('ui.send_failed') }}';
                        Swal.fire({ icon: 'error', title: 'Oops...', text: firstError, confirmButtonColor: '#0031e1' });
                    }
                }
            } catch (e) {
                var idx = this.messages.findIndex(function (m) { return m.id === tempId; });
                if (idx >= 0) {
                    this.messages[idx].is_error = true;
                    this.messages[idx].is_sending = false;
                }
            }
        },

        retryMessage: function (msg) {
            this.messageBody = msg.body || '';
            this.messages = this.messages.filter(function (m) { return m.id !== msg.id; });
        },

        handleAttachmentSelect: function (e) {
            var file = e.target.files ? e.target.files[0] : null;
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire({ icon: 'warning', title: 'Oops...', text: '{{ __('ui.file_too_large') }}', confirmButtonColor: '#0031e1' });
                return;
            }
            if (['image/jpeg', 'image/png', 'image/jpg'].indexOf(file.type) === -1) {
                Swal.fire({ icon: 'warning', title: 'Oops...', text: '{{ __('ui.file_type_invalid') }}', confirmButtonColor: '#0031e1' });
                return;
            }
            this.attachment = file;
            this.attachmentPreview = URL.createObjectURL(file);
            e.target.value = '';
        },

        clearAttachment: function () {
            if (this.attachmentPreview) URL.revokeObjectURL(this.attachmentPreview);
            this.attachment = null;
            this.attachmentPreview = null;
        },

        scrollToBottom: function () {
            var el = this.$refs.messageContainer;
            if (el) {
                el.scrollTop = el.scrollHeight;
            }
        },

        updateConversationInList: function (convId, preview) {
            var conv = this.conversations.find(function (c) { return c.id === convId; });
            if (conv) {
                conv.last_message_preview = (preview || '').substring(0, 50) || '{{ __('ui.image_attachment') }}';
                conv.last_message_time = new Date().toISOString();
            }
        },

        // ── Real-time via Echo ──
        subscribeToConversation: function (convId) {
            var self = this;
            if (this._echoChannel) {
                if (window.Echo) {
                    window.Echo.leave('conversation.' + this._echoChannel);
                }
            }
            this._echoChannel = convId;

            if (window.Echo) {
                window.Echo.private('conversation.' + convId)
                    .listen('.message.sent', function (data) {
                        if (data.sender_id === self.userId) return;

                        self.messages.push({
                            id: data.id,
                            sender_id: data.sender_id,
                            body: data.body,
                            attachment: data.attachment,
                            attachment_url: data.attachment_url,
                            is_read: data.is_read,
                            created_at: data.created_at,
                            time_formatted: data.time_formatted,
                            date_formatted: data.date_formatted,
                            is_mine: false,
                            is_sending: false,
                            sender_name: data.sender_name,
                        });
                        // Mark as read
                        fetch('/pesan/' + convId, {
                            method: 'GET',
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        self.$nextTick(function () { self.scrollToBottom(); });
                    });
            }
        },

        updateNavbarBadge: function () {
            var badge = document.getElementById('chat-unread-count');
            if (badge) {
                var total = this.unreadBadgeCount;
                badge.textContent = total > 99 ? '99+' : total;
                badge.hidden = total === 0;
            }
        },

        viewProfile: function () {
            if (this.otherUser && this.otherUser.id) {
                window.location.href = '/toko/' + this.otherUser.id;
            }
        },

        init: function () {
            var self = this;
            if (this.activeConversationId) {
                this.subscribeToConversation(this.activeConversationId);
            }

            window.addEventListener('auth-changed', function () {
                location.reload();
            });

            this.$nextTick(function () { self.updateNavbarBadge(); });

            if (window.Echo && window.AuthUser) {
                window.Echo.private('user.' + window.AuthUser.id)
                    .listen('.message.sent', function (data) {
                        var conv = self.conversations.find(function (c) { return c.id === data.conversation_id; });
                        if (conv && conv.id !== self.activeConversationId) {
                            conv.unread_count = (conv.unread_count || 0) + 1;
                            conv.last_message_preview = (data.body || '').substring(0, 50) || '{{ __('ui.image_attachment') }}';
                            conv.last_message_time = data.created_at;
                        }
                        self.updateNavbarBadge();
                    });
            }
        },
    };
}
</script>
@endpush
