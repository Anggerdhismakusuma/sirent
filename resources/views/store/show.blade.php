{{-- SI-RENT Store — F-BRW-04, Figma nodes 1764:3219 / 1819:3462 / 1819:4179 --}}
@extends('layouts.app')

@section('title', $owner->name . ' — SI-RENT Store')

@section('content')

@php
    $initialTab = 'items';
    if (request()->route()->getName() === 'store.about') $initialTab = 'about';
    if (request()->route()->getName() === 'store.reviews') $initialTab = 'reviews';
    $storeUrl = route('store.show', $owner->id);
@endphp

<div x-data="{ activeTab: '{{ $initialTab }}' }">

<div class="container mt-4">

    {{-- ============ STORE HEADER ============ --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="d-flex align-items-start gap-4">
                {{-- Avatar --}}
                <x-shared.avatar :imagePath="$owner->avatar" :name="$owner->name" size="lg" />

                <div class="flex-grow-1">
                    {{-- Name + Verified Badge --}}
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <h1 class="fw-semibold mb-0" style="font-family:'Mona Sans',sans-serif; font-size:32px;">
                            {{ $owner->name }}
                        </h1>
                        <x-shared.verified-badge :isVerified="$owner->verification_status === App\Models\User::VERIFICATION_VERIFIED" />
                    </div>

                    {{-- Location + Joined --}}
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <span style="font-family:'Mona Sans',sans-serif; font-size:13px; color:#5c5c5c;">
                            <i class="bi bi-geo-alt me-1" style="color:#204be5;"></i>
                            {{ $owner->location_city ?? 'Indonesia' }}
                        </span>
                        <span style="font-family:'Mona Sans',sans-serif; font-size:13px; color:#5c5c5c;">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ __('ui.joined') }} {{ $owner->created_at ? $owner->created_at->format('F Y') : 'March 2023' }}
                        </span>
                    </div>

                    {{-- Followers --}}
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span style="font-family:'Mona Sans',sans-serif; font-size:13px;">
                            <strong>683</strong> {{ __('ui.followers') }}
                        </span>
                    </div>

                    {{-- Follow + Message Buttons --}}
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary rounded-3 px-4"
                                style="font-family:'Mona Sans',sans-serif; font-size:14px; border-color:#204be5; color:#204be5;">
                            {{ __('ui.follow') }}
                        </button>
                        <button class="btn btn-primary rounded-3 px-4"
                                style="font-family:'Mona Sans',sans-serif; font-size:14px; background:#0031e1; border-color:#0031e1;">
                            {{ __('ui.message') }}
                        </button>
                    </div>
                </div>
            </div>

            {{-- Bio --}}
            <p class="mt-3" style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#5c5c5c; max-width:600px;">
                {{ $owner->bio ?? 'Premium photography, videography, and outdoor gear rental. Trusted equipment for creators, travelers, and professionals.' }}
            </p>
        </div>
    </div>

    {{-- ============ TAB SWITCHER ============ --}}
    <x-store.store-tabs :storeUrl="$storeUrl" />

    {{-- ================================================================ --}}
    {{-- TAB 1: ITEMS (Produk) — Figma node 1764:3219 --}}
    {{-- ================================================================ --}}
    <div x-show="activeTab === 'items'" class="row mt-4">
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold mb-0" style="font-family:'Mona Sans',sans-serif; font-size:20px;">{{ __('ui.all_items') }}</h5>
                <button class="btn btn-outline-secondary rounded-3 px-3"
                        style="font-family:'Mona Sans',sans-serif; font-size:16px; border-color:#c9c9c9;">
                    <i class="bi bi-funnel me-1"></i> {{ __('ui.filter') }}
                </button>
            </div>

            <div class="row g-4">
                @forelse($products as $product)
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2 d-flex justify-content-center">
                        <x-product.product-card :product="$product" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.no_products_listed') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
            @endif
        </div>

        {{-- Right Sidebar — Shop Info (shared across tabs) --}}
        <div class="col-lg-3">
            <x-store.sidebar :owner="$owner" :totalRatings="$totalRatings" :avgRating="$avgRating" :completedRentals="$completedRentals" />
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 2: ABOUT — Figma node 1819:3462 --}}
    {{-- ================================================================ --}}
    <div x-show="activeTab === 'about'" class="row mt-4">
        <div class="col-lg-9">
            {{-- About Description --}}
            <h5 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:16px;">
                {{ __('ui.about_store', ['name' => $owner->name]) }}
            </h5>
            <p style="font-family:'Mona Sans',sans-serif; font-size:14px; color:#5c5c5c; line-height:1.6;">
                {{ $owner->bio ?? 'Premium photography, videography, and outdoor gear rental for creators, travelers, filmmakers, and professionals. We provide trusted, high-quality equipment that is carefully maintained and ready for every project, adventure, or creative journey.' }}
            </p>

            {{-- Our Commitment --}}
            <h5 class="fw-semibold mt-4 mb-3" style="font-family:'Mona Sans',sans-serif; font-size:16px;">
                {{ __('ui.our_commitment') }}
            </h5>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="bg-white rounded-3 border p-3 d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px; height:40px; background:#dde7fc;">
                            <i class="bi bi-shield-check" style="color:#204be5; font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.quality_gear') }}</div>
                            <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#717171;">{{ __('ui.quality_gear_desc') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white rounded-3 border p-3 d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px; height:40px; background:#dde7fc;">
                            <i class="bi bi-tag" style="color:#204be5; font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.affordable_prices') }}</div>
                            <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#717171;">{{ __('ui.affordable_prices_desc') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white rounded-3 border p-3 d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px; height:40px; background:#dde7fc;">
                            <i class="bi bi-lightning-charge" style="color:#204be5; font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.fast_response') }}</div>
                            <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#717171;">{{ __('ui.fast_response_desc') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-white rounded-3 border p-3 d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px; height:40px; background:#dde7fc;">
                            <i class="bi bi-calendar-check" style="color:#204be5; font-size:18px;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.easy_flexible') }}</div>
                            <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#717171;">{{ __('ui.easy_flexible_desc') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Our Mission --}}
            <h5 class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.our_mission') }}</h5>
            <p style="font-family:'Mona Sans',sans-serif; font-size:12px; font-style:italic; color:#5c5c5c;">
                {{ __('ui.our_mission_text') }}
            </p>

            {{-- Need Help? --}}
            <div class="bg-white rounded-3 border p-4 mt-3" style="border-color:#204be5;">
                <h5 class="fw-semibold mb-1" style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.need_help') }}</h5>
                <p class="mb-0" style="font-family:'Mona Sans',sans-serif; font-size:12px; color:#5c5c5c;">
                    {{ __('ui.need_help_desc') }}
                </p>
            </div>
        </div>

        {{-- Right Sidebar --}}
        <div class="col-lg-3">
            <x-store.sidebar :owner="$owner" :totalRatings="$totalRatings" :avgRating="$avgRating" :completedRentals="$completedRentals" />
        </div>
    </div>

    {{-- ================================================================ --}}
    {{-- TAB 3: REVIEWS — Figma node 1819:4179 --}}
    {{-- ================================================================ --}}
    <div x-show="activeTab === 'reviews'" class="row mt-4">
        <div class="col-lg-9">
            {{-- Rating Overview --}}
            <h5 class="fw-bold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.items_rating_overview') }}</h5>

            <div class="row mb-4">
                <div class="col-md-4 text-center">
                    <div class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:48px; color:#000;">
                        {{ number_format($avgRating, 1) }}
                    </div>
                    <x-shared.rating-badge :score="$avgRating" :totalReviews="$totalRatings" />
                </div>
                <div class="col-md-8">
                    @foreach($ratingDistribution as $star => $count)
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span style="font-family:'Mona Sans',sans-serif; font-size:11px; width:12px;">{{ $star }}</span>
                            <div class="flex-grow-1 bg-light rounded-pill" style="height:8px;">
                                <div class="bg-warning rounded-pill" style="height:8px; width:{{ $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0 }}%;"></div>
                            </div>
                            <span style="font-family:'Mona Sans',sans-serif; font-size:11px; width:50px; text-align:right;">{{ number_format($count) }}</span>
                            <span style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#9092a1; width:45px;">
                                ({{ $totalRatings > 0 ? round(($count / $totalRatings) * 100, 1) : 0 }}%)
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Search + Filters --}}
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <div class="position-relative flex-grow-1" style="max-width:300px;">
                    <input type="text" class="form-control rounded-3" placeholder="{{ __('ui.search_reviews') }}"
                           style="font-family:'Mona Sans',sans-serif; font-size:13px;">
                    <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted"
                       style="right:12px;"></i>
                </div>
                <select class="form-select rounded-3" style="width:auto; font-family:'Mona Sans',sans-serif; font-size:13px;">
                    <option>{{ __('ui.all_rating') }}</option>
                    <option>{{ __('ui.stars_5') }}</option>
                    <option>{{ __('ui.stars_4') }}</option>
                    <option>{{ __('ui.stars_3') }}</option>
                    <option>{{ __('ui.stars_2') }}</option>
                    <option>{{ __('ui.stars_1') }}</option>
                </select>
                <select class="form-select rounded-3" style="width:auto; font-family:'Mona Sans',sans-serif; font-size:13px;">
                    <option>{{ __('ui.all_items') }}</option>
                </select>
                <select class="form-select rounded-3" style="width:auto; font-family:'Mona Sans',sans-serif; font-size:13px;">
                    <option>{{ __('ui.all_time') }}</option>
                </select>
            </div>

            {{-- Review Cards --}}
            @forelse($reviews as $rating)
                <div class="bg-white rounded-3 border p-3 mb-3">
                    <div class="d-flex gap-3">
                        <x-shared.avatar :imagePath="$rating->rater->avatar ?? null" :name="$rating->rater->name ?? 'User'" size="sm" />
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                                    {{ $rating->rater->name ?? __('ui.anonymous') }}
                                </span>
                                <div class="d-flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $rating->score ? 'bi-star-fill' : 'bi-star' }}"
                                           style="font-size:10px; color:{{ $i <= $rating->score ? '#FFB800' : '#d9d9d9' }};"></i>
                                    @endfor
                                </div>
                                <span class="text-muted" style="font-size:10px;">
                                    {{ $rating->created_at ? $rating->created_at->diffForHumans() : '' }}
                                </span>
                            </div>
                            <p class="mb-0" style="font-family:'Mona Sans',sans-serif; font-size:11px; color:#5c5c5c;">
                                {{ $rating->review }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-muted" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.no_reviews_store') }}</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($reviews->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $reviews->links() }}
            </div>
            @endif

            {{-- Verified Rentals banner --}}
            <div class="bg-white rounded-3 border p-4 mt-3 d-flex align-items-center gap-3">
                <i class="bi bi-shield-check" style="font-size:24px; color:#204be5;"></i>
                <div>
                    <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.only_verified_rentals') }}</div>
                    <div style="font-family:'Mona Sans',sans-serif; font-size:12px; color:#717171;">{{ __('ui.verified_rentals_desc') }}</div>
                </div>
            </div>

            {{-- Write a review CTA --}}
            <div class="bg-white rounded-3 border p-4 mt-3 d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.share_experience') }}</div>
                    <div style="font-family:'Mona Sans',sans-serif; font-size:12px; color:#717171;">
                        {{ __('ui.share_experience_desc', ['name' => $owner->name]) }}
                    </div>
                </div>
                <button class="btn btn-outline-primary rounded-3 px-4"
                        style="font-family:'Mona Sans',sans-serif; font-size:14px; border-color:#204be5; color:#204be5;">
                    {{ __('ui.write_review') }}
                </button>
            </div>
        </div>

        <div class="col-lg-3">
            <x-store.sidebar :owner="$owner" :totalRatings="$totalRatings" :avgRating="$avgRating" :completedRentals="$completedRentals" />
        </div>
    </div>

</div>

</div>{{-- End x-data --}}

@endsection
