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

            <a href="{{ route('home') }}" class="admin-back-link">
                Back to Home
            </a>
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
                    <a href="#">Manage Users</a>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($latestUsers as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="admin-badge">
                                            {{ ucfirst($user->role ?? 'borrower') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="admin-badge {{ ($user->account_status ?? 'active') !== 'active' ? 'danger' : '' }}">
                                            {{ ucfirst($user->account_status ?? 'active') }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at?->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Latest Products --}}
            <div class="admin-panel">
                <div class="admin-panel-header">
                    <h5>Latest Products</h5>
                    <a href="#">Manage Products</a>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Owner</th>
                                <th>Status</th>
                                <th>Price / Day</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($latestProducts as $product)
                                <tr>
                                    <td>{{ $product->name ?? $product->title ?? 'Product' }}</td>
                                    <td>{{ $product->owner->name ?? '-' }}</td>
                                    <td>
                                        <span class="admin-badge">
                                            {{ ucfirst($product->status ?? '-') }}
                                        </span>
                                    </td>
                                    <td>Rp {{ number_format($product->price_per_day ?? 0, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Latest Rentals --}}
            <div class="admin-panel admin-panel-wide">
                <div class="admin-panel-header">
                    <h5>Latest Rental Requests</h5>
                    <a href="#">View Rentals</a>
                </div>

                <div class="table-responsive">
                    <table class="table admin-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Borrower</th>
                                <th>Owner</th>
                                <th>Period</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($latestRentals as $rental)
                                <tr>
                                    <td>{{ $rental->product->name ?? $rental->product->title ?? '-' }}</td>
                                    <td>{{ $rental->borrower->name ?? '-' }}</td>
                                    <td>{{ $rental->owner->name ?? '-' }}</td>
                                    <td>
                                        {{ $rental->start_date ? \Carbon\Carbon::parse($rental->start_date)->format('d M Y') : '-' }}
                                        -
                                        {{ $rental->end_date ? \Carbon\Carbon::parse($rental->end_date)->format('d M Y') : '-' }}
                                    </td>
                                    <td>Rp {{ number_format($rental->total_price ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="admin-badge">
                                            {{ ucfirst($rental->status ?? '-') }}
                                        </span>
                                    </td>
                                    <td>{{ $rental->created_at?->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-muted">No rental requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection