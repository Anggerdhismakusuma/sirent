{{-- SI-RENT Avatar Component --}}
@props(['imagePath' => null, 'name' => 'User', 'size' => 'md'])

@php
$sizes = [
    'sm' => ['w' => 37, 'h' => 37, 'font' => 14],
    'md' => ['w' => 60, 'h' => 60, 'font' => 20],
    'lg' => ['w' => 139, 'h' => 139, 'font' => 48],
];
$s = $sizes[$size] ?? $sizes['md'];
$initials = collect(explode(' ', $name))->filter()->take(2)->map(fn($n) => mb_substr($n, 0, 1))->join('');
@endphp

@if($imagePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($imagePath))
    <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $name }}"
         width="{{ $s['w'] }}" height="{{ $s['h'] }}"
         class="rounded-circle object-fit-cover"
         style="width:{{ $s['w'] }}px; height:{{ $s['h'] }}px;">
@else
    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
         style="width:{{ $s['w'] }}px; height:{{ $s['h'] }}px; font-size:{{ $s['font'] }}px;">
        {{ strtoupper($initials) }}
    </div>
@endif
