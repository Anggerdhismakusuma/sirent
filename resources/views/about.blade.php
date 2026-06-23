<<<<<<< HEAD
@section('title', 'About Us')
@vite(['resources/js/app.js'])

<section class="about-page text-white">
    <a class="about-back" href="{{ url()->previous() }}"> {{-- route untuk back button blm diisi menunggu push angger --}}
        <i class="bi bi-chevron-left"></i>
    </a>

    <div class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <div class="col-lg-7 about-left d-flex align-items-center justify-content-center">
                <div class="about-content">
                    <h1>SI-RENT</h1>
                    <p>
                        We are multi-vendor website that aims to provide hobby and
                        equipment rental services, such as cameras, drones and other
                        tools, while maintaining and guaranteeing security between
                        renters and owners of goods.
                    </p>

                    <div class="about-features">
                        <div class="about-feature-item">
                            <h3>Variety</h3>
                            <p>Find all kinds of hobby gear in one place.</p>
                        </div>
    
                        <div class="about-feature-item">
                            <h3>Affordable</h3>
                            <p>Rent high quality equipment without overspending.</p>
                        </div>
    
                        <div class="about-feature-item">
                            <h3>Trusted</h3>
                            <p>Secure transactions between renters and owners.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 about-right d-flex align-items-center justify-content-center">
                <img src="{{ asset('images/logo-sirent 1.png') }}" alt="SI-RENT Logo" class="about-logo">
            </div>
        </div>
    </div>
</section>
=======
﻿{{-- SI-RENT About Us —” F-PUB-03, Figma node 1513:1599 --}}
@extends('layouts.app')

@section('title', __('ui.about_title'))

@section('content')

{{-- ============ HERO: Brand Statement ============ --}}
<div class="position-relative text-center" style="background:linear-gradient(180deg, #0c0090 0%, #001f7e 100%); padding:80px 0 60px;">
    <div class="container">
        <h1 class="font-logo text-white mb-4" style="font-size:clamp(48px,10vw,128px); line-height:1;">
            SI-RENT
        </h1>
        <p class="text-white mx-auto" style="font-family:'Mona Sans',sans-serif; font-size:clamp(14px,2vw,20px); max-width:750px; opacity:0.9; line-height:1.6;">
            {{ __('ui.about_hero') }}
        </p>
    </div>
</div>

{{-- ============ VALUE PROPOSITIONS ============ --}}
<div class="container py-5">
    <div class="row g-4 text-center">
        {{-- Variety --}}
        <div class="col-md-4">
            <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:80px; height:80px; background:#dde7fc;">
                    <i class="bi bi-grid-3x3-gap-fill" style="font-size:36px; color: var(--primary-blue);"></i>
                </div>
                <h3 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:32px;">{{ __('ui.variety') }}</h3>
                <p style="font-family:'Mona Sans',sans-serif; font-size:20px; color: var(--text-secondary);">
                    {{ __('ui.variety_desc') }}
                </p>
            </div>
        </div>

        {{-- Affordable --}}
        <div class="col-md-4">
            <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:80px; height:80px; background:#dde7fc;">
                    <i class="bi bi-cash-coin" style="font-size:36px; color: var(--primary-blue);"></i>
                </div>
                <h3 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:32px;">{{ __('ui.affordable') }}</h3>
                <p style="font-family:'Mona Sans',sans-serif; font-size:20px; color: var(--text-secondary);">
                    {{ __('ui.affordable_desc') }}
                </p>
            </div>
        </div>

        {{-- Trusted --}}
        <div class="col-md-4">
            <div class="bg-white rounded-4 shadow-sm p-4 h-100">
                <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:80px; height:80px; background:#dde7fc;">
                    <i class="bi bi-shield-check" style="font-size:36px; color: var(--primary-blue);"></i>
                </div>
                <h3 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:32px;">{{ __('ui.trusted') }}</h3>
                <p style="font-family:'Mona Sans',sans-serif; font-size:20px; color: var(--text-secondary);">
                    {{ __('ui.trusted_desc') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Trust Badges Row --}}
    <div class="d-flex justify-content-center gap-5 mt-4 flex-wrap" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-secondary);">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-shield-check" style="font-size:24px; color: var(--primary-blue-light);"></i>
            <div><strong style="color: var(--text-primary);">{{ __('ui.free_cancellation') }}</strong><br>{{ __('ui.before_1_day') }}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-hand-thumbs-up" style="font-size:24px; color: var(--primary-blue-light);"></i>
            <div><strong style="color: var(--text-primary);">{{ __('ui.secure_payment') }}</strong><br>{{ __('ui.protected_100') }}</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-headset" style="font-size:24px; color: var(--primary-blue-light);"></i>
            <div><strong style="color: var(--text-primary);">{{ __('ui.support_24_7') }}</strong><br>{{ __('ui.were_here_to_help') }}</div>
        </div>
    </div>
</div>

{{-- ============ RECOMMENDED PRODUCTS ============ --}}
<div class="container">
    <h4 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:24px;">
        {{ __('ui.recommended') }}
    </h4>
    <div class="row g-4">
        @forelse($recomended as $product)
            <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <x-product.product-card :product="$product" />
            </div>
        @empty
            <div class="col-12 text-center py-4">
                <p class="text-muted" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.no_products_available') }}</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ============ NEAR YOU ============ --}}
