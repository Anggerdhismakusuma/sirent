@extends('layouts.app')

@section('title', __('ui.dispute_management') . ' — SI-RENT')

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
                    ← {{ __('ui.dispute_back_to_dashboard') }}
                </a>

                <h1>{{ __('ui.dispute_management') }}</h1>

                <p>
                    {{ __('ui.dispute_subtitle') }}
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
                <strong>{{ __('ui.dispute_action_failed') }}</strong>

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
                        {{ __('ui.dispute_search') }}
                    </label>

                    <input
                        type="search"
                        name="search"
                        id="disputeSearch"
                        value="{{ request('search') }}"
                        placeholder="{{ __('ui.dispute_search_placeholder') }}"
                    >
                </div>

                <div class="admin-filter-form__status">
                    <label for="disputeStatusFilter">
                        {{ __('ui.dispute_status') }}
                    </label>

                    <select
                        name="status"
                        id="disputeStatusFilter"
                    >
                        <option value="">{{ __('ui.dispute_all_statuses') }}</option>

                        <option
                            value="open"
                            @selected(request('status') === 'open')
                        >
                            {{ __('ui.dispute_status_open') }}
                        </option>

                        <option
                            value="in_review"
                            @selected(request('status') === 'in_review')
                        >
                            {{ __('ui.dispute_status_in_review') }}
                        </option>

                        <option
                            value="resolved"
                            @selected(request('status') === 'resolved')
                        >
                            {{ __('ui.dispute_status_resolved') }}
                        </option>

                        <option
                            value="rejected"
                            @selected(request('status') === 'rejected')
                        >
                            {{ __('ui.dispute_status_rejected') }}
                        </option>
                    </select>
                </div>

                <div class="admin-filter-form__actions">
                    <button
                        type="submit"
                        class="admin-filter-button"
                    >
                        {{ __('ui.dispute_apply_filter') }}
                    </button>

                    @if (request()->filled('search') || request()->filled('status'))
                        <a
                            href="{{ route('admin.disputes.index') }}"
                            class="admin-filter-reset"
                        >
                            {{ __('ui.dispute_reset') }}
                        </a>
                    @endif
                </div>
            </form>
        </section>

        {{-- DISPUTE TABLE --}}
        <section class="admin-table-card admin-dispute-section">
            <div class="admin-table-card__header">
                <div>
                    <h2>{{ __('ui.dispute_all_reports') }}</h2>

                    <p>
                        {{ trans_choice(
                            'ui.dispute_reports_found',
                            $disputes->total(),
                            ['count' => $disputes->total()]
                        ) }}
                    </p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="admin-data-table">
                    <thead>
                        <tr>
                            <th>{{ __('ui.dispute_column_id') }}</th>
                            <th>{{ __('ui.dispute_column_reporter') }}</th>
                            <th>{{ __('ui.dispute_column_role') }}</th>
                            <th>{{ __('ui.dispute_column_reported_party') }}</th>
                            <th>{{ __('ui.dispute_column_product') }}</th>
                            <th>{{ __('ui.dispute_column_reason') }}</th>
                            <th>{{ __('ui.dispute_column_submitted') }}</th>
                            <th>{{ __('ui.dispute_column_status') }}</th>
                            <th>{{ __('ui.dispute_column_action') }}</th>
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
                                    ? __('ui.dispute_role_borrower')
                                    : __('ui.dispute_role_store');

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

                                $statusKey =
                                    'ui.dispute_status_' .
                                    strtolower($dispute->status);

                                $statusLabel =
                                    \Illuminate\Support\Facades\Lang::has($statusKey)
                                        ? __($statusKey)
                                        : str($dispute->status)
                                            ->replace('_', ' ')
                                            ->title();
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
                                    {{ $dispute->created_at->locale(app()->getLocale())->translatedFormat('d M Y') }}
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
                                        {{ __('ui.dispute_view_detail') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="9"
                                    class="admin-table-empty"
                                >
                                    {{ __('ui.dispute_no_reports') }}
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