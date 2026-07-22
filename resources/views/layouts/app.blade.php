<!DOCTYPE html>
@php
    $theme = auth()->check() ? (auth()->user()->theme ?? 'light') : 'light';
@endphp
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SI-RENT'))</title>

    {{-- FOUC Prevention: apply theme before page renders --}}
    <script>
        (function() {
            var theme = localStorage.getItem('sirent-theme');
            if (theme === 'dark' || theme === 'light') {
                document.documentElement.setAttribute('data-bs-theme', theme);
            }
        })();
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=mona-sans:400,500,600,700|teko:600" rel="stylesheet" />
    <link rel="shortcut icon" href="{{ asset('images/logo-sirent.svg') }}" type="image/x-icon">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @stack('styles')
    @stack('scripts')
</head>
<body>
    {{-- Sembunyikan Navbar saat di halaman onboarding --}}
    @if(!Request::is('onboarding*') && !Request::is('admin*'))
        @sectionMissing('hide-navbar')
            <x-layout.navbar />
        @endif
    @endif

    <main>
        @yield('content')
    </main>

    {{-- Sembunyikan Footer saat di halaman onboarding --}}
    @if(!Request::is('onboarding*') && !Request::is('admin*'))
        @sectionMissing('hide-footer')
            <x-layout.footer />
        @endif
    @endif

    {{-- Auth Modal — available on all pages --}}
    <x-auth-modal />

    @stack('scripts')

    @auth
    <script>
        window.AuthUser = { id: {{ auth()->id() }}, name: '{{ auth()->user()->name }}' };
        window.SIRENT_CONFIG = {
            lang: '{{ auth()->user()->language ?? 'id' }}',
            theme: '{{ auth()->user()->theme ?? 'light' }}'
        };

        // Listener Echo ditaruh di sini (khusus user login)
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                if (window.Echo && window.AuthUser) {
                    window.Echo.private('user.' + window.AuthUser.id)
                        .listen('.message.sent', function(e) {
                            var badge = document.getElementById('chat-unread-count');
                            if (badge) {
                                var current = parseInt(badge.textContent || '0');
                                badge.textContent = (current + 1) > 99 ? '99+' : (current + 1);
                                badge.hidden = false;
                                badge.style.transform = 'scale(1.3)';
                                setTimeout(function() { badge.style.transform = 'scale(1)'; }, 200);
                            }
                        });
                }
            }, 1000);
        });
    </script>
    @else
    @php $guestLang = session()->get('locale', config('app.locale', 'id')); @endphp
    <script>
        window.SIRENT_CONFIG = { lang: '{{ $guestLang }}', theme: '{{ $theme }}' };
    </script>
    @endauth

    <script>
        // Auto-open auth modal based on URL params
        document.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.get('auth') === 'login') {
                window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: { mode: 'login' } }));
            } else if (params.get('auth') === 'register') {
                window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: { mode: 'register' } }));
            }
        });
    </script>
</body>
</html>
