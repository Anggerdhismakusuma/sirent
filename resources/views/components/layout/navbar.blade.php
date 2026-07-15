{{-- SI-RENT Navbar --}}
<nav class="navbar navbar-expand-lg bg-white shadow-sm py-0" style="height:64px;">
    <div class="container h-100">
        {{-- Logo --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ url('/') }}">
            <img src="{{ asset('images/logo-sirent.svg') }}" alt="SI-RENT" width="52" height="43" class="me-1">
            <span class="font-logo text-primary" style="font-size:32px;">SI-RENT</span>
        </a>

        {{-- Search Bar (desktop) --}}
        <div class="d-none d-lg-flex mx-auto" style="width:400px;">
            <form action="{{ route('products.index') }}" method="GET" class="w-100">
                <div class="input-group">  
                    <button type="submit" class="input-group-text bg-transparent border-primary" style="border-radius:10px 0 0 10px;">
                        <i class="bi bi-search text-primary"></i>
                    </button>
                    <input type="text" name="q" class="form-control border-primary shadow-none" placeholder="{{ __('ui.find_any_items') }}"
                        style="border-radius:0 10px 10px 0; font-size:14px;">
                </div>
            </form>
        </div>

        <div class="d-flex align-items-center gap-2 ms-auto">
            @auth
                {{-- Chat Icon --}}
                <a href="{{ route('chat.index') }}" class="btn btn-link text-primary p-0 position-relative me-2" title="{{ __('ui.messages') }}"
                   x-data
                   x-init="
                       fetch('{{ route('chat.unread') }}', {
                           headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                       })
                       .then(r => r.json())
                       .then(data => {
                           const badge = document.getElementById('chat-unread-count');
                           if (badge) {
                               badge.textContent = data.count > 99 ? '99+' : data.count;
                               badge.hidden = data.count === 0;
                           }
                       })
                       .catch(() => {});
                   ">
                    <i class="bi bi-chat-dots fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size:10px;" id="chat-unread-count" hidden>0</span>
                </a>

                {{-- User Dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-link text-dark dropdown-toggle d-flex align-items-center gap-2 text-decoration-none p-0"
                            type="button" data-bs-toggle="dropdown">
                        <x-shared.avatar :imagePath="auth()->user()->avatar" :name="auth()->user()->name" size="sm" />
                        <span class="d-none d-md-inline fw-medium" style="font-size:14px;">{{ auth()->user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border" style="border-radius:10px;">
                        <li>
                            <a class="dropdown-item"
                            href="{{ auth()->user()->role === \App\Models\User::ROLE_ADMIN
                                    ? route('admin.dashboard')
                                    : route('borrower.dashboard') }}">
                                {{ __('ui.dashboard') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                {{ __('ui.logout') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" hidden>
                    @csrf
                </form>
            @else
                <button class="btn btn-outline-primary px-3 d-flex align-items-center gap-1"
                        style="border-radius:10px; font-size:14px;"
                        onclick="window.dispatchEvent(new CustomEvent('open-auth-modal', {detail:{mode:'login'}}))">
                    <span>{{ __('ui.sign_in') }}</span>
                </button>
                <button class="btn btn-primary px-3 d-flex align-items-center gap-1"
                        style="border-radius:10px; font-size:14px; background-color:#0031e1; border-color:#0031e1;"
                        onclick="window.dispatchEvent(new CustomEvent('open-auth-modal', {detail:{mode:'register'}}))">
                    <span>{{ __('ui.sign_up') }}</span>
                </button>
            @endauth
        </div>
    </div>
</nav>
