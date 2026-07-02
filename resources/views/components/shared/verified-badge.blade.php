{{-- SI-RENT Verified Badge — blue pill for verified users --}}
@props(['isVerified' => false])

@if($isVerified)
<span class="d-inline-flex align-items-center gap-1 px-2 py-0 rounded-pill"
      style="background:#ecf2fd; font-family:'Mona Sans',sans-serif; font-size:11px; color:#204be5; border:1px solid #c2d8ff;">
    <i class="bi bi-patch-check-fill" style="font-size:11px;"></i>
    {{ __('ui.verified') }}
</span>
@endif
