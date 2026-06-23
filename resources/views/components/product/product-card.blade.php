{{-- SI-RENT Product Card —” 188Ã—265px --}}
@props(['product' => null])

@if($product)
<a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none">
    <div class="bg-white rounded-3 shadow-sm overflow-hidden" style="width:188px; height:280px; box-shadow:0px 4px 6px 0px rgba(0,0,0,0.2);">
        {{-- Product Image --}}
        <div class="overflow-hidden" style="height:190px; border-radius:10px 10px 0 0;">
            @if($product->primaryImage)
                <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                     alt="{{ $product->title }}"
                     class="w-100 h-100 object-fit-cover"
                     style="object-position:center;">
            @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background:var(--primary-blue-card);">
                    <i class="bi bi-image text-muted" style="font-size:40px;"></i>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="px-2" style="padding-top:3px;">
            {{-- Title --}}
            <p class="text-dark mb-0" style="font-size:13px; font-family:'Mona Sans',sans-serif; line-height:1.2; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                {{ $product->title }}
            </p>

            {{-- Owner + Location --}}
            <p class="mb-0 mt-0 lh-sm" style="font-size:9px; color: var(--text-tertiary); font-family:'Mona Sans',sans-serif; line-height:1.4;">
                <a href="{{ route('store.show', $product->owner_id) }}"
                   class="text-decoration-none"
                   style="color: var(--text-tertiary);"
                   onclick="event.stopPropagation();">{{ $product->owner->name ?? __('ui.unknown') }}</a><br>
                {{ $product->location_city ?? '' }}
            </p>

            {{-- Price --}}
            <p class="mb-0 mt-0 lh-sm">
                <span class="fw-bold" style="color:var(--primary-blue); font-size:14px; font-family:'Mona Sans',sans-serif;">
                    Rp {{ number_format($product->price_per_day, 0, ',', '.') }}
                </span>
                <span class="text-dark" style="font-size:11px; font-family:'Mona Sans',sans-serif;"> {{ __('ui.per_day') }}</span>
            </p>

            {{-- Rating + Rented count --}}
            <div class="d-flex align-items-center justify-content-between" style="margin-top:1px;">
                <div class="d-flex align-items-center gap-0">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star-fill" style="font-size:8px; color:{{ $i <= round($product->rating_avg) ? '#FFB800' : '#d9d9d9' }};"></i>
                    @endfor
                </div>
                <span style="font-size:9px; color: var(--text-tertiary); font-family:'Mona Sans',sans-serif;">
                    {{ $product->total_rented }}x {{ __('ui.rented') }}
                </span>
            </div>
        </div>
    </div>
</a>
@endif
