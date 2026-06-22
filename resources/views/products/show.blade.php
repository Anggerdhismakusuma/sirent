{{-- SI-RENT Product Detail — F-BRW-03, Figma node 1686:3959 --}}
@extends('layouts.app')

@section('title', $product->title . ' — SI-RENT')

@section('content')

{{-- Back Button --}}
<div class="container mt-3">
    <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('home') }}"
       class="btn btn-link text-decoration-none d-inline-flex align-items-center gap-1 p-0"
       style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--primary-blue-light);">
        <i class="bi bi-chevron-left" style="font-size:20px;"></i> {{ __('ui.back') }}
    </a>
</div>

{{-- ============ MAIN CONTENT ROW ============ --}}
<div class="container mt-3">
    <div class="row g-4">

        {{-- LEFT: Image Gallery --}}
        <div class="col-lg-5">
            <div class="d-flex gap-3">
                {{-- Main Image --}}
                <div class="flex-grow-1 bg-light rounded-4 overflow-hidden d-flex align-items-center justify-content-center"
                     style="max-width:472px; aspect-ratio:1;">
                    @if($product->primaryImage)
                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                             alt="{{ $product->title }}"
                             class="w-100 h-100 object-fit-cover">
                    @else
                        <i class="bi bi-image text-muted" style="font-size:80px;"></i>
                    @endif
                </div>

                {{-- Thumbnail Column --}}
                <div class="d-flex flex-column gap-2">
                    @php $allImages = $product->images->sortBy('sort_order'); @endphp
                    @forelse($allImages->take(4) as $image)
                        <div class="bg-light rounded-3 overflow-hidden"
                             style="width:85px; height:85px; border:2px solid transparent;">
                            <img src="{{ asset('storage/' . $image->image_path) }}"
                                 alt="Thumbnail"
                                 class="w-100 h-100 object-fit-cover">
                        </div>
                    @empty
                        @for($i = 0; $i < 4; $i++)
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                 style="width:85px; height:85px;">
                                <i class="bi bi-image text-muted" style="font-size:24px;"></i>
                            </div>
                        @endfor
                    @endforelse

                    {{-- +N overlay on last thumbnail or show-all --}}
                    @if($allImages->count() > 5)
                        <div class="bg-white rounded-3 d-flex flex-column align-items-center justify-content-center position-relative overflow-hidden"
                             style="width:85px; height:85px; border:1px solid #e5e5e5;">
                            <span style="font-family:'Mona Sans',sans-serif; font-size:20px; color: var(--primary-blue-light);">+{{ $allImages->count() - 4 }}</span>
                            <span style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--primary-blue-light);">{{ __('ui.see_all') }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- CENTER: Product Info --}}
        <div class="col-lg-4">
            {{-- Recommended badge --}}
            <span class="d-inline-block px-3 py-1 rounded-pill mb-2"
                  style="background:#dde7fc; font-family:'Mona Sans',sans-serif; font-size:12px; color: var(--primary-blue-light);">
                <i class="bi bi-hand-thumbs-up me-1"></i>{{ __('ui.recommended') }}
            </span>

            {{-- Title --}}
            <h1 class="fw-bold mb-1" style="font-family:'Mona Sans',sans-serif; font-size:32px; color: var(--text-primary);">
                {{ $product->title }}
            </h1>

            {{-- Category --}}
            <p class="mb-2" style="font-family:'Mona Sans',sans-serif; font-size:20px; color: var(--text-primary);">
                {{ $product->category->name ?? __('ui.general') }}
            </p>

            {{-- Location --}}
            <div class="d-flex align-items-center gap-1 mb-2">
                <i class="bi bi-geo-alt" style="color: var(--primary-blue-light); font-size:16px;"></i>
                <span style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--primary-blue-light);">
                    {{ $product->location_city }}
                </span>
            </div>

            {{-- Rating --}}
            <div class="mb-3">
                <x-shared.rating-badge :score="$product->rating_avg" :totalReviews="$product->total_rented" />
            </div>

            {{-- Condition Checklist --}}
            <div class="mb-4" style="font-family:'Mona Sans',sans-serif;">
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:35px; height:35px; background:#dde7fc;">
                        <i class="bi bi-clipboard-check" style="color: var(--primary-blue-light); font-size:16px;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:14px; color: var(--text-primary);">{{ ucwords(str_replace('_', ' ', $product->condition)) }} {{ __('ui.condition') }}</div>
                        <div style="font-size:11px; color: var(--text-secondary);">{{ __('ui.condition_great_shape') }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-2 mb-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:35px; height:35px; background:#dde7fc;">
                        <i class="bi bi-box-seam" style="color: var(--primary-blue-light); font-size:16px;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:14px; color: var(--text-primary);">{{ __('ui.include_accessories') }}</div>
                        <div style="font-size:11px; color: var(--text-secondary);">Battery, charger, strap, and camera bag</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:35px; height:35px; background:#dde7fc;">
                        <i class="bi bi-truck" style="color: var(--primary-blue-light); font-size:16px;"></i>
                    </div>
                    <div>
                        <div class="fw-semibold" style="font-size:14px; color: var(--text-primary);">{{ __('ui.pickup_or_delivery') }}</div>
                        <div style="font-size:11px; color: var(--text-secondary);">{{ __('ui.pickup_delivery_desc', ['city' => $product->location_city]) }}</div>
                    </div>
                </div>
            </div>

            {{-- Owner Info Card --}}
            <div class="bg-white rounded-3 border p-3" style="border-color:#dfdfdf;">
                <div class="d-flex align-items-center gap-3 mb-2">
                    <x-shared.avatar :imagePath="$product->owner->avatar" :name="$product->owner->name" size="md" />
                    <div>
                        <div class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                            <a href="{{ route('store.show', $product->owner_id) }}" class="text-decoration-none text-dark">
                                {{ $product->owner->name }}
                            </a>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <x-shared.verified-badge :isVerified="$product->owner->verification_status === App\Models\User::VERIFICATION_VERIFIED" />
                            <span style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--text-secondary);">
                                {{ __('ui.years_on_sirent', ['count' => 3]) }}
                            </span>
                            <span class="text-muted">·</span>
                            <span style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--text-grey-light);">
                                {{ __('ui.respon_rate', ['rate' => 98]) }}
                            </span>
                        </div>
                    </div>
                </div>
                @auth
                    <button class="btn btn-outline-primary w-100 rounded-3"
                            style="font-family:'Mona Sans',sans-serif; font-size:14px; font-weight:600; border-color:#3058e6; color: var(--primary-blue-light);"
                            x-data
                            @click="
                                $el.disabled = true;
                                $el.innerHTML = '<span class=\'spinner-border spinner-border-sm me-1\' role=\'status\'></span>{{ __('ui.starting_chat') }}';
                                fetch('{{ route('chat.start', $product->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.content || '',
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                })
                                .then(r => r.json())
                                .then(data => {
                                    if (data.success) {
                                        window.location.href = data.data.redirect_url;
                                    } else {
                                        Swal.fire({ icon: 'error', title: 'Oops...', text: data.message || '{{ __('ui.failed_start_chat') }}', confirmButtonColor: '#0031e1' });
                                        $el.disabled = false;
                                        $el.textContent = '{{ __('ui.chat_with_owner') }}';
                                    }
                                })
                                .catch(() => {
                                    Swal.fire({ icon: 'error', title: 'Oops...', text: '{{ __('ui.error_try_again') }}', confirmButtonColor: '#0031e1' });
                                    $el.disabled = false;
                                    $el.textContent = '{{ __('ui.chat_with_owner') }}';
                                });
                            ">
                        {{ __('ui.chat_with_owner') }}
                    </button>
                @else
                    <button class="btn btn-outline-primary w-100 rounded-3"
                            style="font-family:'Mona Sans',sans-serif; font-size:14px; font-weight:600; border-color:#3058e6; color: var(--primary-blue-light);"
                            onclick="window.dispatchEvent(new CustomEvent('open-auth-modal', {detail:{mode:'login'}}))">
                        {{ __('ui.chat_with_owner') }}
                    </button>
                @endauth
            </div>
        </div>

        {{-- RIGHT: Booking Card --}}
        <div class="col-lg-3" x-data="bookingCard({{ $product->price_per_day }}, {{ $product->id }})"
             @dates-selected="onDatesSelected($event)">
            <div class="bg-white rounded-4 shadow-sm p-4" style="border: 1px solid var(--border-subtle);">
                {{-- Price --}}
                <div class="mb-3">
                    <span class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:25px; color: var(--primary-blue);">
                        Rp {{ number_format($product->price_per_day, 0, ',', '.') }}
                    </span>
                    <span style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);"> {{ __('ui.per_day') }}</span>
                    <div style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-grey-light);">{{ __('ui.minimum_rent') }}</div>
                </div>

                {{-- Rental Period — Date Range Picker --}}
                <div class="mb-3">
                    <div class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">{{ __('ui.rental_period') }}</div>
                    <x-product.date-range-picker :blockedDates="$blockedDates" :productId="$product->id" />
                </div>

                {{-- Duration --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">{{ __('ui.duration') }}</span>
                    <span style="font-family:'Mona Sans',sans-serif; font-size:14px;" x-text="durationText">—</span>
                </div>

                {{-- Quantity --}}
                <div class="mb-3">
                    <div class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">{{ __('ui.quantity') }}</div>
                    <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-3 py-2"
                         style="background:#f0f0f0; height:40px;">
                        <button class="btn btn-link text-dark text-decoration-none p-0"
                                style="font-size:18px; cursor:pointer;"
                                @click="quantity = Math.max(1, quantity - 1); recalc()"
                                :class="quantity <= 1 ? 'opacity-25' : ''"
                                :disabled="quantity <= 1"
                                aria-label="Kurangi jumlah">−</button>
                        <span style="font-family:'Mona Sans',sans-serif; font-size:14px;"
                              x-text="quantity + ' ' + (window.SIRENT_TRANSLATIONS ? window.SIRENT_TRANSLATIONS.unit : '{{ __('ui.unit') }}')"></span>
                        <button class="btn btn-link text-dark text-decoration-none p-0"
                                style="font-size:18px; cursor:pointer;"
                                @click="quantity++; recalc()"
                                aria-label="Tambah jumlah">+</button>
                    </div>
                </div>

                {{-- Divider --}}
                <hr style="border-color: var(--border-light);">

                {{-- Price Breakdown --}}
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
                        Rp <span x-text="pricePerDayFormatted">{{ number_format($product->price_per_day, 0, ',', '.') }}</span> x <span x-text="totalDays">0</span> {{ __('ui.days') }}
                    </span>
                    <span style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
                        Rp <span x-text="subtotalFormatted">0</span>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-grey-light);">{{ __('ui.service_fee') }}</span>
                    <span style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">Rp {{ number_format(2000, 0, ',', '.') }}</span>
                </div>

                {{-- Divider --}}
                <hr style="border-color: var(--border-light);">

                {{-- Total --}}
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:16px; color: var(--text-primary);">{{ __('ui.total') }}</span>
                    <span class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:20px; color: var(--primary-blue);">
                        Rp <span x-text="totalFormatted">0</span>
                    </span>
                </div>

                {{-- Rent Now Button or Verification Notice --}}
                @auth
                    @if(auth()->user()->verification_status !== \App\Models\User::VERIFICATION_VERIFIED)
                        <div class="text-center mb-3 p-3 rounded-3" style="background: #fff3cd; border: 1px solid #ffc107;">
                            <i class="bi bi-shield-exclamation d-block mb-1" style="font-size:24px; color: #856404;"></i>
                            <p class="mb-0 fw-medium" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: #856404;">
                                @if(auth()->user()->verification_status === \App\Models\User::VERIFICATION_UNVERIFIED)
                                    {{ __('ui.rental_restricted_unverified') }}
                                @else
                                    {{ __('ui.waiting_verification') }}
                                @endif
                            </p>
                        </div>
                    @else
                        <input type="hidden" id="_csrf" value="{{ csrf_token() }}">
                        <button class="btn w-100 text-white fw-medium rounded-3 mb-3"
                                :disabled="!canSubmit || submitting"
                                :style="canSubmit ? 'background:#0031e1;' : 'background:#a0b4f0;'"
                                style="font-family:'Mona Sans',sans-serif; font-size:14px; height:40px;"
                                @click="submitRental">
                            <span x-show="!submitting">{{ __('ui.rent_now_button') }}</span>
                            <span x-show="submitting">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                                {{ __('ui.processing') }}
                            </span>
                        </button>
                    @endif
                @else
                    <input type="hidden" id="_csrf" value="{{ csrf_token() }}">
                    <button class="btn w-100 text-white fw-medium rounded-3 mb-3"
                            :disabled="!canSubmit || submitting"
                            :style="canSubmit ? 'background:#0031e1;' : 'background:#a0b4f0;'"
                            style="font-family:'Mona Sans',sans-serif; font-size:14px; height:40px;"
                            @click="submitRental">
                        <span x-show="!submitting">{{ __('ui.rent_now_button') }}</span>
                        <span x-show="submitting">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            {{ __('ui.processing') }}
                        </span>
                    </button>
                @endauth

                {{-- Trust Badges (compact) --}}
                <div class="d-flex gap-2 mb-3" style="font-family:'Mona Sans',sans-serif; font-size:10px;">
                    <div class="text-center">
                        <i class="bi bi-shield-check d-block" style="color: var(--primary-blue-light); font-size:18px;"></i>
                        <div class="fw-medium" style="color: var(--text-primary);">{{ __('ui.free_cancellation') }}</div>
                        <div style="color: var(--text-grey-dark);">{{ __('ui.before_1_day') }}</div>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-hand-thumbs-up d-block" style="color: var(--primary-blue-light); font-size:18px;"></i>
                        <div class="fw-medium" style="color: var(--text-primary);">{{ __('ui.secure_payment') }}</div>
                        <div style="color: var(--text-grey-dark);">{{ __('ui.protected_100') }}</div>
                    </div>
                    <div class="text-center">
                        <i class="bi bi-headset d-block" style="color: var(--primary-blue-light); font-size:18px;"></i>
                        <div class="fw-medium" style="color: var(--text-primary);">{{ __('ui.support_24_7') }}</div>
                        <div style="color: var(--text-grey-dark);">{{ __('ui.were_here_to_help') }}</div>
                    </div>
                </div>

                {{-- Tips for Renters --}}
                <div class="rounded-3 p-3 position-relative" style="background:#ecf2fd;">
                    <div class="fw-semibold mb-1" style="font-family:'Mona Sans',sans-serif; font-size:16px; color:#001d83;">{{ __('ui.tips_for_renters') }}</div>
                    <p class="mb-0" style="font-family:'Mona Sans',sans-serif; font-size:12px; color: var(--text-primary);">
                        {{ __('ui.tips_text') }}
                    </p>
                    {{-- Decorative sparkle icon (top right) --}}
                    <div class="position-absolute" style="top:10px; right:15px; color:#6697ef; font-size:14px;">✦</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============ BOTTOM SECTION: About + Reviews ============ --}}
