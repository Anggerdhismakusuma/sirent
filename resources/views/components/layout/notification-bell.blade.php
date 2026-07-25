{{-- SI-RENT Notification Bell & Dropdown --}}
<div x-data="notificationBell" class="position-relative">
    {{-- Bell Icon Button --}}
    <button @click="toggleDropdown($event)"
            class="btn btn-link text-primary p-0 position-relative me-2"
            title="{{ __('ui.notifications') }}"
            :aria-expanded="open">
        <i class="bi bi-bell fs-5"></i>
        {{-- Unread Badge --}}
        <span x-show="unreadCount > 0" x-cloak
              class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
              style="font-size:10px;"
              x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
    </button>

    {{-- Dropdown Panel --}}
    <div x-show="open" x-cloak
         @click.outside="open = false"
         @keydown.escape.window="open = false"
         class="position-absolute end-0 mt-2 bg-white rounded-3 shadow-lg border z-3"
         style="width:380px; max-height:480px; overflow:hidden; border-color: var(--border-default); top:100%;">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom" style="border-color: var(--border-light);">
            <span class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-primary);">
                {{ __('ui.notifications') }}
            </span>
            <button x-show="unreadCount > 0"
                    @click="markAllAsRead()"
                    class="btn btn-link text-decoration-none p-0"
                    style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light);">
                {{ __('ui.mark_all_read') }}
            </button>
        </div>

        {{-- Notification List --}}
        <div style="max-height:360px; overflow-y:auto;" id="notification-list">
            {{-- Loading Spinner --}}
            <div x-show="loading" class="text-center py-4">
                <div class="spinner-border text-primary" role="status" style="width:24px;height:24px;"></div>
            </div>

            {{-- Empty State --}}
            <div x-show="!loading && notifications.length === 0" class="text-center py-5">
                <i class="bi bi-bell-slash d-block mb-2" style="font-size:32px; color: var(--text-tertiary);"></i>
                <span style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-tertiary);">
                    {{ __('ui.no_notifications') }}
                </span>
            </div>

            {{-- Notifications --}}
            <template x-for="notif in notifications" :key="notif.id">
                <a :href="notif.link_url || '#'"
                   @click.prevent="handleClick(notif)"
                   class="d-flex align-items-start gap-3 p-3 border-bottom text-decoration-none"
                   :class="notif.is_read ? '' : 'bg-light'"
                   style="border-color: var(--border-light); transition: background 0.15s;"
                   @mouseenter="$el.classList.add('bg-light-subtle')"
                   @mouseleave="$el.classList.remove('bg-light-subtle')">
                    {{-- Icon --}}
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:36px;height:36px;"
                         :style="notif.is_read ? 'background:#e9ecef;' : 'background:#d6e3ff;'">
                        <i :class="notif.icon_class"
                           :style="notif.is_read ? 'font-size:16px; color:#6c757d;' : 'font-size:16px; color:#0031e1;'"></i>
                    </div>

                    {{-- Content --}}
                    <div class="flex-grow-1" style="min-width:0;">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <span class="fw-medium"
                                  x-text="notif.type_label"
                                  style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary);"></span>
                            <span class="text-nowrap"
                                  x-text="notif.time_ago"
                                  style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--text-tertiary);"></span>
                        </div>
                        <p class="mb-0 mt-1"
                           x-text="notif.message"
                           style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-secondary); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;"></p>
                    </div>

                    {{-- Unread Dot --}}
                    <div x-show="!notif.is_read" class="flex-shrink-0 mt-2">
                        <span class="d-inline-block rounded-circle" style="width:8px;height:8px; background:#0031e1;"></span>
                    </div>
                </a>
            </template>
        </div>

        {{-- Load More --}}
        <div x-show="hasMore && !loading" class="text-center p-2 border-top" style="border-color: var(--border-light);">
            <button @click="loadMore()"
                    class="btn btn-link text-decoration-none p-0"
                    style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--primary-blue-light);">
                {{ __('ui.load_more') }}
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBell', () => ({
        open: false,
        loading: false,
        unreadCount: 0,
        notifications: [],
        currentPage: 1,
        lastPage: 1,
        hasMore: false,

        init() {
            this.fetchUnreadCount();
            this.pollUnreadCount();
        },

        toggleDropdown(event) {
            event.stopPropagation();
            this.open = !this.open;
            if (this.open && this.notifications.length === 0) {
                this.fetchNotifications();
            }
        },

        async fetchUnreadCount() {
            try {
                const res = await fetch(@js(route('notifications.unread-count')), {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                this.unreadCount = data.count || 0;
            } catch (e) {
                // Silently ignore
            }
        },

        pollUnreadCount() {
            setInterval(() => {
                this.fetchUnreadCount();
            }, 30000); // Poll every 30 seconds
        },

        async fetchNotifications(page = 1) {
            this.loading = true;
            try {
                const res = await fetch(@js(route('notifications.index')) + '?page=' + page, {
                    headers: { 'Accept': 'application/json' },
                });
                const result = await res.json();
                if (result.success) {
                    if (page === 1) {
                        this.notifications = result.data;
                    } else {
                        this.notifications = this.notifications.concat(result.data);
                    }
                    this.currentPage = result.meta.current_page;
                    this.lastPage = result.meta.last_page;
                    this.hasMore = this.currentPage < this.lastPage;
                }
            } catch (e) {
                // Silently ignore
            } finally {
                this.loading = false;
            }
        },

        loadMore() {
            if (this.currentPage < this.lastPage && !this.loading) {
                this.fetchNotifications(this.currentPage + 1);
            }
        },

        async handleClick(notif) {
            // Mark as read
            if (!notif.is_read) {
                try {
                    await fetch(
                        @js(route('notifications.mark-read', ['id' => '__ID__'])).replace('__ID__', notif.id),
                        {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            },
                        }
                    );
                    notif.is_read = true;
                    if (this.unreadCount > 0) this.unreadCount--;
                } catch (e) {
                    // Silently ignore
                }
            }

            this.open = false;

            // Navigate to link
            if (notif.link_url) {
                window.location.href = notif.link_url;
            }
        },

        async markAllAsRead() {
            try {
                const res = await fetch(@js(route('notifications.mark-all-read')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                });
                const data = await res.json();
                if (data.success) {
                    this.notifications.forEach(n => n.is_read = true);
                    this.unreadCount = 0;
                }
            } catch (e) {
                // Silently ignore
            }
        },
    }));
});
</script>
