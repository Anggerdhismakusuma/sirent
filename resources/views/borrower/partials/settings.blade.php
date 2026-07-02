{{-- SI-RENT Dashboard: Settings Tab --}}
<div x-data="{
    theme: localStorage.getItem('sirent-theme') || document.documentElement.getAttribute('data-bs-theme') || 'light',
    lang: (window.SIRENT_CONFIG && window.SIRENT_CONFIG.lang) || 'id',
    saving: false,
    saved: false,

    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', this.theme);
        localStorage.setItem('sirent-theme', this.theme);
        this.save({ theme: this.theme });
    },

    setLang(l) {
        if (this.lang === l) return;
        this.lang = l;
        this.save({ language: l });
    },

    save(data) {
        var self = this;
        this.saving = true;
        this.saved = false;
        var csrf = document.querySelector('meta[name=csrf-token]');
        fetch('/dashboard/settings', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf ? csrf.content : '',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            self.saving = false;
            if (res.success) {
                self.saved = true;
                setTimeout(function() { self.saved = false; }, 2000);
                if (data.language) location.reload();
            }
        })
        .catch(function() { self.saving = false; });
    }
}" x-cloak>
    <div class="row p-3">
        <div class="col-lg-8 m-auto w-100" style="max-width: 900px;">
            <div class="bg-white rounded-4 p-4 shadow-sm border" style="border-color:#d4d4d4;">
                <h4 class="fw-semibold mb-4" style="font-family:'Mona Sans',sans-serif; font-size:24px; color:#204BE5;">{{ __('ui.settings') }}</h4>

                {{-- Theme --}}
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span class="fw-medium d-flex align-items-center gap-2" style="font-family:'Mona Sans',sans-serif; font-size:20px; color:#062375;">
                        <i class="bi bi-moon-stars"></i> {{ __('ui.theme') }}
                    </span>
                    <button type="button" @click="toggleTheme()"
                        class="theme-switch"
                        :class="theme === 'dark' ? 'theme-switch-on' : 'theme-switch-off'"
                        aria-label="Toggle theme">
                    <span class="theme-switch-thumb d-flex align-items-center justify-content-center">
                        <i class="bi bi-sun-fill" x-show="theme !== 'dark'" style="font-size:11px; color:#f59e0b;"></i>
                        <i class="bi bi-moon-stars-fill" x-show="theme === 'dark'" style="font-size:10px; color:#a78bfa;"></i>
                    </span>
                </button>
                </div>

                {{-- Language --}}
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span class="fw-medium d-flex align-items-center gap-2" style="font-family:'Mona Sans',sans-serif; font-size:20px; color:#062375;">
                        <i class="bi bi-translate"></i> {{ __('ui.language') }}
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2"
                                type="button" data-bs-toggle="dropdown"
                                style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#062375; background:#fff; border:1px solid #d4d4d4; border-radius:5px; min-width:120px;">
                            <span x-text="lang === 'id' ? 'Indonesia' : 'English'"></span>
                        </button>
                        <ul class="dropdown-menu shadow-sm" style="border-radius:5px; min-width:120px;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                   href="#" @click.prevent="setLang('id')"
                                   style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                                    Indonesia
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2"
                                   href="#" @click.prevent="setLang('en')"
                                   style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                                    English
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Connected Devices --}}
                <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                    <span class="fw-medium d-flex align-items-center gap-2" style="font-family:'Mona Sans',sans-serif; font-size:20px; color:#062375;">
                        <i class="bi bi-phone"></i> {{ __('ui.connected_devices') }}
                    </span>
                    <i class="bi bi-chevron-right text-muted" style="font-size:18px;"></i>
                </div>

                {{-- Notification --}}
                <div class="d-flex justify-content-between align-items-center py-3">
                    <span class="fw-medium d-flex align-items-center gap-2" style="font-family:'Mona Sans',sans-serif; font-size:20px; color:#062375;">
                        <i class="bi bi-bell"></i> {{ __('ui.notification') }}
                    </span>
                    <i class="bi bi-chevron-right text-muted" style="font-size:18px;"></i>
                </div>

                {{-- Saved indicator --}}
                <div x-show="saved" class="text-success mt-3 text-end"
                     style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ __('ui.settings_saved') }}
                </div>
            </div>
        </div>
    </div>
</div>
