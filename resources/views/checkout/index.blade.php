{{-- SI-RENT Checkout Page --}}
@extends('layouts.app')

@section('title', __('ui.checkout_title') . ' — ' . config('app.name', 'SI-RENT'))

{{-- Hide footer for clean checkout experience --}}
@section('hide-footer', true)
@if(config('midtrans.is_production') === false)
@section('hide-navbar', true)
@endif

@section('content')
<div class="min-vh-100 d-flex flex-column" style="background: var(--bs-light, #f5f7fa);">
    <div class="container py-4 flex-grow-1">
        {{-- Back Link --}}
        <a href="{{ route('products.show', $product->slug) }}"
           class="d-inline-flex align-items-center gap-1 text-decoration-none mb-3"
           style="font-size: 14px; color: #0031e1;">
            <i class="bi bi-chevron-left"></i> {{ __('ui.back_to_product') }}
        </a>

        <div class="row g-4 justify-content-center">

            {{-- ============ LEFT COLUMN: Order Details ============ --}}
            <div class="col-lg-7">

                {{-- Product Info Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="font-size: 18px;">{{ __('ui.order_summary') }}</h5>
                        <div class="d-flex gap-3">
                            <div class="flex-shrink-0 bg-light rounded-3 overflow-hidden"
                                 style="width: 100px; height: 100px;">
                                @if($product->primaryImage)
                                    <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}"
                                         alt="{{ $product->title }}" class="w-100 h-100"
                                         style="object-fit: cover;">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-image text-muted" style="font-size: 32px;"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1" style="font-size: 16px;">{{ $product->title }}</h6>
                                <p class="text-muted mb-1" style="font-size: 13px;">
                                    {{ $product->category->name ?? '' }}
                                </p>
                                <div class="d-flex align-items-center gap-2" style="font-size: 13px; color: #0031e1;">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $product->location_city }}</span>
                                </div>
                                <div class="mt-1" style="font-size: 13px; color: #6b7280;">
                                    {{ __('ui.owner') }}: {{ $product->owner->name ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rental Detail Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="font-size: 18px;">{{ __('ui.rental_detail') }}</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-muted mb-1" style="font-size: 12px;">{{ __('ui.start_date') }}</div>
                                <div class="fw-semibold" style="font-size: 15px;">
                                    {{ \Carbon\Carbon::parse($data['start_date'])->translatedFormat('d F Y') }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted mb-1" style="font-size: 12px;">{{ __('ui.end_date') }}</div>
                                <div class="fw-semibold" style="font-size: 15px;">
                                    {{ \Carbon\Carbon::parse($data['end_date'])->translatedFormat('d F Y') }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted mb-1" style="font-size: 12px;">{{ __('ui.duration') }}</div>
                                <div class="fw-semibold" style="font-size: 15px;">
                                    {{ $data['total_days'] }} {{ __('ui.days') }}
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted mb-1" style="font-size: 12px;">{{ __('ui.quantity') }}</div>
                                <div class="fw-semibold" style="font-size: 15px;">
                                    {{ $data['quantity'] }} {{ __('ui.unit') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Customer Details Card --}}
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="font-size: 18px;">{{ __('ui.customer_detail') }}</h5>
                        <div class="d-flex align-items-center gap-3">
                            <x-shared.avatar :imagePath="auth()->user()->avatar"
                                             :name="auth()->user()->name" size="md" />
                            <div>
                                <div class="fw-semibold" style="font-size: 15px;">{{ auth()->user()->name }}</div>
                                <div style="font-size: 13px; color: #6b7280;">{{ auth()->user()->email }}</div>
                                <div style="font-size: 13px; color: #6b7280;">
                                    {{ auth()->user()->phone ?? __('ui.phone_not_set') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============ RIGHT COLUMN: Price Summary + Pay Button ============ --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3" style="font-size: 18px;">{{ __('ui.payment_summary') }}</h5>

                        {{-- Price Breakdown --}}
                        <div class="d-flex justify-content-between mb-2" style="font-size: 14px;">
                            <span style="color: #6b7280;">
                                Rp {{ number_format($product->price_per_day, 0, ',', '.') }}
                                × {{ $data['total_days'] }} {{ __('ui.days') }}
                                × {{ $data['quantity'] }} {{ __('ui.unit') }}
                            </span>
                            <span class="fw-medium">Rp {{ number_format($data['total_price'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3" style="font-size: 14px;">
                            <span style="color: #6b7280;">{{ __('ui.service_fee') }}</span>
                            <span class="fw-medium">Rp {{ number_format($data['service_fee'], 0, ',', '.') }}</span>
                        </div>

                        <hr style="border-color: var(--bs-border-color, #e5e7eb);">

                        {{-- Grand Total --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <span class="fw-bold" style="font-size: 16px;">{{ __('ui.total') }}</span>
                            <span class="fw-bold" style="font-size: 22px; color: #0031e1;">
                                Rp {{ number_format($data['grand_total'], 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Pay Now Button --}}
                        <button id="pay-now-btn"
                                class="btn w-100 text-white fw-semibold rounded-3 py-3"
                                style="background: #0031e1; font-size: 15px;">
                            {{ __('ui.pay_now') }}
                        </button>

                        {{-- Trust Badges --}}
                        <div class="d-flex justify-content-center gap-3 mt-3" style="font-size: 11px; color: #9ca3af;">
                            <span><i class="bi bi-shield-check me-1"></i>{{ __('ui.secure_payment') }}</span>
                            <span><i class="bi bi-lock me-1"></i>{{ __('ui.encrypted_data') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Environment-aware Midtrans Snap CDN --}}
@php
    $snapUrl = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
@endphp
<script src="{{ $snapUrl }}"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const payBtn = document.getElementById('pay-now-btn');
    if (! payBtn) return;

    payBtn.addEventListener('click', async function () {
        const btn = this;
        const originalText = btn.textContent;

        // ── Disable button, show spinner ──
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ __("ui.processing") }}';

        try {
            const res = await fetch('{{ route('checkout.pay', $token) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
            });

            const data = await res.json();

            if (! data.success || ! data.snap_token) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("ui.oops") }}',
                    text: data.message || '{{ __("ui.payment_failed_desc") }}',
                    confirmButtonColor: '#0031e1',
                });
                btn.disabled = false;
                btn.textContent = originalText;
                return;
            }

            // ── Trigger Midtrans Snap modal ──
            window.snap.pay(data.snap_token, {
                onSuccess: function (result) {
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __("ui.payment_success") }}',
                        text: '{{ __("ui.payment_success_desc") }}',
                        confirmButtonColor: '#0031e1',
                    }).then(() => {
                        window.location.href = '{{ route('borrower.dashboard') }}?tab=activity';
                    });
                },
                onPending: function (result) {
                    Swal.fire({
                        icon: 'info',
                        title: '{{ __("ui.payment_pending") }}',
                        text: '{{ __("ui.payment_pending_desc") }}',
                        confirmButtonColor: '#0031e1',
                    }).then(() => {
                        window.location.href = '{{ route('borrower.dashboard') }}?tab=activity';
                    });
                },
                onError: function (result) {
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __("ui.payment_failed") }}',
                        text: result.status_message || '{{ __("ui.payment_failed_desc") }}',
                        confirmButtonColor: '#0031e1',
                    });
                    btn.disabled = false;
                    btn.textContent = originalText;
                },
                onClose: function () {
                    // User closed the popup without completing payment
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("ui.payment_cancelled") }}',
                        text: '{{ __("ui.payment_cancelled_desc") }}',
                        confirmButtonColor: '#0031e1',
                        confirmButtonText: '{{ __("ui.go_to_dashboard") }}',
                        showCancelButton: true,
                        cancelButtonText: '{{ __("ui.close") }}',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route('borrower.dashboard') }}?tab=activity';
                        }
                    });
                    btn.disabled = false;
                    btn.textContent = originalText;
                },
            });
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: '{{ __("ui.network_error_title") }}',
                text: '{{ __("ui.network_error") }}',
                confirmButtonColor: '#0031e1',
            });
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
});
</script>
@endpush
