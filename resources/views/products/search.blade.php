{{-- SI-RENT Search Results —” F-BRW-01 / F-BRW-02 --}}
@extends('layouts.app')

@section('title', request('q') ? '"' . request('q') . '" —” SI-RENT' : 'Browse Products —” SI-RENT')

@section('content')

<div class="container mt-4">
    <div class="row">
        {{-- ============ MOBILE FILTER TOGGLE ============ --}}
        <div class="col-12 d-lg-none mb-3">
            <button class="btn btn-outline-primary w-100"
                    style="border-radius:10px; font-family:'Mona Sans',sans-serif; font-size:14px;"
                    x-data
                    @click="$refs.filterSidebar.classList.toggle('d-none'); $refs.filterSidebar.classList.toggle('d-block');">
                <i class="bi bi-funnel me-1"></i> {{ __('ui.filters') }}
            </button>
        </div>

        {{-- ============ LEFT SIDEBAR: FILTERS ============ --}}
        <div class="col-lg-3 d-none d-lg-block mb-4" x-ref="filterSidebar">
            <x-search.filter-sidebar :locations="$locations" />
        </div>

        {{-- ============ RIGHT AREA: RESULTS ============ --}}
        <div class="col-lg-9">

            {{-- Results Header --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
                <div>
                    @if(request('q'))
                        <h5 class="fw-semibold mb-0" style="font-family:'Mona Sans',sans-serif; font-size:18px; color: var(--text-primary);">
                            Results for "<span class="text-primary">{{ request('q') }}</span>"
                        </h5>
                    @else
                        <h5 class="fw-semibold mb-0" style="font-family:'Mona Sans',sans-serif; font-size:18px; color: var(--text-primary);">
                            {{ __('ui.browse') }}
                        </h5>
                    @endif
                    <p class="mb-0" style="font-family:'Mona Sans',sans-serif; font-size:13px; color: var(--text-tertiary);">
                        {{ $products->total() }} {{ __('ui.items_found', ['count' => $products->total()]) }}
                    </p>
                </div>

                {{-- Sort Dropdown --}}
                <select name="sort" class="form-select"
                        style="width:auto; font-size:13px; border-radius:10px; font-family:'Mona Sans',sans-serif;"
                        onchange="window.location.href='{{ route('products.index') }}?' + new URLSearchParams({...@json(request()->except('sort', 'page')), sort: this.value}).toString()">
                    <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>{{ __('ui.sort_latest') }}</option>
                    <option value="cheapest" {{ request('sort') === 'cheapest' ? 'selected' : '' }}>{{ __('ui.sort_cheapest') }}</option>
                    <option value="most_expensive" {{ request('sort') === 'most_expensive' ? 'selected' : '' }}>{{ __('ui.sort_most_expensive') }}</option>
                    <option value="highest_rated" {{ request('sort') === 'highest_rated' ? 'selected' : '' }}>{{ __('ui.sort_highest_rated') }}</option>
                    <option value="most_rented" {{ request('sort') === 'most_rented' ? 'selected' : '' }}>{{ __('ui.sort_most_rented') }}</option>
                </select>
            </div>

            {{-- Active Filter Tags --}}
            @php
                $activeFilters = collect();
                if (request('q')) $activeFilters->push(['label' => __('ui.search') . ': "' . request('q') . '"', 'except' => ['q']]);
                if (request('location')) $activeFilters->push(['label' => request('location'), 'except' => ['location']]);
                if (request('min_price') || request('max_price'))
                    $activeFilters->push([
                        'label' => 'Rp ' . number_format((int) request('min_price', 0), 0, ',', '.')
                                   . ' —“ Rp ' . number_format((int) request('max_price', 999999999), 0, ',', '.'),
                        'except' => ['min_price', 'max_price']
                    ]);
                if (request('rating')) $activeFilters->push(['label' => request('rating') . '+ ' . __('ui.rented'), 'except' => ['rating']]);
                if (request('verified')) $activeFilters->push(['label' => __('ui.verified_only'), 'except' => ['verified']]);
            @endphp

            @if($activeFilters->isNotEmpty())
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="text-muted" style="font-size:13px; font-family:'Mona Sans',sans-serif;">{{ __('ui.filters') }}:</span>
                    @foreach($activeFilters as $filter)
                        <span class="d-inline-flex align-items-center gap-1 px-3 py-1 rounded-pill"
                              style="background:#ecf2fd; font-size:12px; color: var(--primary-blue); border:1px solid #c2d8ff; font-family:'Mona Sans',sans-serif;">
                            {{ $filter['label'] }}
                            <a href="{{ route('products.index', request()->except($filter['except'])) }}"
                               class="text-decoration-none" style="color: var(--primary-blue); font-size:16px; line-height:1;">&times;</a>
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Product Grid --}}
            <div class="row g-3">
                @forelse($products as $product)
                    <div class="col-6 col-md-4 col-lg-4 col-xl-3 d-flex justify-content-center">
                        <x-product.product-card :product="$product" />
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <div class="text-muted" style="font-family:'Mona Sans',sans-serif;">
                            <i class="bi bi-search d-block mb-3" style="font-size:56px;"></i>
                            <h5 class="fw-semibold mt-3" style="color: var(--text-primary);">{{ __('ui.no_items_found') }}</h5>
                            <p style="color: var(--text-secondary); font-size:14px; max-width:400px; margin:0 auto;">
                                {{ __('ui.try_adjusting_search') }}
                            </p>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-primary px-4 mt-3"
                               style="border-radius:10px; font-family:'Mona Sans',sans-serif;">
                                {{ __('ui.clear_all_filters') }}
                            </a>
                        </div>
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
    </div>
</div>

@endsection