<div class="container mt-5">
    <div class="row">
        <div class="col-lg-8">

            {{-- Tabs --}}
            <ul class="nav nav-tabs border-0 mb-3" style="font-family:'Mona Sans',sans-serif;">
                <li class="nav-item">
                    <a class="nav-link active border-0 fw-bold" href="#" style="font-size:16px; color: var(--text-primary);">{{ __('ui.about_this_item') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link border-0 fw-bold" href="#" style="font-size:16px; color: var(--text-primary);">{{ __('ui.reviews_count', ['count' => $reviews->count()]) }}</a>
                </li>
                <li class="nav-item ms-auto">
                    <a class="nav-link border-0 fw-semibold" href="#" style="font-size:11px; color: var(--primary-blue-light);">
                        {{ __('ui.see_more') }} <i class="bi bi-chevron-right" style="font-size:10px;"></i>
                    </a>
                </li>
            </ul>

            {{-- About Content --}}
            <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--text-secondary); line-height:1.6;">
                <p>{{ $product->description }}</p>
            </div>

            {{-- Specs Row --}}
            <div class="d-flex gap-5 mt-3 mb-4" style="font-family:'Mona Sans',sans-serif;">
                @if($product->category && $product->category->name === 'Kamera')
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-camera" style="font-size:24px; color: var(--text-primary);"></i>
                    <div>
                        <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.megapixels') }}</div>
                        <div class="fw-semibold" style="font-size:14px; color: var(--text-primary);">20 MP</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-camera-video" style="font-size:24px; color: var(--text-primary);"></i>
                    <div>
                        <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.video') }}</div>
                        <div class="fw-semibold" style="font-size:14px; color: var(--text-primary);">4k 60fps</div>
                    </div>
                </div>
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-box" style="font-size:24px; color: var(--text-primary);"></i>
                    <div>
                        <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.weight') }}</div>
                        <div class="fw-semibold" style="font-size:14px; color: var(--text-primary);">680 g</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Divider --}}
            <hr style="border-color: var(--border-light);">

            {{-- Reviews Section --}}
            <h5 class="fw-bold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:16px;">{{ __('ui.reviews_count', ['count' => $reviews->count()]) }}</h5>

            @forelse($reviews as $rating)
                <div class="d-flex gap-3 mb-4">
                    {{-- Reviewer avatar --}}
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
                        <p class="mb-0" style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--text-secondary);">
                            {{ $rating->review }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <p class="text-muted" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        {{ __('ui.no_reviews') }}
                    </p>
                </div>
            @endforelse

            @if($reviews->count() > 0)
                <a href="#" class="fw-semibold text-decoration-none" style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--primary-blue-light);">
                    {{ __('ui.see_all_reviews') }} <i class="bi bi-arrow-right" style="font-size:10px;"></i>
                </a>
            @endif
        </div>
    </div>
