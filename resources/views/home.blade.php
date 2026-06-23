<<<<<<< HEAD
<body>
    <x-navbar />
    
</body>
=======
﻿{{-- SI-RENT Home —” F-PUB-01 (Guest) & F-PUB-02 (Logged-in), Figma nodes 1737-2623 & 1749-4405 --}}
@extends('layouts.app')

@section('title', __('ui.home_title', ['app' => config('app.name', 'SI-RENT')]))

@section('content')

{{-- ============ HERO SECTION (Shared: guest & logged-in) ============ --}}
<div class="position-relative overflow-hidden" style="background:linear-gradient(180deg, #0c0090 0%, #001f7e 60%, #f8f9fa 100%); min-height:620px;">
    {{-- Hero Card --}}
    <div class="container position-relative" style="z-index:2; padding-top:45px;">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white pt-4">
                <h1 class="fw-bold display-4" style="font-family:'Mona Sans',sans-serif; line-height:1.15;">
                    {{ __('ui.rent_anything') }}<br>{{ __('ui.you_need') }}
                </h1>
                <p class="fs-5 mb-4 opacity-80" style="font-family:'Mona Sans',sans-serif; max-width:400px;">
                    {{ __('ui.hero_subtitle') }}
                </p>

                {{-- Search Bar --}}
                <form action="{{ route('products.index') }}" method="GET"
                      class="bg-white rounded-3 d-flex align-items-center shadow"
                      style="max-width:574px; height:57px; padding:4px;">
                    <input type="text" name="q" class="form-control border-0 h-100 ms-2" placeholder="{{ __('ui.what_do_you_want') }}"
                           style="font-size:15px; font-family:'Mona Sans',sans-serif; box-shadow:none; background:transparent;">
                    <div style="width:1px; height:45px; background:#ccc;"></div>
                    <span class="text-muted ps-3"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" name="location" class="form-control border-0 h-100" placeholder="{{ __('ui.location') }}"
                           style="font-size:15px; font-family:'Mona Sans',sans-serif; max-width:160px; box-shadow:none; background:transparent;">
                    <button type="submit"
                            class="btn text-white fw-semibold d-flex align-items-center h-100 px-4 border-0"
                            style="background:var(--primary-blue); border-radius:10px; font-size:14px; font-family:'Mona Sans',sans-serif; white-space:nowrap;">
                        {{ __('ui.rent_now') }}
                    </button>
                </form>

                {{-- Trust Badges --}}
                <div class="d-flex gap-4 mt-3 flex-wrap" style="font-family:'Mona Sans',sans-serif; font-size:13px;">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-shield-check"></i>
                        <div><strong>{{ __('ui.free_cancellation') }}</strong><br><small class="opacity-75">{{ __('ui.before_1_day') }}</small></div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-hand-thumbs-up"></i>
                        <div><strong>{{ __('ui.secure_payment') }}</strong><br><small class="opacity-75">{{ __('ui.protected_100') }}</small></div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-headset"></i>
                        <div><strong>{{ __('ui.support_24_7') }}</strong><br><small class="opacity-75">{{ __('ui.were_here_to_help') }}</small></div>
                    </div>
                </div>
            </div>
            {{-- Hero image --}}
            <div class="col-lg-5 d-none d-lg-flex justify-content-end pe-0">
                @if($featuredProduct && $featuredProduct->primaryImage)
                <img src="{{ asset('storage/' . $featuredProduct->primaryImage->image_path) }}"
                    alt="{{ $featuredProduct->title }}"
                    class="img-fluid rounded-4"
                    style="max-height:420px; object-fit:cover; border-radius:20px;">
                @else
                <div class="rounded-4 d-flex align-items-center justify-content-center"
                    style="width:476px; height:444px; background:linear-gradient(180deg, rgba(255,255,255,0.3), rgba(255,255,255,0.05)); border-radius:20px;">
                    <i class="bi bi-camera text-white opacity-50" style="font-size:120px;"></i>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ================================================================ --}}
{{-- LOGGED-IN ONLY: Quick Stats Cards (F-PUB-02, Figma y=665) --}}
{{-- ================================================================ --}}
@auth
<div class="container mt-4">
    <div class="row g-3">
        {{-- Active Rentals —” link to borrower.activity --}}
        <div class="col-6 col-md-3">
            <a href="{{ route('borrower.activity') }}" class="text-decoration-none">
                <div class="bg-white rounded-3 shadow-sm p-3 d-flex align-items-center gap-3 h-100"
                     style="transition:transform 0.2s; border: 1px solid var(--border-subtle);">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px; height:48px; background:rgba(0,49,225,0.1);">
                        <i class="bi bi-play-circle-fill" style="font-size:22px; color: var(--primary-blue);"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:32px; line-height:1; color: var(--text-primary);">
                            {{ $stats['activeRentals'] }}
                        </div>
                        <div class="fw-medium" style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-primary);">
                            {{ __('ui.active_rentals') }}
                        </div>
                        <div style="font-family:'Mona Sans',sans-serif; font-size:12px; color: var(--text-muted);">
                            {{ __('ui.view_ongoing_rentals') }}
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Upcoming Rentals —” link to borrower.activity --}}
        <div class="col-6 col-md-3">
            <a href="{{ route('borrower.activity') }}" class="text-decoration-none">
                <div class="bg-white rounded-3 shadow-sm p-3 d-flex align-items-center gap-3 h-100"
                     style="transition:transform 0.2s; border: 1px solid var(--border-subtle);">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px; height:48px; background:rgba(0,49,225,0.1);">
                        <i class="bi bi-calendar-check" style="font-size:22px; color: var(--primary-blue);"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:32px; line-height:1; color: var(--text-primary);">
                            {{ $stats['upcomingRentals'] }}
                        </div>
                        <div class="fw-medium" style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-primary);">
                            {{ __('ui.upcoming_rentals') }}
                        </div>
                        <div style="font-family:'Mona Sans',sans-serif; font-size:12px; color: var(--text-muted);">
                            {{ __('ui.view_upcoming_rentals') }}
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Favourite Items —” link to products.index --}}
        <div class="col-6 col-md-3">
            <a href="{{ route('products.index') }}" class="text-decoration-none">
                <div class="bg-white rounded-3 shadow-sm p-3 d-flex align-items-center gap-3 h-100"
                     style="transition:transform 0.2s; border: 1px solid var(--border-subtle);">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px; height:48px; background:rgba(0,49,225,0.1);">
                        <i class="bi bi-heart" style="font-size:22px; color: var(--primary-blue);"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:32px; line-height:1; color: var(--text-primary);">
                            {{ $stats['favouriteItems'] }}
                        </div>
                        <div class="fw-medium" style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-primary);">
                            {{ __('ui.favourite_items') }}
                        </div>
                        <div style="font-family:'Mona Sans',sans-serif; font-size:12px; color: var(--text-muted);">
                            {{ __('ui.view_favourite_items') }}
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- This Month Items Rented —” link to borrower.history --}}
        <div class="col-6 col-md-3">
            <a href="{{ route('borrower.history') }}" class="text-decoration-none">
                <div class="bg-white rounded-3 shadow-sm p-3 d-flex align-items-center gap-3 h-100"
                     style="transition:transform 0.2s; border: 1px solid var(--border-subtle);">
                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center"
                         style="width:48px; height:48px; background:rgba(0,49,225,0.1);">
                        <i class="bi bi-clock-history" style="font-size:22px; color: var(--primary-blue);"></i>
                    </div>
                    <div>
                        <div class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:32px; line-height:1; color: var(--text-primary);">
                            {{ $stats['thisMonthRented'] }}
                        </div>
                        <div class="fw-medium" style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-primary);">
                            {{ __('ui.this_month_rented') }}
                        </div>
                        <div style="font-family:'Mona Sans',sans-serif; font-size:12px; color: var(--text-muted);">
                            {{ __('ui.view_history') }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endauth

{{-- ================================================================ --}}
{{-- GUEST ONLY: Recommended Section (F-PUB-01) --}}
{{-- ================================================================ --}}
@guest
<div class="container mt-4">
    <h4 class="fw-semibold mb-3 px-1" style="font-family:'Mona Sans',sans-serif; font-size:22px;">{{ __('ui.recommended') }}</h4>
    <div class="row g-5">
        @forelse($recomended->count() ? $recomended->take(6) : [] as $product)
            <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <x-product.product-card :product="$product" />
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted" style="font-family:'Mona Sans',sans-serif;">
                    <i class="bi bi-box-seam d-block mb-2" style="font-size:48px;"></i>
                    {{ __('ui.no_products_available') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endguest

{{-- ============ CTA CAROUSEL BANNER (Shared, Figma 1565-4676) ============ --}}
<div x-data="ctaCarousel()" class="mt-5 overflow-hidden position-relative"
     style="background:linear-gradient(269.76deg, #24015b 45.67%, #4b02c1 96.09%); min-height:234px;">

    {{-- Slides --}}
    <div class="container position-relative py-4" style="z-index:2;">
        <template x-for="(slide, i) in slides" :key="i">
            <div x-show="active === i"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="row align-items-center" style="min-height:234px;">
                <div class="col-lg-7">
                    {{-- Product Title —” Teko SemiBold, 96px Figma (clamp for responsive) --}}
                    <h2 class="font-logo text-white mb-0" style="font-size:clamp(40px,6vw,96px); line-height:1;"
                        x-text="slide.title"></h2>

                    {{-- Rented count + avatar row --}}
                    <div class="d-flex align-items-center gap-2 mt-2 mb-3">
                        {{-- Avatar group (Figma 1565:4621) --}}
                        <div class="d-flex" style="margin-right:-6px;">
                            <div class="rounded-circle border border-white d-flex align-items-center justify-content-center"
                                 style="width:28px; height:28px; background:#ccc; font-size:12px; color: var(--text-muted); margin-right:-8px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="rounded-circle border border-white d-flex align-items-center justify-content-center"
                                 style="width:28px; height:28px; background:#bbb; font-size:12px; color: var(--text-muted); margin-right:-8px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <div class="rounded-circle border border-white d-flex align-items-center justify-content-center"
                                 style="width:28px; height:28px; background:#aaa; font-size:12px; color: var(--text-muted);">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>
                        <span class="text-white fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:20px;"
                              x-text="slide.subtitle"></span>
                    </div>

                    {{-- CTA Buttons --}}
                    <div class="d-flex gap-3 align-items-center">
                        <a :href="slide.link"
                           class="btn bg-white text-dark fw-semibold px-4 py-2 rounded-pill"
                           style="font-size:20px; font-family:'Mona Sans',sans-serif; box-shadow:0px 4px 4px rgba(0,0,0,0.3);">
                            {{ __('ui.rent_now_cta') }}
                        </a>
                        <a :href="slide.link"
                           class="text-white fw-semibold text-decoration-none position-relative d-inline-block"
                           style="font-size:20px; font-family:'Mona Sans',sans-serif;">
                            {{ __('ui.see_details') }}
                            <span class="d-block position-absolute" style="bottom:-2px; left:0; right:0; height:1px; background:white;"></span>
                        </a>
                    </div>
                </div>

                {{-- Decorative abstract shapes (Figma right-side graphics) --}}
                <div class="col-lg-5 d-none d-lg-flex justify-content-center position-relative">
                    <div class="text-white opacity-20" style="font-size:180px; transform:rotate(15deg);">
                        &#9670;
                    </div>
                </div>
            </div>
        </template>
    </div>

    {{-- Left Navigation Arrow --}}
    <button @click="prev()"
            class="cta-carousel-arrow position-absolute start-0 top-50 translate-middle-y btn border-0 d-flex align-items-center justify-content-center"
            style="background:rgba(238,226,226,0.72); opacity:0.85; width:48px; height:60px; border-radius:0 100px 100px 0; z-index:3; cursor:pointer; transition:opacity 0.2s;"
            aria-label="{{ __('ui.previous_slide') }}">
        <i class="bi bi-chevron-left text-white" style="font-size:28px;"></i>
    </button>

    {{-- Right Navigation Arrow --}}
    <button @click="next()"
            class="cta-carousel-arrow position-absolute end-0 top-50 translate-middle-y btn border-0 d-flex align-items-center justify-content-center"
            style="background:rgba(238,226,226,0.72); opacity:0.85; width:48px; height:60px; border-radius:100px 0 0 100px; z-index:3; cursor:pointer; transition:opacity 0.2s;"
            aria-label="{{ __('ui.next_slide') }}">
        <i class="bi bi-chevron-right text-white" style="font-size:28px;"></i>
    </button>

    {{-- Slide indicator dots --}}
    <div class="d-flex justify-content-center gap-2 position-absolute bottom-0 start-50 translate-middle-x pb-3" style="z-index:3;">
        <template x-for="(dot, i) in slides" :key="'dot-'+i">
            <button @click="goTo(i)"
                    class="border-0 rounded-circle p-0"
                    :class="active === i ? 'bg-white' : 'bg-white opacity-50'"
                    style="width:8px; height:8px; transition:all 0.3s;"
                    :aria-label="'Slide ' + (i + 1)"></button>
        </template>
    </div>
</div>

{{-- ============ NEAR YOU SECTION (Shared) ============ --}}
<div class="container mt-5">
    <h4 class="fw-semibold mb-3 px-1" style="font-family:'Mona Sans',sans-serif; font-size:22px;">{{ __('ui.near_you') }}</h4>
</div>
<div class="container-fluid px-3">
    <div class="d-flex gap-3 overflow-auto pb-2" style="scroll-snap-type:x mandatory;">
        @forelse($nearYou as $product)
            <div class="flex-shrink-0" style="scroll-snap-align:start;">
                <x-product.product-card :product="$product" />
            </div>
        @empty
            <div class="text-center py-4 w-100">
                <p class="text-muted" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.no_products_near_you') }}</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ============ AVAILABLE NOW SECTION (Shared) ============ --}}
<div class="container my-5">
    <h4 class="fw-semibold mb-3 px-1" style="font-family:'Mona Sans',sans-serif; font-size:22px;">{{ __('ui.available_now') }}</h4>
    <div class="row g-5">
        @forelse($availableNow as $product)
            <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <x-product.product-card :product="$product" />
            </div>
        @empty
            <div class="col-12 text-center py-4">
                <p class="text-muted" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.no_products_available_now') }}</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ================================================================ --}}
{{-- LOGGED-IN ONLY: "For [Name]" Personalized Section (F-PUB-02, Figma y=1961) --}}
{{-- ================================================================ --}}
@auth
<div class="container mt-5">
    <h4 class="fw-semibold mb-3 px-1" style="font-family:'Mona Sans',sans-serif; font-size:22px;">
        {{ __('ui.for_user', ['name' => explode(' ', auth()->user()->name)[0]]) }}
    </h4>
    <div class="row g-5">
        @forelse($recomended as $product)
            <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <x-product.product-card :product="$product" />
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted" style="font-family:'Mona Sans',sans-serif;">
                    <i class="bi bi-box-seam d-block mb-2" style="font-size:48px;"></i>
                    {{ __('ui.no_recommendations') }}
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination — dynamic based on product count --}}
    @if($recomended->hasPages())
    <div class="d-flex justify-content-center mt-4 mb-5 shadow-none">
        {{ $recomended->links('vendor.pagination.bootstrap-5') }}
    </div>
    @endif
</div>
@endauth

@endsection

@push('styles')
<style>
    /* Hide scrollbar for Near You section */
    .overflow-auto::-webkit-scrollbar {
        height: 6px;
    }
    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    .overflow-auto::-webkit-scrollbar-thumb {
        background: var(--primary-blue);
        border-radius: 3px;
    }

    /* CTA Carousel —” nav arrow hover */
    .cta-carousel-arrow:hover {
        opacity: 1 !important;
        background: rgba(238,226,226,0.95) !important;
    }

    /* Stats cards hover effect */
    .col-6.col-md-3 a:hover > div {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // CTA Carousel (Figma node 1565-4676)
    function ctaCarousel() {
        return {
            active: 0,
            slides: [
                {
                    title: @json($featuredProduct ? strtoupper($featuredProduct->title) : 'SONY ZV E10 KIT'),
                    subtitle: @json($featuredProduct ? __('ui.people_rented', ['count' => '1.2K']) : __('ui.people_rented', ['count' => '1.2K'])),
                    link: @json($featuredProduct ? route('products.show', $featuredProduct->slug) : '#'),
                },
                {
                    title: 'CANON EOS R50',
                    subtitle: @json(__('ui.people_rented', ['count' => '900+'])),
                    link: @json(route('products.index')),
                },
                {
                    title: 'DJI MINI 4 PRO',
                    subtitle: @json(__('ui.people_rented', ['count' => '2.1K'])),
                    link: @json(route('products.index')),
                },
            ],
            next() {
                this.active = (this.active + 1) % this.slides.length;
            },
            prev() {
                this.active = (this.active - 1 + this.slides.length) % this.slides.length;
            },
            goTo(i) {
                this.active = i;
            },
            // Auto-rotate every 5 seconds
            init() {
                this._interval = setInterval(() => this.next(), 5000);
            },
            destroy() {
                clearInterval(this._interval);
            },
        };
    }
</script>
@endpush
>>>>>>> origin/feat-peminjam
