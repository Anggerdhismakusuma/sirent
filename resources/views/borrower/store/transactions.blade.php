@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <a
                href="{{ url('/dashboard?tab=store') }}"
                class="text-decoration-none small"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Back to Store Dashboard
            </a>

            <h3 class="fw-bold text-primary mt-2 mb-1">
                Store Transaction History
            </h3>

            <p class="text-muted mb-0">
                Review all rental transactions involving your store items.
            </p>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success rounded-3">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->has('dispute'))
        <div class="alert alert-danger">
            {{ $errors->first('dispute') }}
        </div>
    @endif

    @php
        $disputeStatusClasses = [
            'open' => 'bg-warning-subtle text-warning-emphasis',
            'in_review' => 'bg-primary-subtle text-primary-emphasis',
            'resolved' => 'bg-success-subtle text-success-emphasis',
            'rejected' => 'bg-danger-subtle text-danger-emphasis',
        ];
    @endphp

    {{-- Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form
                action="{{ route('borrower.store.transactions.history') }}"
                method="GET"
                class="row g-3 align-items-end"
            >
                <div class="col-12 col-md-4">
                    <label for="transactionMonth" class="form-label fw-semibold">
                        Rental Month
                    </label>

                    <input
                        type="month"
                        id="transactionMonth"
                        name="month"
                        class="form-control"
                        value="{{ $month }}"
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="transactionStatus" class="form-label fw-semibold">
                        Transaction Status
                    </label>

                    <select
                        id="transactionStatus"
                        name="status"
                        class="form-select"
                    >
                        <option value="all" @selected($status === 'all')>
                            All Status
                        </option>

                        <option value="APPROVED" @selected($status === 'APPROVED')>
                            Approved
                        </option>

                        <option value="ONGOING" @selected($status === 'ONGOING')>
                            Ongoing
                        </option>

                        <option value="COMPLETED" @selected($status === 'COMPLETED')>
                            Completed
                        </option>

                        <option value="CANCELLED" @selected($status === 'CANCELLED')>
                            Cancelled
                        </option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <div class="d-flex gap-2">
                        <button
                            type="submit"
                            class="btn btn-primary rounded-pill px-4"
                        >
                            <i class="bi bi-funnel me-1"></i>
                            Apply Filter
                        </button>

                        <a
                            href="{{ route('borrower.store.transactions.history') }}"
                            class="btn btn-light rounded-pill px-4"
                        >
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 px-4 pt-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-1">
                        Transaction List
                    </h5>

                    <small class="text-muted">
                        {{ $transactions->total() }} transaction(s) found
                    </small>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th>Item</th>
                            <th>Renter</th>
                            <th>Rental Period</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($transactions as $transaction)
                            @php
                                $product = $transaction->product;
                                $borrower = $transaction->borrower;

                                $transactionStatus = strtoupper(
                                    (string) $transaction->status
                                );

                                $statusClass = match ($transactionStatus) {
                                    'APPROVED' =>
                                        'bg-primary-subtle text-primary-emphasis',

                                    'ONGOING' =>
                                        'bg-info-subtle text-info-emphasis',

                                    'COMPLETED' =>
                                        'bg-success-subtle text-success-emphasis',

                                    'CANCELLED' =>
                                        'bg-secondary-subtle text-secondary-emphasis',

                                    'REJECTED' =>
                                        'bg-danger-subtle text-danger-emphasis',

                                    default =>
                                        'bg-light text-dark',
                                };

                                $hasActiveDispute =
                                    (bool) $transaction->activeDispute;

                                $canDispute = in_array(
                                    $transactionStatus,
                                    ['APPROVED', 'ONGOING', 'COMPLETED'],
                                    true
                                ) && !$hasActiveDispute;

                                $imagePath =
                                    $product?->primaryImage?->image_path;
                            @endphp

                            <tr>
                                <td>
                                    <strong>#{{ $transaction->id }}</strong>

                                    <div class="small text-muted">
                                        {{ $transaction->created_at?->format(
                                            'd M Y, H:i'
                                        ) }}
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img
                                            src="{{ $imagePath
                                                ? asset('storage/' . $imagePath)
                                                : asset(
                                                    'images/placeholder-product.png'
                                                ) }}"
                                            alt="{{ $product?->title ?? 'Rental item' }}"
                                            class="rounded-3 border"
                                            style="
                                                width: 58px;
                                                height: 58px;
                                                object-fit: cover;
                                            "
                                        >

                                        <div>
                                            <strong>
                                                {{ $product?->title
                                                    ?? 'Product unavailable' }}
                                            </strong>

                                            <div class="small text-muted">
                                                {{ $product?->category?->name
                                                    ?? 'No category' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <strong>
                                        {{ $borrower?->name ?? 'Unknown renter' }}
                                    </strong>

                                    <div class="small text-muted">
                                        {{ $borrower?->email ?? '-' }}
                                    </div>
                                </td>

                                <td>
                                    <div>
                                        {{ $transaction->start_date?->format(
                                            'd M Y'
                                        ) }}
                                    </div>

                                    <div class="small text-muted">
                                        until
                                        {{ $transaction->end_date?->format(
                                            'd M Y'
                                        ) }}
                                    </div>

                                    <div class="small text-muted">
                                        {{ $transaction->total_days }} day(s)
                                    </div>
                                </td>

                                <td>
                                    <strong class="text-primary">
                                        Rp {{ number_format(
                                            (float) $transaction->total_price,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </strong>
                                </td>

                                <td>
                                    <span
                                        class="badge rounded-pill {{ $statusClass }}"
                                    >
                                        {{ ucfirst(
                                            strtolower($transactionStatus)
                                        ) }}
                                    </span>

                                    @if ($hasActiveDispute)
                                        <div class="mt-1">
                                            <span
                                                class="badge rounded-pill text-bg-danger"
                                            >
                                                Dispute
                                                {{ ucfirst(
                                                    strtolower(
                                                        $transaction
                                                            ->activeDispute
                                                            ->status
                                                    )
                                                ) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>

                                <td class="text-end">
                                    @if ($canDispute)
                                        <button
                                            type="button"
                                            class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                            data-bs-toggle="modal"
                                            data-bs-target="#historyDisputeModal{{ $transaction->id }}"
                                        >
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Raise Dispute
                                        </button>
                                    @elseif ($hasActiveDispute)
                                        <span class="small text-muted">
                                            Waiting for admin
                                        </span>
                                    @else
                                        <span class="small text-muted">
                                            No action available
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="fs-1 text-muted mb-2">
                                        <i class="bi bi-receipt"></i>
                                    </div>

                                    <h6 class="fw-bold">
                                        No transactions found
                                    </h6>

                                    <p class="small text-muted mb-0">
                                        Try changing the month or status filter.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($transactions->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Transaction History Dispute Modals --}}
@foreach ($transactions as $transaction)
    @php
        $modalStatus = strtoupper(
            (string) $transaction->status
        );

        $modalCanDispute = in_array(
            $modalStatus,
            ['APPROVED', 'ONGOING', 'COMPLETED'],
            true
        ) && !$transaction->activeDispute;
    @endphp

    @if ($modalCanDispute)
        <div
            class="modal fade"
            id="historyDisputeModal{{ $transaction->id }}"
            tabindex="-1"
            aria-labelledby="historyDisputeModalLabel{{ $transaction->id }}"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 rounded-4">
                    <form
                        action="{{ route(
                            'borrower.store.disputes.store',
                            $transaction
                        ) }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf

                        <div class="modal-header border-0 px-4 pt-4">
                            <div>
                                <h5
                                    class="modal-title fw-bold text-danger"
                                    id="historyDisputeModalLabel{{ $transaction->id }}"
                                >
                                    Raise a Dispute
                                </h5>

                                <small class="text-muted">
                                    Transaction #{{ $transaction->id }}
                                    · {{ $transaction->product?->title }}
                                </small>
                            </div>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>

                        <div class="modal-body px-4">
                            <div class="mb-3">
                                <label
                                    for="historyDisputeCategory{{ $transaction->id }}"
                                    class="form-label fw-semibold"
                                >
                                    Dispute Category
                                </label>

                                <select
                                    id="historyDisputeCategory{{ $transaction->id }}"
                                    name="category"
                                    class="form-select"
                                    required
                                >
                                    <option value="">
                                        Select category
                                    </option>

                                    <option value="late_return">
                                        Late Return
                                    </option>

                                    <option value="item_damage">
                                        Item Damaged
                                    </option>

                                    <option value="item_not_returned">
                                        Item Not Returned
                                    </option>

                                    <option value="payment_issue">
                                        Payment Issue
                                    </option>

                                    <option value="other">
                                        Other Issue
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label
                                    for="historyDisputeDescription{{ $transaction->id }}"
                                    class="form-label fw-semibold"
                                >
                                    Description
                                </label>

                                <textarea
                                    id="historyDisputeDescription{{ $transaction->id }}"
                                    name="description"
                                    class="form-control"
                                    rows="5"
                                    minlength="20"
                                    maxlength="1000"
                                    placeholder="Explain the problem clearly and provide relevant details."
                                    required
                                ></textarea>
                            </div>

                            <div class="mb-3">
                                <label
                                    for="historyDisputeEvidence{{ $transaction->id }}"
                                    class="form-label fw-semibold"
                                >
                                    Supporting Evidence
                                </label>

                                <input
                                    type="file"
                                    id="historyDisputeEvidence{{ $transaction->id }}"
                                    name="evidence"
                                    class="form-control"
                                    accept="
                                        image/jpeg,
                                        image/png,
                                        image/webp,
                                        application/pdf
                                    "
                                >

                                <small class="text-muted">
                                    Optional image or PDF, maximum 4 MB.
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer border-0 px-4 pb-4">
                            <button
                                type="button"
                                class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-danger rounded-pill px-4"
                            >
                                Submit Dispute
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection