{{-- SI-RENT Store Sidebar —” Shared across store tabs --}}
@props(['owner', 'totalRatings' => 0, 'avgRating' => 0, 'completedRentals' => 0])

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
            <span style="font-size:11px; color: var(--text-primary);">{{ $owner->location_city ?? 'Indonesia' }}</span>
        </div>
        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.joined') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ $owner->created_at ? $owner->created_at->format('j F Y') : '9 March 2023' }}</span>
        </div>
        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.avg_rating') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ number_format($avgRating, 1) }}</span>
        </div>
        <div class="mb-2 d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.respon_rate_label') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">98%</span>
        </div>
        <div class="d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.respon_time') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ __('ui.within_minutes') }}</span>
        </div>

        {{-- Stats row --}}
        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
            <div class="text-center">
                <div class="fw-medium" style="font-size:11px;">{{ $owner->products_count ?? 0 }}</div>
                <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.items') }}</div>
            </div>
            <div class="text-center">
                <div class="fw-medium" style="font-size:11px;">683</div>
                <div style="font-size:11px; color: var(--text-muted);">{{ __('ui.followers') }}</div>
            </div>
        </div>
    </div>

    {{-- Shop Policies --}}
    <div class="bg-white rounded-3 border p-4 mb-3">
        <h5 class="fw-bold mb-3" style="font-size:16px;">{{ __('ui.shop_policies') }}</h5>

        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.id_verification') }}</span>
            <span style="font-size:11px; color:#00bc10;">
                <i class="bi bi-check-circle me-1"></i>{{ __('ui.verified') }}
            </span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.security_deposit') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ __('ui.required') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.free_cancelation') }}</span>
            <span style="font-size:11px; color:#00bc10;">{{ __('ui.flexible') }}</span>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
            <span style="font-size:11px; color: var(--text-muted);">{{ __('ui.free_cancelation') }}</span>
            <span style="font-size:11px; color: var(--text-primary);">{{ __('ui.flexible') }}</span>
        </div>
    </div>

    {{-- Trust Score --}}
    <div class="bg-white rounded-3 border p-4 text-center">
        <div class="fw-bold" style="font-size:40px; color: var(--primary-blue);">{{ $trustScore ?? 94 }}</div>
        <div class="fw-semibold" style="font-size:16px;">{{ __('ui.trust_score') }}</div>
        <div class="fw-medium" style="font-size:16px; color: var(--text-secondary);">{{ __('ui.very_trusted') }}</div>
    </div>
</div>