<div class="container mt-5">
    <h4 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:24px;">{{ __('ui.near_you') }}</h4>
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

{{-- ============ AVAILABLE NOW ============ --}}
<div class="container mt-5">
    <h4 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:24px;">{{ __('ui.available_now') }}</h4>
    <div class="row g-4">
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

{{-- ============ CTA CAROUSEL ============ --}}
<div x-data="ctaCarousel()" class="mt-5 overflow-hidden position-relative"
     style="background:linear-gradient(269.76deg, #24015b 45.67%, #4b02c1 96.09%); min-height:234px;">
    <div class="container position-relative py-4" style="z-index:2;">
        <template x-for="(slide, i) in slides" :key="i">
            <div x-show="active === i"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="row align-items-center" style="min-height:234px;">
                <div class="col-lg-7">
                    <h2 class="font-logo text-white mb-0" style="font-size:clamp(40px,6vw,96px); line-height:1;" x-text="slide.title"></h2>
                    <div class="d-flex align-items-center gap-2 mt-2 mb-3">
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
                        <span class="text-white fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:20px;" x-text="slide.subtitle"></span>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <a :href="slide.link" class="btn bg-white text-dark fw-semibold px-4 py-2 rounded-pill"
                           style="font-size:20px; font-family:'Mona Sans',sans-serif; box-shadow:0px 4px 4px rgba(0,0,0,0.3);">{{ __('ui.rent_now_cta') }}</a>
                        <a :href="slide.link" class="text-white fw-semibold text-decoration-none position-relative d-inline-block"
                           style="font-size:20px; font-family:'Mona Sans',sans-serif;">
                            {{ __('ui.see_details') }}
                            <span class="d-block position-absolute" style="bottom:-2px; left:0; right:0; height:1px; background:white;"></span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 d-none d-lg-flex justify-content-center position-relative">
                    <div class="text-white opacity-20" style="font-size:180px; transform:rotate(15deg);">&#9670;</div>
                </div>
            </div>
        </template>
    </div>
    <button @click="prev()" class="cta-carousel-arrow position-absolute start-0 top-50 translate-middle-y btn border-0 d-flex align-items-center justify-content-center"
            style="background:rgba(238,226,226,0.72); opacity:0.85; width:48px; height:60px; border-radius:0 100px 100px 0; z-index:3;" aria-label="{{ __('ui.previous_slide') }}">
        <i class="bi bi-chevron-left text-white" style="font-size:28px;"></i>
    </button>
    <button @click="next()" class="cta-carousel-arrow position-absolute end-0 top-50 translate-middle-y btn border-0 d-flex align-items-center justify-content-center"
            style="background:rgba(238,226,226,0.72); opacity:0.85; width:48px; height:60px; border-radius:100px 0 0 100px; z-index:3;" aria-label="{{ __('ui.next_slide') }}">
        <i class="bi bi-chevron-right text-white" style="font-size:28px;"></i>
    </button>
    <div class="d-flex justify-content-center gap-2 position-absolute bottom-0 start-50 translate-middle-x pb-3" style="z-index:3;">
        <template x-for="(dot, i) in slides" :key="'dot-'+i">
            <button @click="goTo(i)" class="border-0 rounded-circle p-0"
                    :class="active === i ? 'bg-white' : 'bg-white opacity-50'"
                    style="width:8px; height:8px; transition:all 0.3s;"></button>
        </template>
    </div>
</div>

{{-- ============ LOAD MORE ============ --}}
<div class="text-center py-5 mb-5">
    <a href="{{ route('products.index') }}" class="btn btn-primary px-5 py-2 rounded-pill fw-bold"
       style="font-family:'Mona Sans',sans-serif; font-size:14px; background:#0031e1; border-color: var(--primary-blue);">
        {{ __('ui.load_more') }}
    </a>
</div>

@endsection

@push('styles')
<style>
    .overflow-auto::-webkit-scrollbar { height: 6px; }
    .overflow-auto::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
    .overflow-auto::-webkit-scrollbar-thumb { background: var(--primary-blue); border-radius: 3px; }
    .cta-carousel-arrow:hover { opacity: 1 !important; background: rgba(238,226,226,0.95) !important; }
</style>
@endpush

@push('scripts')
<script>
    function ctaCarousel() {
        return {
            active: 0,
            slides: [
                { title: @json($featuredProduct ? strtoupper($featuredProduct->title) : 'SONY ZV E10 KIT'), subtitle: @json(__('ui.people_rented', ['count' => '1.2K'])), link: @json($featuredProduct ? route('products.show', $featuredProduct->slug) : '#') },
                { title: 'CANON EOS R50', subtitle: @json(__('ui.people_rented', ['count' => '900+'])), link: @json(route('products.index')) },
                { title: 'DJI MINI 4 PRO', subtitle: @json(__('ui.people_rented', ['count' => '2.1K'])), link: @json(route('products.index')) },
            ],
            next() { this.active = (this.active + 1) % this.slides.length; },
            prev() { this.active = (this.active - 1 + this.slides.length) % this.slides.length; },
            goTo(i) { this.active = i; },
            init() { this._interval = setInterval(() => this.next(), 5000); },
            destroy() { clearInterval(this._interval); },
        };
    }
</script>
@endpush
>>>>>>> origin/feat-peminjam
