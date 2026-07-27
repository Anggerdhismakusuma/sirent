{{-- SI-RENT Borrower Dashboard Container — F-BRW-06/07/08/09 --}}
@extends('layouts.app')

@section('title', 'Dashboard — SI-RENT')
@section('hide-footer', true)

@section('content')
    @php
        $activeTab = request()->query('tab', 'profile');

        if (!in_array($activeTab, ['profile', 'activity', 'settings', 'store'])) {
            $activeTab = 'profile';
        }

        function dashboardTabStyle($tab, $activeTab)
        {
            return $tab === $activeTab ? 'background:rgba(255,255,255,0.15);' : '';
        }

        function dashboardTabLineStyle($tab, $activeTab)
        {
            return $tab === $activeTab ? 'background:white;' : '';
        }

        function dashboardTabTextStyle($tab, $activeTab)
        {
            return $tab === $activeTab ? '' : 'opacity:0.7;';
        }
    @endphp

    <div class="container-fluid p-0" style="background:#f8f9fa; min-height:100vh;">
        <div class="row g-0">

            {{-- ============ LEFT SIDEBAR ============ --}}
            <aside class="col-auto d-none d-md-block position-sticky top-0 p-0"
                style="width:289px; height:100vh; z-index:100;">

                <!-- Hapus overflow-y dari kontainer utama agar layout luar tetap kokoh -->
                <div class="d-flex flex-column px-3 pt-4 pb-4 justify-content-between"
                    style="background:#04278c; border-radius:0 20px 20px 0; height:100vh;">

                    {{-- BAGIAN ATAS (Statis - Tidak Ikut Scroll) --}}
                    <div>

                        {{-- User Avatar --}}
                        <div class="text-center mb-3">
                            <div class="position-relative d-inline-block mb-2">
                                <x-shared.avatar :imagePath="$user->avatar" :name="$user->name" size="lg" />
                                <span class="position-absolute rounded-circle border border-white"
                                    style="bottom:5px; right:5px; width:13px; height:13px; background:#69c55c;"></span>
                            </div>

                            <div class="fw-bold text-white" style="font-family:'Geist',sans-serif; font-size:19px;">
                                {{ explode(' ', $user->name)[0] }}
                            </div>

                            <div class="text-white-50" style="font-family:'Mona Sans',sans-serif; font-size:13px;">
                                {{ $user->email }}
                            </div>
                        </div>
                    </div>

                    {{-- BAGIAN TENGAH (Hanya area ini yang bisa di-scroll jika layar sangat pendek) --}}
                    <!-- Taktik: Menggunakan flex-grow-1 dan overflow-y:auto dengan scrollbar tersembunyi -->
                    <nav class="flex-grow-1 my-2 pe-1"
                        style="overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none;">
                        <div class="rounded-4 p-3 mb-2"
                            style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);">

                            <a href="{{ url('/dashboard') }}?tab=profile"
                                class="d-flex align-items-center gap-3 text-decoration-none mb-2 py-1 rounded-3 px-2"
                                style="{{ dashboardTabStyle('profile', $activeTab) }}">
                                <span class="rounded-2 flex-shrink-0"
                                    style="width:3px; height:40px; {{ dashboardTabLineStyle('profile', $activeTab) }}"></span>
                                <i class="bi bi-person text-white" style="font-size:20px; opacity:0.9;"></i>
                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px; {{ dashboardTabTextStyle('profile', $activeTab) }}">
                                    {{ __('ui.profile') }}
                                </span>
                            </a>

                            <a href="{{ url('/dashboard') }}?tab=activity"
                                class="d-flex align-items-center gap-3 text-decoration-none mb-2 py-1 rounded-3 px-2"
                                style="{{ dashboardTabStyle('activity', $activeTab) }}">
                                <span class="rounded-2 flex-shrink-0"
                                    style="width:3px; height:40px; {{ dashboardTabLineStyle('activity', $activeTab) }}"></span>
                                <i class="bi bi-activity text-white" style="font-size:20px; opacity:0.9;"></i>
                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px; {{ dashboardTabTextStyle('activity', $activeTab) }}">
                                    {{ __('ui.activity') }}
                                </span>
                            </a>

                            <a href="{{ url('/dashboard') }}?tab=settings"
                                class="d-flex align-items-center gap-3 text-decoration-none mb-2 py-1 rounded-3 px-2"
                                style="{{ dashboardTabStyle('settings', $activeTab) }}">
                                <span class="rounded-2 flex-shrink-0"
                                    style="width:3px; height:40px; {{ dashboardTabLineStyle('settings', $activeTab) }}"></span>
                                <i class="bi bi-gear text-white" style="font-size:20px; opacity:0.9;"></i>
                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px; {{ dashboardTabTextStyle('settings', $activeTab) }}">
                                    {{ __('ui.settings') }}
                                </span>
                            </a>

                            <a href="{{ url('/dashboard') }}?tab=store"
                                class="d-flex align-items-center gap-3 text-decoration-none py-1 rounded-3 px-2"
                                style="{{ dashboardTabStyle('store', $activeTab) }}">
                                <span class="rounded-2 flex-shrink-0"
                                    style="width:3px; height:40px; {{ dashboardTabLineStyle('store', $activeTab) }}"></span>
                                <i class="bi bi-shop text-white" style="font-size:20px; opacity:0.9;"></i>
                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px; {{ dashboardTabTextStyle('store', $activeTab) }}">
                                    {{ __('ui.store.nav') }}
                                </span>
                            </a>
                        </div>
                    </nav>

                    {{-- BAGIAN BAWAH (Statis - Terkunci di Paling Bawah) --}}
                    <div class="mt-auto pt-2 px-2" style="border-top: 1px solid rgba(255,255,255,0.1);">
                        <a href="{{ route('about') }}"
                            class="d-flex align-items-center gap-3 text-decoration-none text-white mb-2 px-2 py-2 rounded-3"
                            style="background:rgba(255,255,255,0.08);">
                            <i class="bi bi-question-circle" style="font-size:20px;"></i>
                            <span class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:16px;">
                                {{ __('ui.about_us') }}
                            </span>
                        </a>

                        <form id="sidebar-logout" action="{{ route('auth.logout') }}" method="POST" class="m-0">
                            @csrf
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('sidebar-logout').submit();"
                                class="d-flex align-items-center gap-3 text-decoration-none text-white px-2 py-2 d-block"
                                style="opacity:0.6;">
                                <i class="bi bi-box-arrow-left" style="font-size:20px;"></i>
                                <span class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:16px;">
                                    {{ __('ui.logout') }}
                                </span>
                            </a>
                        </form>
                    </div>

                </div>
            </aside>

            {{-- ============ MAIN CONTENT ============ --}}
            <main class="col" style="min-height:100vh; padding:24px 32px; overflow-x:hidden;">

                {{-- Top Profile Banner (profile tab only) --}}
                @if ($activeTab === 'profile')
                    @php
                        $bannerUrl = $user->banner ? asset('storage/' . $user->banner) : null;
                    @endphp
                    <div id="profile-banner" class="position-relative overflow-hidden rounded-4 mb-4"
                        style="min-height:250px;
                           background: linear-gradient(135deg, #8fbdff 0%, #5c8fde 100%){{ $bannerUrl ? ', url(' . $bannerUrl . ') center/cover no-repeat' : '' }};
                           {{ $bannerUrl ? 'background-blend-mode: overlay;' : '' }}">
                        <div class="position-absolute w-100 h-100" style="background:rgba(0,0,0,0.08);"></div>

                        {{-- Banner change button --}}
                        <label class="position-absolute rounded-circle d-flex align-items-center justify-content-center"
                            style="top:12px; right:12px; width:36px; height:36px; background:rgba(255,255,255,0.85); box-shadow:0 2px 6px rgba(0,0,0,0.15); cursor:pointer; z-index:2;"
                            title="{{ __('ui.change_banner') }}">
                            <i class="bi bi-camera" style="font-size:15px; color:#0031e1;"></i>
                            <input type="file" accept="image/jpeg,image/png,image/webp" class="d-none"
                                onchange="window._profileUploadBanner(event)">
                        </label>

                        <div class="position-relative d-flex align-items-center p-4 h-100" style="z-index:1;">
                            <div class="flex-shrink-0 me-4 position-relative d-inline-block">
                                <x-shared.avatar :imagePath="$user->avatar" :name="$user->name" size="lg" />

                                <label
                                    class="position-absolute rounded-circle d-flex align-items-center justify-content-center"
                                    style="bottom:-4px; right:-4px; width:40px; height:40px; background:white; box-shadow:0 2px 6px rgba(0,0,0,0.2); cursor:pointer;"
                                    title="{{ __('ui.change_photo') }}">
                                    <i class="bi bi-camera" style="font-size:18px; color:#0031e1;"></i>
                                    <input type="file" accept="image/jpeg,image/png,image/webp" class="d-none"
                                        onchange="window._profileUploadAvatar(event)">
                                </label>
                            </div>

                            <div class="text-white">
                                <h2 class="fw-normal mb-1" id="banner-user-name"
                                    style="font-family:'Mona Sans',sans-serif; font-size:29px;">
                                    {{ $user->name }}
                                </h2>

                                <div class="d-flex align-items-center gap-1 mb-2">
                                    <i class="bi bi-geo-alt" style="font-size:18px;"></i>
                                    <span class="fw-bold" id="banner-user-location"
                                        style="font-family:'Mona Sans',sans-serif; font-size:18px;">
                                        {{ $user->domicile ?? 'Bogor, Jawa Barat' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Active Tab Content --}}
                <div class="dashboard-content">
                    @switch($activeTab)
                        @case('profile')
                            @include('borrower.partials.profile')
                        @break

                        @case('activity')
                            @include('borrower.partials.activity')
                        @break

                        @case('settings')
                            @include('borrower.partials.settings')
                        @break

                        @case('store')
                            @include('borrower.partials.store')
                        @break

                        @default
                            @include('borrower.partials.profile')
                    @endswitch
                </div>
            </main>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Avatar & banner upload handlers (profile tab only) --}}
    @if ($activeTab === 'profile')
        <script>
            window._profileUploadAvatar = async function(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: @js(__('ui.oops')),
                        text: @js(__('ui.photo_too_large')),
                        confirmButtonColor: '#0031e1',
                    });
                    event.target.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('avatar', file);
                formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

                try {
                    const res = await fetch(@js(route('borrower.profile.update-avatar')), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData,
                    });

                    const data = await res.json();
                    event.target.value = '';

                    if (!data.success) {
                        Swal.fire({
                            icon: 'error',
                            title: @js(__('ui.oops')),
                            text: data.message || @js(__('ui.photo_upload_error')),
                            confirmButtonColor: '#0031e1',
                        });
                        return;
                    }

                    const newSrc = '/storage/' + data.data.avatar + '?t=' + Date.now();

                    document.querySelectorAll('.user-avatar-img').forEach(el => {
                        el.src = newSrc;
                        el.style.display = '';
                    });

                    document.querySelectorAll('.avatar-fallback').forEach(el => {
                        const img = document.createElement('img');
                        img.src = newSrc;
                        img.alt = @js($user->name);
                        img.className = 'rounded-circle object-fit-cover user-avatar-img';
                        img.style.cssText = el.style.cssText;
                        el.replaceWith(img);
                    });

                    Swal.fire({
                        icon: 'success',
                        title: @js(__('ui.success')),
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                } catch (err) {
                    event.target.value = '';
                    Swal.fire({
                        icon: 'error',
                        title: @js(__('ui.network_error_title')),
                        text: @js(__('ui.photo_upload_error')),
                        confirmButtonColor: '#0031e1',
                    });
                }
            };

            window._profileUploadBanner = async function(event) {
                const file = event.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: @js(__('ui.oops')),
                        text: @js(__('ui.banner_too_large')),
                        confirmButtonColor: '#0031e1',
                    });
                    event.target.value = '';
                    return;
                }

                const formData = new FormData();
                formData.append('banner', file);
                formData.append('_token', document.querySelector('meta[name=csrf-token]').content);

                try {
                    const res = await fetch(@js(route('borrower.profile.update-banner')), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData,
                    });

                    const data = await res.json();
                    event.target.value = '';

                    if (!data.success) {
                        Swal.fire({
                            icon: 'error',
                            title: @js(__('ui.oops')),
                            text: data.message || @js(__('ui.banner_upload_error')),
                            confirmButtonColor: '#0031e1',
                        });
                        return;
                    }

                    const newSrc = '/storage/' + data.data.banner + '?t=' + Date.now();
                    const bannerEl = document.getElementById('profile-banner');
                    if (bannerEl) {
                        bannerEl.style.backgroundImage = 'linear-gradient(135deg, #8fbdff 0%, #5c8fde 100%), url(' +
                            newSrc + ')';
                        bannerEl.style.backgroundPosition = 'center';
                        bannerEl.style.backgroundSize = 'cover';
                        bannerEl.style.backgroundRepeat = 'no-repeat';
                        bannerEl.style.backgroundBlendMode = 'overlay';
                    }

                    Swal.fire({
                        icon: 'success',
                        title: @js(__('ui.success')),
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false,
                    });
                } catch (err) {
                    event.target.value = '';
                    Swal.fire({
                        icon: 'error',
                        title: @js(__('ui.network_error_title')),
                        text: @js(__('ui.banner_upload_error')),
                        confirmButtonColor: '#0031e1',
                    });
                }
            };
        </script>
    @endif

    @if ($activeTab === 'store')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    if (window.initStoreCharts) {
                        window.initStoreCharts();
                    }
                }, 200);
            });
        </script>
    @endif
@endpush
