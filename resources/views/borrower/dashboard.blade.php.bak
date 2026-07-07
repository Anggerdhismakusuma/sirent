{{-- SI-RENT Borrower Dashboard Container — F-BRW-06/07/08/09 --}}
@extends('layouts.app')

@section('title', 'Dashboard — SI-RENT')
@section('content')

    @php
        $initialTab = 'profile';
        if (request()->has('tab')) {
            $initialTab = in_array(request()->get('tab'), ['profile', 'activity', 'settings', 'store'])
                ? request()->get('tab')
                : 'profile';
        }
    @endphp

<div class="container-fluid p-0 h-100" style="background:#f8f9fa; min-height:100vh;">
<div class="row g-0" x-data="{ activeTab: '{{ $initialTab }}' }">

    {{-- ============ LEFT SIDEBAR (shared) ============ --}}
    <div class="col-auto d-none d-md-block bg-white shadow-sm position-sticky top-0"
         style="width:289px; max-height:100vh; border-radius:0 20px 20px 0; z-index:100;">

        <div class="d-flex flex-column h-100 px-3 pt-4 p-5" style="background:#04278c; min-height:100vh; border-radius:0 20px 20px 0;">

            {{-- Logo --}}
            <div class="d-flex align-items-center gap-2 mb-4 ps-2">
                <img src="{{ asset('images/logo-sirent.svg') }}" alt="SI-RENT" width="42" height="35">
                <span class="font-logo text-white" style="font-size:32px;">SI-RENT</span>
            </div>

                    {{-- User Avatar --}}
                    <div class="text-center mb-4">
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

                    {{-- Navigation — Profile / Activity / Settings --}}
                    <nav class="flex-grow-1">
                        <div class="rounded-4 p-3 mb-3"
                            style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);">

                            <a href="#"
                                @click.prevent="activeTab='profile'; window.history.replaceState({},'','?tab=profile')"
                                class="d-flex align-items-center gap-3 text-decoration-none mb-2 py-1 rounded-3 px-2"
                                :style="activeTab === 'profile' ? 'background:rgba(255,255,255,0.15)' : ''">
                                <span class="rounded-2 flex-shrink-0" style="width:3px; height:40px;"
                                    :style="activeTab === 'profile' ? 'background:white' : ''"></span>
                                <i class="bi bi-person text-white" style="font-size:20px; opacity:0.9;"></i>
                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px;"
                                    :style="activeTab === 'profile' ? '' : 'opacity:0.7'">{{ __('ui.profile') }}</span>
                            </a>

                            <a href="#"
                                @click.prevent="activeTab='activity'; window.history.replaceState({},'','?tab=activity')"
                                class="d-flex align-items-center gap-3 text-decoration-none mb-2 py-1 rounded-3 px-2"
                                :style="activeTab === 'activity' ? 'background:rgba(255,255,255,0.15)' : ''">
                                <span class="rounded-2 flex-shrink-0" style="width:3px; height:40px;"
                                    :style="activeTab === 'activity' ? 'background:white' : ''"></span>
                                <i class="bi bi-activity text-white" style="font-size:20px; opacity:0.9;"></i>
                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px;"
                                    :style="activeTab === 'activity' ? '' : 'opacity:0.7'">{{ __('ui.activity') }}</span>
                            </a>

                            <a href="#"
                                @click.prevent="activeTab='settings'; window.history.replaceState({},'','?tab=settings')"
                                class="d-flex align-items-center gap-3 text-decoration-none py-1 rounded-3 px-2"
                                :style="activeTab === 'settings' ? 'background:rgba(255,255,255,0.15)' : ''">
                                <span class="rounded-2 flex-shrink-0" style="width:3px; height:40px;"
                                    :style="activeTab === 'settings' ? 'background:white' : ''"></span>
                                <i class="bi bi-gear text-white" style="font-size:20px; opacity:0.9;"></i>
                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px;"
                                    :style="activeTab === 'settings' ? '' : 'opacity:0.7'">{{ __('ui.settings') }}</span>
                            </a>

                            <a href="#"
                                @click.prevent="activeTab='store'; window.history.replaceState({},'','?tab=store'); setTimeout(() => window.initStoreCharts && window.initStoreCharts(), 200)"
                                class="d-flex align-items-center gap-3 text-decoration-none mt-2 py-1 rounded-3 px-2"
                                :style="activeTab === 'store' ? 'background:rgba(255,255,255,0.15)' : ''">

                                <span class="rounded-2 flex-shrink-0" style="width:3px; height:40px;"
                                    :style="activeTab === 'store' ? 'background:white' : ''"></span>

                                <i class="bi bi-shop text-white" style="font-size:20px; opacity:0.9;"></i>

                                <span class="text-white fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px;"
                                    :style="activeTab === 'store' ? '' : 'opacity:0.7'">
                                    Store
                                </span>
                            </a>
                        </div>
                    </nav>

                    {{-- Bottom --}}
                    <div class="mt-auto pb-4 ps-4">
                        <a href="{{ route('about.index') }}"
                            class="d-flex align-items-center gap-3 text-decoration-none text-white mb-2 px-2 py-2 rounded-3"
                            style="background:rgba(255,255,255,0.08);">
                            <i class="bi bi-question-circle" style="font-size:20px;"></i>
                            <span class="fw-semibold"
                                style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.about_us') }}</span>
                        </a>
                        <form id="sidebar-logout" action="{{ route('auth.logout') }}" method="POST">
                            @csrf
                            <a href="#" onclick="document.getElementById('sidebar-logout').submit();"
                                class="d-flex align-items-center gap-3 text-decoration-none text-white px-2 py-2"
                                style="opacity:0.6;">
                                <i class="bi bi-box-arrow-left" style="font-size:20px;"></i>
                                <span class="fw-semibold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.logout') }}</span>
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Navigation — Profile / Activity / Settings --}}
            <nav class="flex-grow-1">
                <div class="rounded-4 p-3 mb-3" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);">

                {{-- Top Profile Banner (shared) --}}
                <div x-show="activeTab !== 'store'" x-cloak
                    class="position-relative overflow-hidden rounded-4 mt-3 mx-3 mb-4"
                    style="background:linear-gradient(135deg, #8fbdff 0%, #5c8fde 100%); min-height:250px;">
                    <div class="position-absolute w-100 h-100" style="background:rgba(0,0,0,0.08);"></div>
                    <div class="position-relative d-flex align-items-center p-4 h-100" style="z-index:1;">
                        <div class="flex-shrink-0 me-4 position-relative d-inline-block">
                            <x-shared.avatar :imagePath="$user->avatar" :name="$user->name" size="lg" />
                            {{-- Camera overlay — positioned at bottom-right of avatar --}}
                            <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center"
                                style="bottom:-4px; right:-4px; width:40px; height:40px; background:white; box-shadow:0 2px 6px rgba(0,0,0,0.2); cursor:pointer;"
                                title="Change profile photo">
                                <i class="bi bi-camera" style="font-size:18px; color:#0031e1;"></i>
                            </div>
                        </div>
                        <div class="text-white">
                            <h2 class="fw-normal mb-1" style="font-family:'Mona Sans',sans-serif; font-size:29px;">
                                {{ $user->name }}</h2>
                            <div class="d-flex align-items-center gap-1 mb-2">
                                <i class="bi bi-geo-alt" style="font-size:18px;"></i>
                                <span class="fw-bold"
                                    style="font-family:'Mona Sans',sans-serif; font-size:18px;">{{ $user->location_city ?? 'Bogor, Jawa Barat' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-white">
                    <h2 class="fw-normal mb-1" style="font-family:'Mona Sans',sans-serif; font-size:29px;">{{ $user->name }}</h2>
                    <div class="d-flex align-items-center gap-1 mb-2">
                        <i class="bi bi-geo-alt" style="font-size:18px;"></i>
                        <span class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:18px;">{{ $user->location_city ?? 'Bogor, Jawa Barat' }}</span>
                    </div>
                    <div x-show="activeTab === 'store'" x-cloak>
                        @include('borrower.partials.store')
                    </div>
                </div>
            </div>
        </div>

        {{-- Tab Contents --}}
        <div class="mx-3">
            <div x-show="activeTab === 'profile'">
                @include('borrower.partials.profile')
            </div>
            <div x-show="activeTab === 'activity'">
                @include('borrower.partials.activity')
            </div>
            <div x-show="activeTab === 'settings'">
                @include('borrower.partials.settings')
            </div>
        </div>

    </div>{{-- End main col --}}

</div>{{-- End x-data row --}}
</div>{{-- End container-fluid --}}

@endsection
