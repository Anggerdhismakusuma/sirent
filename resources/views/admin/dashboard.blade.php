@extends('layouts.app')

@section('title', 'Admin Dashboard — SI-RENT')

@section('content')
<div class="admin-dashboard-wrapper">
    <div class="admin-dashboard-container">

        {{-- HEADER --}}
        <div class="admin-header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Operational control center for SI-RENT platform.</p>
            </div>

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
                    Logout
                </button>
            </form>
        </div>

        {{-- STATS --}}
        <div class="admin-stats-grid">
            <div class="admin-stat-card">
                <small>Total Users</small>
                <h3>{{ $stats['total_users'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Borrowers</small>
                <h3>{{ $stats['total_borrowers'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Owners</small>
                <h3>{{ $stats['total_owners'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Admins</small>
                <h3>{{ $stats['total_admins'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Active Sellers</small>
                <h3>{{ $stats['active_sellers'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Total Products</small>
                <h3>{{ $stats['total_products'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Active Products</small>
                <h3>{{ $stats['active_products'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Total Rentals</small>
                <h3>{{ $stats['total_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Pending Rentals</small>
                <h3>{{ $stats['pending_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Ongoing Rentals</small>
                <h3>{{ $stats['ongoing_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Completed Rentals</small>
                <h3>{{ $stats['completed_rentals'] ?? 0 }}</h3>
            </div>

            <div class="admin-stat-card">
                <small>Completed Revenue</small>
                <h3>Rp {{ number_format($stats['total_completed_revenue'] ?? 0, 0, ',', '.') }}</h3>
            </div>
        </div>

        {{-- SUMMARY ROW --}}
        <div class="admin-summary-grid">
            <div class="admin-panel">
                <h5>Rental Status Summary</h5>

                <div class="admin-status-list">
                    @forelse (($rentalStatusSummary ?? []) as $status => $total)
                        <div class="admin-status-item">
                            <span>{{ ucfirst($status) }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No rental data found.</p>
                    @endforelse
                </div>
            </div>

            <div class="admin-panel">
                <h5>Product Status Summary</h5>

                <div class="admin-status-list">
                    @forelse (($productStatusSummary ?? []) as $status => $total)
                        <div class="admin-status-item">
                            <span>{{ ucfirst($status) }}</span>
                            <strong>{{ $total }}</strong>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No product data found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- TABLES --}}
        <div class="admin-content-grid">

            {{-- Latest Users --}}
            <div class="admin-panel">
                <div class="admin-panel-header">
                    <h5>Latest Users</h5>
                    <button
                        type="button"
                        class="admin-card-action"
                        data-bs-toggle="modal"
                        data-bs-target="#allUsersModal"
                    >
                        View All Users
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                         <tbody>
                            @foreach ($latestUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>

                                    <td>
                                        {{ ucfirst(strtolower($user->role)) }}
                                    </td>

                                    <td>
                                        <span
                                            class="user-status-badge
                                            {{ $user->account_status === \App\Models\User::ACCOUNT_ACTIVE
                                                ? 'user-status-badge--active'
                                                : 'user-status-badge--suspended' }}"
                                        >
                                            {{ ucfirst(strtolower($user->account_status)) }}
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
                        <h5>Products by Category</h5>
                        <p class="product-category-card__subtitle">
                            Distribution of active products.
                        </p>
                    </div>

                    <div class="product-category-card__total">
                        <strong>{{ $totalActiveProducts ?? 0 }}</strong>
                        <span>Active Products</span>
                    </div>
                </div>

                @if (($productCategoryStats ?? collect())->isNotEmpty())
                    <div class="product-category-card__content">

                        {{-- PIE / DOUGHNUT CHART --}}
                        <div class="product-category-card__chart">
                            <canvas
                                id="productCategoryChart"
                                aria-label="Product distribution by category"
                                role="img"
                            ></canvas>
                        </div>

                        {{-- CATEGORY DETAILS --}}
                        <div class="product-category-card__details">
                            @foreach ($productCategoryStats as $stat)
                                @php
                                    $categoryName =
                                        $stat->category?->name
                                        ?? 'Tanpa Kategori';

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
                        No active products found.
                    </div>
                @endif
            </div>

            {{-- ============================================
                DISPUTES REQUIRING ATTENTION
                ============================================ --}}
            <section class="admin-table-card admin-dispute-section">

                <div class="admin-table-card__header">
                    <div>
                        <h2>Disputes Requiring Attention</h2>

                        <p>
                            Oldest unresolved dispute reports.
                        </p>
                    </div>

                    <a
                        href="{{ route('admin.disputes.index') }}"
                        class="admin-table-card__link"
                    >
                        View All Disputes
                    </a>
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
                                <th>Age</th>
                                <th>Status</th>
                                <th>Action</th>
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
                                        ? 'Borrower'
                                        : 'Store';

                                    $reportedParty = $isBorrowerReporter
                                        ? $rentalRequest?->owner
                                        : $rentalRequest?->borrower;

                                    $productName =
                                        $rentalRequest?->product?->name
                                        ?? $rentalRequest?->product?->title
                                        ?? '-';
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
                                        {{ $dispute->created_at->diffForHumans() }}
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
                                            {{ str($dispute->status)
                                                ->replace('_', ' ')
                                                ->title() }}
                                        </span>
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="admin-action-button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#disputeModal{{ $dispute->id }}"
                                        >
                                            View
                                        </button>
                                    </td>
                                </tr>

                                @include(
                                    'admin.disputes.partials.detail-modal',
                                    ['dispute' => $dispute]
                                )
                            @empty
                                <tr>
                                    <td colspan="9" class="admin-table-empty">
                                        No unresolved disputes.
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
                        All Users
                    </h5>

                    <p>
                        {{ $allUsers->count() }} registered users
                    </p>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body admin-users-modal__body">

                <div class="admin-users-modal__search">
                    <input
                        type="search"
                        id="allUsersSearch"
                        placeholder="Search name, email, role, or status..."
                        autocomplete="off"
                    >
                </div>

                <div class="table-responsive admin-users-modal__table">
                    <table class="admin-data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="all-users-table-body">
                            @foreach ($users as $user)
                                <tr class="all-user-row">
                                    <td>{{ $user->name }}</td>

                                    <td>{{ $user->email }}</td>

                                    <td>
                                        <span class="user-role-badge">
                                            {{ ucfirst(strtolower($user->role)) }}
                                        </span>
                                    </td>

                                    <td>
                                        <span
                                            class="user-status-badge
                                            {{ $user->account_status === \App\Models\User::ACCOUNT_ACTIVE
                                                ? 'user-status-badge--active'
                                                : 'user-status-badge--suspended' }}"
                                        >
                                            {{ ucfirst(strtolower($user->account_status)) }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>

                                    <td>
                                        @if (auth()->user()->is($user))
                                            <span class="admin-user-protected">
                                                Current User
                                            </span>

                                        @elseif ($user->role === \App\Models\User::ROLE_ADMIN)
                                            <span class="admin-user-protected">
                                                Protected
                                            </span>

                                        @elseif ($user->account_status === \App\Models\User::ACCOUNT_ACTIVE)
                                            <form
                                                action="{{ route('admin.users.update-status', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Suspend akun {{ $user->name }}?')"
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
                                                    Suspend
                                                </button>
                                            </form>
                                        @else
                                            <form
                                                action="{{ route('admin.users.update-status', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Aktifkan kembali akun {{ $user->name }}?')"
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
                                                    Activate
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
                    No users match your search.
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