{{-- SI-RENT Search Filter Sidebar --}}
@props(['locations' => []])

<form method="GET" action="{{ route('products.index') }}"
      x-data
      x-on:change.debounce.300ms="$el.submit()"
      class="bg-white rounded-4 p-4 shadow-sm border" style="border-color: var(--border-default);">

    {{-- 1. Search Keyword --}}
    <div class="mb-4">
        <label class="fw-semibold mb-2 d-block" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
            {{ __('ui.search') }}
        </label>
        <input type="text" name="q" class="form-control" placeholder="{{ __('ui.find_items_placeholder') }}"
               value="{{ request('q') }}"
               style="border-radius:10px; font-size:13px; font-family:'Mona Sans',sans-serif;">
    </div>

    {{-- 2. Owner Verification --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
            {{ __('ui.owner_verification') }}
        </h6>
        <div class="form-check">
            <input type="checkbox" name="verified" value="1" class="form-check-input"
                   id="filter-verified" {{ request('verified') ? 'checked' : '' }}
                   style="cursor:pointer;">
            <label class="form-check-label" for="filter-verified"
                   style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary); cursor:pointer;">
                {{ __('ui.verified_only') }}
            </label>
        </div>
    </div>

    {{-- 3. Location --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
            {{ __('ui.location') }}
        </h6>
        @if($locations->isNotEmpty())
            <select name="location" class="form-select"
                    style="font-size:13px; border-radius:10px; font-family:'Mona Sans',sans-serif;">
                <option value="">{{ __('ui.all_locations') }}</option>
                @foreach($locations as $city)
                    <option value="{{ $city }}" {{ request('location') === $city ? 'selected' : '' }}>
                        {{ $city }}
                    </option>
                @endforeach
            </select>
        @else
            <p class="text-muted mb-0" style="font-size:12px;">{{ __('ui.no_locations') }}</p>
        @endif
    </div>

    {{-- 4. Price Range --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
            {{ __('ui.price_per_day') }}
        </h6>
        <div class="row g-2">
            <div class="col-6">
                <input type="number" name="min_price" class="form-control" placeholder="{{ __('ui.min') }}"
                       value="{{ request('min_price') }}"
                       style="font-size:13px; border-radius:10px; font-family:'Mona Sans',sans-serif;">
            </div>
            <div class="col-6">
                <input type="number" name="max_price" class="form-control" placeholder="{{ __('ui.max') }}"
                       value="{{ request('max_price') }}"
                       style="font-size:13px; border-radius:10px; font-family:'Mona Sans',sans-serif;">
            </div>
        </div>
    </div>

    {{-- 5. Minimum Rating --}}
    <div class="mb-4">
        <h6 class="fw-semibold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
            {{ __('ui.minimum_rating') }}
        </h6>
        @foreach([4, 3, 2, 1] as $star)
            <div class="form-check">
                <input type="radio" name="rating" value="{{ $star }}" class="form-check-input"
                       id="rating-{{ $star }}"
                       {{ (int) request('rating') === $star ? 'checked' : '' }}
                       style="cursor:pointer;">
                <label class="form-check-label" for="rating-{{ $star }}"
                       style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary); cursor:pointer;">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star-fill" style="font-size:10px; color:{{ $i <= $star ? '#FFB800' : '#d9d9d9' }};"></i>
                    @endfor
                    {{ __('ui.and_up') }}
                </label>
            </div>
        @endforeach
        <div class="form-check">
            <input type="radio" name="rating" value="" class="form-check-input"
                   id="rating-any" {{ !request('rating') ? 'checked' : '' }}
                   style="cursor:pointer;">
            <label class="form-check-label" for="rating-any"
                   style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-primary); cursor:pointer;">
                {{ __('ui.any_rating') }}
            </label>
        </div>
    </div>

    {{-- 6. Action Buttons --}}
    <button type="submit" class="btn btn-primary w-100 mb-2"
            style="border-radius:10px; font-size:14px; font-family:'Mona Sans',sans-serif; background-color: var(--primary-blue); border-color: var(--primary-blue);">
        {{ __('ui.apply_filters') }}
    </button>
    @if(count(request()->except('page')) > 0)
        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100"
           style="border-radius:10px; font-size:14px; font-family:'Mona Sans',sans-serif;">
            {{ __('ui.reset_all') }}
        </a>
    @endif
</form>