</div>

{{-- ============ RECOMMENDED PRODUCTS ============ --}}
@if($recommended->count() > 0)
<div class="container mt-5 mb-5">
    <h4 class="fw-semibold mb-3" style="font-family:'Mona Sans',sans-serif; font-size:22px;">{{ __('ui.you_might_like') }}</h4>
    <div class="row g-4">
        @foreach($recommended as $recProduct)
            <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center">
                <x-product.product-card :product="$recProduct" />
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .object-fit-cover {
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script>
    // Translations for Alpine JS components
    window.SIRENT_TRANSLATIONS = window.SIRENT_TRANSLATIONS || {};
    Object.assign(window.SIRENT_TRANSLATIONS, {
        day: '{{ __('ui.per_day') }}',
        days: '{{ __('ui.days') }}',
        unit: '{{ __('ui.unit') }}',
    });

    function bookingCard(pricePerDay, productId) {
        return {
            pricePerDay: pricePerDay,
            productId: productId,
            startDate: null,
            endDate: null,
            startDateText: '',
            returnDateText: '',
            totalDays: 0,
            quantity: 1,
            subtotal: 0,
            total: 0,
            serviceFee: 2000,
            canSubmit: false,
            submitting: false,

            get pricePerDayFormatted() {
                return new Intl.NumberFormat('id-ID').format(this.pricePerDay);
            },

            get subtotalFormatted() {
                return new Intl.NumberFormat('id-ID').format(this.subtotal);
            },

            get totalFormatted() {
                return new Intl.NumberFormat('id-ID').format(this.total);
            },

            get durationText() {
                if (this.totalDays <= 0) return '—';
                var label = this.totalDays > 1
                    ? (window.SIRENT_TRANSLATIONS && window.SIRENT_TRANSLATIONS.days || 'days')
                    : (window.SIRENT_TRANSLATIONS && window.SIRENT_TRANSLATIONS.day || 'day');
                return this.totalDays + ' ' + label;
            },

            recalc() {
                if (this.totalDays > 0) {
                    this.subtotal = this.pricePerDay * this.totalDays * this.quantity;
                    this.total = this.subtotal + this.serviceFee;
                    this.canSubmit = true;
                } else {
                    this.resetCalc();
                }
            },

            onDatesSelected(event) {
                const detail = event.detail;
                this.startDateText = detail.startDateText;
                this.returnDateText = detail.returnDateText;
                this.startDate = this.parseDate(detail.startDateText);
                this.endDate = this.parseDate(detail.returnDateText);

                if (this.startDate && this.endDate) {
                    const diffTime = this.endDate.getTime() - this.startDate.getTime();
                    this.totalDays = Math.max(1, Math.round(diffTime / (1000 * 3600 * 24)) + 1);
                    this.recalc();
                } else {
                    this.resetCalc();
                }
            },

            parseDate(dateStr) {
                if (!dateStr) return null;
                const months = {
                    'January': 0, 'February': 1, 'March': 2, 'April': 3, 'May': 4, 'June': 5,
                    'July': 6, 'August': 7, 'September': 8, 'October': 9, 'November': 10, 'December': 11
                };
                const parts = dateStr.split(' ');
                if (parts.length !== 3) return null;
                const day = parseInt(parts[0]);
                const month = months[parts[1]];
                const year = parseInt(parts[2]);
                if (month === undefined || isNaN(day) || isNaN(year)) return null;
                return new Date(year, month, day);
            },

            resetCalc() {
                this.totalDays = 0;
                this.subtotal = 0;
                this.total = 0;
                this.canSubmit = false;
            },

            async submitRental() {
                if (!this.canSubmit || this.submitting) return;

                this.submitting = true;
                const csrf = document.getElementById('_csrf').value;

                try {
                    const res = await fetch('{{ route("rentals.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: this.productId,
                            start_date: this.startDate.toISOString().split('T')[0],
                            end_date: this.endDate.toISOString().split('T')[0],
                            quantity: this.quantity,
                        }),
                    });

                    const data = await res.json();

                    if (res.status === 401 || res.status === 419) {
                        // Not authenticated — trigger login modal
                        window.dispatchEvent(new CustomEvent('open-auth-modal', { detail: { mode: 'login' } }));
                        return;
                    }

                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Success', text: data.message, confirmButtonColor: '#0031e1' }).then(() => {
                            window.location.href = '{{ route("borrower.dashboard") }}#activity';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Oops...', text: data.message || '{{ __('ui.rental_failed') }}', confirmButtonColor: '#0031e1' });
                    }
                } catch (err) {
                    Swal.fire({ icon: 'error', title: 'Network Error', text: '{{ __('ui.network_error') }}', confirmButtonColor: '#0031e1' });
                } finally {
                    this.submitting = false;
                }
            },
        };
    }
</script>
@endpush
