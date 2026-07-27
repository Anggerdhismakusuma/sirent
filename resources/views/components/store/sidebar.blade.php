{{-- SI-RENT Store Sidebar — Shared across store tabs --}}
@props([
    'owner',
    'totalRatings' => 0,
    'avgRating' => 0,
    'completedRentals' => 0,
    'trustScore' => 0,
    'responseRate' => null,
    'avgResponseMinutes' => null,
    'storeLocation' => null,
])

<div style="font-family:'Mona Sans',sans-serif;">
    {{-- Shop Information --}}
    <div class="bg-white rounded-3 border p-4 mb-3">
        <h5 class="fw-bold mb-3" style="font-size:16px;">{{ __('ui.shop_information') }}</h5>

        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.owner') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ $owner->name }}</span>
        </div>
        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.location') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ $storeLocation ?? __('ui.indonesia') }}</span>
        </div>
        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.joined') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ $owner->created_at->translatedFormat('j F Y') }}</span>
        </div>
        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.avg_rating') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ number_format($avgRating, 1) }}</span>
        </div>
        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.respon_rate_label') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">
                {{ $responseRate !== null ? $responseRate . '%' : '—' }}
            </span>
        </div>
        <div class="d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.respon_time') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">
                @if($avgResponseMinutes !== null)
                    @if($avgResponseMinutes < 60)
                        {{ __('ui.within_minutes') }}
                    @elseif($avgResponseMinutes < 120)
                        {{ __('ui.within_hour') }}
                    @elseif($avgResponseMinutes < 1440)
                        {{ __('ui.within_hours', ['count' => round($avgResponseMinutes / 60)]) }}
                    @else
                        {{ __('ui.within_days', ['count' => round($avgResponseMinutes / 1440)]) }}
                    @endif
                @else
                    —
                @endif
            </span>
        </div>

        {{-- Stats row --}}
        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
            <div class="text-center">
                <div class="fw-medium" style="font-size:11px;">{{ $owner->products_count ?? 0 }}</div>
                <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.items') }}</div>
            </div>
            <div class="text-center">
                <div class="fw-medium" style="font-size:11px;">{{ number_format($owner->followers_count ?? 0) }}</div>
                <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.followers') }}</div>
            </div>
            <div class="text-center">
                <div class="fw-medium" style="font-size:11px;">{{ number_format($completedRentals) }}</div>
                <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.rentals') }}</div>
            </div>
        </div>
    </div>

    {{-- Shop Policies --}}
    <div class="bg-white rounded-3 border p-4 mb-3">
        <h5 class="fw-bold mb-3" style="font-size:16px;">{{ __('ui.shop_policies') }}</h5>

        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.id_verification') }}</span>
            <span style="font-size:11px; color:{{ $owner->isVerified() ? '#00bc10' : '#dc3545' }};">
                <i class="bi {{ $owner->isVerified() ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                {{ $owner->isVerified() ? __('ui.verified') : __('ui.unverified') }}
            </span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.security_deposit') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ __('ui.required') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.cancellation_policy') }}</span>
            <span style="font-size:11px; color:#00bc10;">{{ __('ui.flexible') }}</span>
        </div>
    </div>

    {{-- Trust Score --}}
    <div class="bg-white rounded-3 border p-4 text-center">
        <div class="fw-bold" style="font-size:40px; color: var(--primary-blue);">{{ $trustScore }}</div>
        <div class="fw-semibold" style="font-size:16px;">{{ __('ui.trust_score') }}</div>
        <div class="fw-medium" style="font-size:16px; color: var(--text-secondary);">
            @if($trustScore >= 80)
                {{ __('ui.very_trusted') }}
            @elseif($trustScore >= 60)
                {{ __('ui.trusted') }}
            @elseif($trustScore >= 40)
                {{ __('ui.fairly_trusted') }}
            @else
                {{ __('ui.new_store') }}
            @endif
        </div>
    </div>
</div>
