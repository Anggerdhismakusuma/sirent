{{-- SI-RENT Status Badge — PRD section 10.2 --}}
@props(['status' => 'pending'])

@php
$map = [
    'pending'   => ['label' => __('ui.status_pending'),   'bg' => '#fff3cd', 'color' => '#856404'],
    'approved'  => ['label' => __('ui.status_approved'),  'bg' => '#d4edda', 'color' => '#155724'],
    'rejected'  => ['label' => __('ui.status_rejected'),  'bg' => '#f8d7da', 'color' => '#721c24'],
    'ongoing'   => ['label' => __('ui.status_ongoing'),   'bg' => '#cce5ff', 'color' => '#004085'],
    'completed' => ['label' => __('ui.status_completed'), 'bg' => '#d4edda', 'color' => '#155724'],
    'cancelled' => ['label' => __('ui.status_cancelled'), 'bg' => '#e2e3e5', 'color' => '#383d41'],
];
$s = $map[$status] ?? $map['pending'];
@endphp

<span class="d-inline-block px-2 py-0 rounded-pill fw-medium"
      style="background:{{ $s['bg'] }}; color:{{ $s['color'] }}; font-family:'Mona Sans',sans-serif; font-size:11px;">
    {{ $s['label'] }}
</span>
