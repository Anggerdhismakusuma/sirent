{{-- SI-RENT Rating Badge — stars + score + review count --}}
@props(['score' => 0, 'totalReviews' => 0, 'showReviews' => true])

<span class="d-inline-flex align-items-center gap-1" style="font-family:'Mona Sans',sans-serif; font-size:13px;">
    @for($i = 1; $i <= 5; $i++)
        @if($i <= floor($score))
            <i class="bi bi-star-fill" style="color:#FFB800; font-size:11px;"></i>
        @elseif($i - 0.5 <= $score)
            <i class="bi bi-star-half" style="color:#FFB800; font-size:11px;"></i>
        @else
            <i class="bi bi-star-fill" style="color:#d9d9d9; font-size:11px;"></i>
        @endif
    @endfor
    <span class="fw-medium text-dark">{{ number_format($score, 1) }}</span>
    @if($showReviews && $totalReviews > 0)
        <span class="text-muted" style="font-size:13px;">({{ __('ui.reviews_count', ['count' => $totalReviews]) }})</span>
    @endif
</span>
