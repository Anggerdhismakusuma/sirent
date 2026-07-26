@extends('layouts.app')

@section('title', __('ui.admin_dashboard_title') . ' — SI-RENT')

@section('content')
<div class="admin-dashboard-wrapper">
    <div class="admin-dashboard-container">

        {{-- HEADER --}}
        <div class="admin-header">
            <div>
                <h1>{{ __('ui.admin_dashboard_title') }}</h1>
                <p>{{ __('ui.admin_dashboard_subtitle') }}</p>
            </div>

            <div class="admin-header__actions">

                {{-- LANGUAGE SWITCHER --}}
                <div class="admin-language-switcher">
                    <form
                        action="{{ route('locale.switch', 'en') }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="admin-language-button
                                {{ app()->isLocale('en') ? 'active' : '' }}"
                        >
                            EN
                        </button>
                    </form>

                    <form
                        action="{{ route('locale.switch', 'id') }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="admin-language-button
                                {{ app()->isLocale('id') ? 'active' : '' }}"
                        >
                            ID
                        </button>
                    </form>
                </div>

                {{-- LOGOUT --}}
                <form
                    action="{{ route('auth.logout') }}"
                    method="POST"
                    class="admin-logout-form"
                >
                    @csrf

                    <button
                        type="submit"
                        class="admin-logout-button"
                    >
                        {{ __('ui.logout') }}
                    </button>
                </form>

            </div>
        </div>

        {{-- STATS --}}
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <small>{{ __('ui.admin_total_users') }}</small>
                <h3>{{ $stats['total_users'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_borrowers') }}</small>
                <h3>{{ $stats['total_borrowers'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_owners') }}</small>
                <h3>{{ $stats['total_owners'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_admins') }}</small>
                <h3>{{ $stats['total_admins'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_suspended_users') }}</small>
                <h3>{{ $stats['suspended_user'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_total_products') }}</small>
                <h3>{{ $stats['total_products'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_active_products') }}</small>
                <h3>{{ $stats['active_products'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_total_rentals') }}</small>
                <h3>{{ $stats['total_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_pending_rentals') }}</small>
                <h3>{{ $stats['pending_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_ongoing_rentals') }}</small>
                <h3>{{ $stats['ongoing_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_completed_rentals') }}</small>
                <h3>{{ $stats['completed_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>{{ __('ui.admin_completed_revenue') }}</small>
                <h3>Rp {{ number_format($stats['total_completed_revenue'] ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>

        {{-- SUMMARY ROW --}}
        <div class="admin-summary-grid">
            <div class="admin-panel">
                <h5>{{ __('ui.admin_rental_status_summary') }}</h5>

                <div class="admin-status-list">
                    @forelse (($rentalStatusSummary ?? []) as $status => $total)
                        @php
                            $statusKey = 'ui.status_' . strtolower($status);

                            $statusLabel = \Illuminate\Support\Facades\Lang::has($statusKey)
                                ? __($statusKey)
                                : str($status)->replace('_', ' ')->title();
                        @endphp

                        <div class="admin-status-item">
                            <span>{{ $statusLabel }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('ui.admin_no_rental_data') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="admin-panel">
                <h5>{{ __('ui.admin_product_status_summary') }}</h5>

                <div class="admin-status-list">
                    @forelse (($productStatusSummary ?? []) as $status => $total)
                        @php
                            $statusKey = 'ui.status_' . strtolower($status);

                            $statusLabel = \Illuminate\Support\Facades\Lang::has($statusKey)
                                ? __($statusKey)
                                : str($status)->replace('_', ' ')->title();
                        @endphp

                        <div class="admin-status-item">
                            <span>{{ $statusLabel }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">{{ __('ui.admin_no_product_data') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- TABLES --}}
        <div class="admin-content-grid">

            {{-- Latest Users --}}
            <div class="admin-panel">
                <div class="admin-panel-header">
                    <h5>{{ __('ui.admin_latest_users') }}</h5>
                    <button
                        type="button"
                        class="admin-card-action"
                        data-bs-toggle="modal"
                        data-bs-target="#allUsersModal"
                    >
                        {{ __('ui.admin_view_all_users') }}
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>{{ __('ui.name') }}</th>
                                <th>{{ __('ui.email') }}</th>
                                <th>{{ __('ui.admin_role') }}</th>
                                <th>{{ __('ui.admin_status') }}</th>
                            </tr>
                        </thead>

                         <tbody>
                            @foreach ($latestUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>

                                    @php
                                        $roleKey = 'ui.admin_role_' . strtolower($user->role);
                                    @endphp

                                    <td>
                                        {{ \Illuminate\Support\Facades\Lang::has($roleKey)
                                            ? __($roleKey)
                                            : ucfirst(strtolower($user->role)) }}
                                    </td>

                                    <td>
                                        <span
                                            class="user-status-badge
                                            {{ $user->account_status === \App\Models\User::ACCOUNT_ACTIVE
                                                ? 'user-status-badge--active'
                                                : 'user-status-badge--suspended' }}"
                                        >
                                            @php
                                                $accountStatusKey =
                                                    'ui.admin_status_' .
                                                    strtolower($user->account_status);
                                            @endphp

                                            {{ \Illuminate\Support\Facades\Lang::has($accountStatusKey)
                                                ? __($accountStatusKey)
                                                : ucfirst(strtolower($user->account_status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Products by Category --}}
            <div class="admin-panel product-category-card">
                <div class="admin-panel-header">
                    <div>
                        <h5>{{ __('ui.admin_products_by_category') }}</h5>
                        <p class="product-category-card__subtitle">
                            {{ __('ui.admin_active_product_distribution') }}
                        </p>
                    </div>

                    <div class="product-category-card__total">
                        <strong>{{ $totalActiveProducts ?? 0 }}</strong>
                        <span>{{ __('ui.admin_active_products') }}</span>
                    </div>
                </div>

                @if (($productCategoryStats ?? collect())->isNotEmpty())
                    <div class="product-category-card__content">

                        {{-- PIE / DOUGHNUT CHART --}}
                        <div class="product-category-card__chart">
                            <canvas
                                id="productCategoryChart"
                                aria-label="{{ __('ui.admin_product_distribution_aria') }}"
                                role="img"
                            ></canvas>
                        </div>

                        {{-- CATEGORY DETAILS --}}
                        <div class="product-category-card__details">
                            @foreach ($productCategoryStats as $stat)
                                @php
                                    $categoryName =
                                        $stat->category?->name
                                        ?? __('ui.admin_uncategorized');

                                    $percentage = ($totalActiveProducts ?? 0) > 0
                                        ? round(
                                            ((int) $stat->total / $totalActiveProducts)
                                            * 100
                                        )
                                        : 0;
                                @endphp

                                <div class="product-category-item">
                                    <div class="product-category-item__name">
                                        <span
                                            class="product-category-item__dot"
                                            data-category-dot="{{ $loop->index }}"
                                        ></span>

                                        <span>{{ $categoryName }}</span>
                                    </div>

                                    <div class="product-category-item__value">
                                        <strong>{{ $stat->total }}</strong>
                                        <span>{{ $percentage }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @else
                    <div class="product-category-card__empty">
                        {{ __('ui.admin_no_active_products') }}
                    </div>
                @endif
            </div>

            {{-- ============================================
                DISPUTES REQUIRING ATTENTION
                ============================================ --}}
            <section class="admin-table-card admin-dispute-section">

                <div class="admin-table-card__header">
                    <div>
                        <h2>{{ __('ui.admin_disputes_attention') }}</h2>

                        <p>
                            {{ __('ui.admin_oldest_unresolved_disputes') }}
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.disputes.index') }}"
                        class="admin-table-card__link"
                    >
                        {{ __('ui.admin_view_all_disputes') }}
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="admin-data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ __('ui.dispute_column_reporter') }}</th>
                                <th>{{ __('ui.admin_role') }}</th>
                                <th>{{ __('ui.dispute_column_reported_party') }}</th>
                                <th>{{ __('ui.dispute_column_product') }}</th>
                                <th>{{ __('ui.dispute_column_reason') }}</th>
                                <th>{{ __('ui.admin_age') }}</th>
                                <th>{{ __('ui.admin_status') }}</th>
                                <th>{{ __('ui.admin_action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($oldestDisputes as $dispute)
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

                                    $disputeStatusKey =
                                        'ui.dispute_status_' .
                                        strtolower($dispute->status);

                                    $disputeStatusLabel =
                                        \Illuminate\Support\Facades\Lang::has(
                                            $disputeStatusKey
                                        )
                                            ? __($disputeStatusKey)
                                            : str($dispute->status)
                                                ->replace('_', ' ')
                                                ->title();
                                @endphp

                                <tr>
                                    <td>
                                        DSP-{{ str_pad(
                                            $dispute->id,
                                            4,
                                            '0',
                                            STR_PAD_LEFT
                                        ) }}
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
                                            40
                                        ) }}
                                    </td>

                                    <td>
                                        {{ $dispute->created_at->locale(app()->getLocale())->diffForHumans() }}
                                    </td>

                                    <td>
                                        <span
                                            class="admin-badge
                                                admin-badge--{{ str_replace(
                                                    '_',
                                                    '-',
                                                    $dispute->status
                                                ) }}"
                                        >
                                            {{ $disputeStatusLabel }}
                                        </span>
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="admin-action-button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#disputeModal{{ $dispute->id }}"
                                        >
                                            {{ __('ui.dispute_view') }}
                                        </button>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="9" class="admin-table-empty">
                                        {{ __('ui.admin_no_unresolved_disputes') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </section>
        </div>
    </div>
</div>

{{-- Dispute modals are outside the table to keep the HTML structure valid. --}}
@foreach ($oldestDisputes as $dispute)
    @include(
        'admin.disputes.partials.detail-modal',
        ['dispute' => $dispute]
    )
@endforeach

<div
    class="modal fade"
    id="allUsersModal"
    tabindex="-1"
    aria-labelledby="allUsersModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content admin-users-modal">

            <div class="modal-header admin-users-modal__header">
                <div>
                    <h5
                        class="modal-title"
                        id="allUsersModalLabel"
                    >
                        {{ __('ui.admin_all_users') }}
                    </h5>

                    <p>
                        {{ trans_choice(
                            'ui.admin_registered_users',
                            $allUsers->count(),
                            ['count' => $allUsers->count()]
                        ) }}
                    </p>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="{{ __('ui.close') }}"
                ></button>
            </div>

            <div class="modal-body admin-users-modal__body">

                <div class="admin-users-modal__search">
                    <input
                        type="search"
                        id="allUsersSearch"
                        placeholder="{{ __('ui.admin_search_users_placeholder') }}"
                        autocomplete="off"
                    >
                </div>

                <div class="table-responsive admin-users-modal__table">
                    <table class="admin-data-table">
                        <thead>
                            <tr>
                                <th>{{ __('ui.name') }}</th>
                                <th>{{ __('ui.email') }}</th>
                                <th>{{ __('ui.admin_role') }}</th>
                                <th>{{ __('ui.admin_status') }}</th>
                                <th>{{ __('ui.admin_joined') }}</th>
                                <th>{{ __('ui.admin_action') }}</th>
                            </tr>
                        </thead>

                        <tbody id="all-users-table-body">
                            @foreach ($users as $user)
                                <tr class="all-user-row">
                                    <td>{{ $user->name }}</td>

                                    <td>{{ $user->email }}</td>

                                    <td>
                                        @php
                                            $roleKey =
                                                'ui.admin_role_' .
                                                strtolower($user->role);
                                        @endphp

                                        <span class="user-role-badge">
                                            {{ \Illuminate\Support\Facades\Lang::has($roleKey)
                                                ? __($roleKey)
                                                : ucfirst(strtolower($user->role)) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span
                                            class="user-status-badge
                                            {{ $user->account_status === \App\Models\User::ACCOUNT_ACTIVE
                                                ? 'user-status-badge--active'
                                                : 'user-status-badge--suspended' }}"
                                        >
                                            @php
                                                $accountStatusKey =
                                                    'ui.admin_status_' .
                                                    strtolower($user->account_status);
                                            @endphp

                                            {{ \Illuminate\Support\Facades\Lang::has($accountStatusKey)
                                                ? __($accountStatusKey)
                                                : ucfirst(strtolower($user->account_status)) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $user->created_at->locale(app()->getLocale())->translatedFormat('d M Y') }}
                                    </td>

                                    <td>
                                        @if (auth()->user()->is($user))
                                            <span class="admin-user-protected">
                                                {{ __('ui.admin_current_user') }}
                                            </span>

                                        @elseif ($user->role === \App\Models\User::ROLE_ADMIN)
                                            <span class="admin-user-protected">
                                                {{ __('ui.admin_protected') }}
                                            </span>

                                        @elseif ($user->account_status === \App\Models\User::ACCOUNT_ACTIVE)
                                            <form
                                                action="{{ route('admin.users.update-status', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm(@js(
                                                    __('ui.admin_confirm_suspend', [
                                                        'name' => $user->name,
                                                    ])
                                                ))"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="{{ \App\Models\User::ACCOUNT_SUSPENDED }}"
                                                >

                                                <button
                                                    type="submit"
                                                    class="admin-user-action admin-user-action--suspend"
                                                >
                                                    {{ __('ui.admin_suspend') }}
                                                </button>
                                            </form>
                                        @else
                                            <form
                                                action="{{ route('admin.users.update-status', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm(@js(
                                                    __('ui.admin_confirm_activate', [
                                                        'name' => $user->name,
                                                    ])
                                                ))"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="{{ \App\Models\User::ACCOUNT_ACTIVE }}"
                                                >

                                                <button
                                                    type="submit"
                                                    class="admin-user-action admin-user-action--activate"
                                                >
                                                    {{ __('ui.admin_activate') }}
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div
                    id="allUsersEmptySearch"
                    class="admin-users-modal__empty d-none"
                >
                    {{ __('ui.admin_no_users_match') }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chartCanvas = document.getElementById('productCategoryChart');

    if (!chartCanvas) {
        return;
    }

    const labels = {{ Illuminate\Support\Js::from($productCategoryLabels ?? []) }};
    const values = {{ Illuminate\Support\Js::from($productCategoryCounts ?? []) }};

    const rootStyles = getComputedStyle(document.documentElement);

    const chartColors = [
        rootStyles.getPropertyValue('--primary-blue').trim(),
        rootStyles.getPropertyValue('--primary-blue-light').trim(),
        rootStyles.getPropertyValue('--primary-blue-dark').trim(),
        rootStyles.getPropertyValue('--sidebar-highlight').trim(),
        rootStyles.getPropertyValue('--primary-blue-deep').trim(),
        rootStyles.getPropertyValue('--primary-blue-ghost').trim(),
    ];

    new Chart(chartCanvas, {
        type: 'doughnut',

        data: {
            labels: labels,

            datasets: [{
                data: values,
                backgroundColor: labels.map(
                    (_, index) => chartColors[index % chartColors.length]
                ),
                borderWidth: 3,
                borderColor: rootStyles
                    .getPropertyValue('--bg-surface')
                    .trim(),
            }],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '62%',

            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
});
</script>
@endpush
