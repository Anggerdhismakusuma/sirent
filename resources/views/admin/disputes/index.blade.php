@extends('layouts.app')

@section('title', 'Dispute Management — SI-RENT')

@section('content')
<main class="admin-disputes-page">
    <div class="container py-4">

        {{-- PAGE HEADER --}}
        <div class="admin-page-header">
            <div>
                <a
                    href="{{ route('admin.dashboard') }}"
                    class="admin-page-header__back"
                >
                    ← Back to Dashboard
                </a>

                <h1>Dispute Management</h1>

                <p>
                    Review and manage dispute reports submitted by borrowers
                    and store owners.
                </p>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- VALIDATION ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <strong>Action failed.</strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FILTER --}}
        <section class="admin-filter-card">
            <form
                action="{{ route('admin.disputes.index') }}"
                method="GET"
                class="admin-filter-form"
            >
                <div class="admin-filter-form__search">
                    <label for="disputeSearch">
                        Search
                    </label>

                    <input
                        type="search"
                        name="search"
                        id="disputeSearch"
                        value="{{ request('search') }}"
                        placeholder="Search reporter, product, or reason..."
                    >
                </div>

                <div class="admin-filter-form__status">
                    <label for="disputeStatusFilter">
                        Status
                    </label>

                    <select
                        name="status"
                        id="disputeStatusFilter"
                    >
                        <option value="">All Statuses</option>

                        <option
                            value="open"
                            @selected(request('status') === 'open')
                        >
                            Open
                        </option>

                        <option
                            value="in_review"
                            @selected(request('status') === 'in_review')
                        >
                            In Review
                        </option>

                        <option
                            value="resolved"
                            @selected(request('status') === 'resolved')
                        >
                            Resolved
                        </option>

                        <option
                            value="rejected"
                            @selected(request('status') === 'rejected')
                        >
                            Rejected
                        </option>
                    </select>
                </div>

                <div class="admin-filter-form__actions">
                    <button
                        type="submit"
                        class="admin-filter-button"
                    >
                        Apply Filter
                    </button>

                    @if (request()->filled('search') || request()->filled('status'))
                        <a
                            href="{{ route('admin.disputes.index') }}"
                            class="admin-filter-reset"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </section>

        {{-- DISPUTE TABLE --}}
        <section class="admin-table-card admin-dispute-section">
            <div class="admin-table-card__header">
                <div>
                    <h2>All Dispute Reports</h2>

                    <p>
                        {{ $disputes->total() }}
                        dispute report{{ $disputes->total() === 1 ? '' : 's' }}
                        found.
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Reporter</th>
                            <th>Role</th>
                            <th>Reported Party</th>
                            <th>Product</th>
                            <th>Reason</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($disputes as $dispute)
                            @php
                                $rentalRequest = $dispute->rentalRequest;

                                $isBorrowerReporter =
                                    (int) $dispute->reporter_id ===
                                    (int) $rentalRequest?->borrower_id;

                                $reporterRole = $isBorrowerReporter
                                    ? 'Borrower'
                                    : 'Store';

                                $reportedParty = $isBorrowerReporter
                                    ? $rentalRequest?->owner
                                    : $rentalRequest?->borrower;

                                $productName =
                                    $rentalRequest?->product?->name
                                    ?? $rentalRequest?->product?->title
                                    ?? '-';

                                $statusClass = str_replace(
                                    '_',
                                    '-',
                                    strtolower($dispute->status)
                                );

                                $statusLabel = ucwords(
                                    str_replace('_', ' ', $dispute->status)
                                );
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        DSP-{{ str_pad(
                                            $dispute->id,
                                            4,
                                            '0',
                                            STR_PAD_LEFT
                                        ) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $dispute->reporter?->name ?? '-' }}
                                </td>

                                <td>
                                    <span class="admin-badge admin-badge--role">
                                        {{ $reporterRole }}
                                    </span>
                                </td>

                                <td>
                                    {{ $reportedParty?->name ?? '-' }}
                                </td>

                                <td>
                                    {{ $productName }}
                                </td>

                                <td>
                                    {{ \Illuminate\Support\Str::limit(
                                        $dispute->reason,
                                        45
                                    ) }}
                                </td>

                                <td>
                                    {{ $dispute->created_at->format('d M Y') }}
                                </td>

                                <td>
                                    <span
                                        class="admin-badge
                                            admin-badge--{{ $statusClass }}"
                                    >
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td>
                                    <button
                                        type="button"
                                        class="admin-action-button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#disputeModal{{ $dispute->id }}"
                                    >
                                        View Detail
                                    </button>
                                </td>
                            </tr>
                            @include(
                                'admin.disputes.partials.detail-modal',
                                ['dispute' => $dispute]
                            )
                        @empty
                            <tr>
                                <td
                                    colspan="9"
                                    class="admin-table-empty"
                                >
                                    No dispute reports found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($disputes->hasPages())
                <div class="admin-table-pagination">
                    {{ $disputes->links() }}
                </div>
            @endif
        </section>

    </div>

    {{-- MODALS
         Diletakkan di luar table agar struktur HTML tetap valid.
    --}}
    @foreach ($disputes as $dispute)
        @include(
            'admin.disputes.partials.detail-modal',
            ['dispute' => $dispute]
        )
    @endforeach
</main>
@endsection