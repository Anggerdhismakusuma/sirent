<div class="store-dashboard-wrapper py-4">

    @if (!$user->is_owner_active)
        {{-- EMPTY STORE STATE --}}
        <div class="empty-store-state">
            <h1>OOPS, YOU HAVE NO STORE!</h1>

            <form action="{{ route('borrower.store.open') }}" method="POST">
                @csrf
                <button type="submit" class="btn-open-store">
                    Open Store NOW!
                </button>
            </form>
        </div>
    @else
        {{-- SELLER DASHBOARD --}}
        <div class="seller-dashboard">

            <h5 class="seller-section-title mb-4">Rental Statistics</h5>

            {{-- STATS CARDS --}}
            <div class="seller-stats-grid mb-4">

                <div class="seller-stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <small>Total Rent Income</small>
                    <h4>Rp {{ number_format($sellerStats['income'] ?? 0, 0, ',', '.') }}</h4>
                    <p class="{{ $sellerGrowth['income']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['income']['icon'] ?? '•' }}
                        {{ $sellerGrowth['income']['label'] ?? 'No comparison data' }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <small>Total Transaction</small>
                    <h4>{{ $sellerStats['transactions'] ?? 0 }}</h4>
                    <p class="{{ $sellerGrowth['transactions']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['transactions']['icon'] ?? '•' }}
                        {{ $sellerGrowth['transactions']['label'] ?? 'No comparison data' }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <small>Available Items</small>
                    <h4>{{ $sellerStats['items'] ?? 0 }}</h4>
                    <p class="{{ $sellerGrowth['items']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['items']['icon'] ?? '•' }}
                        {{ $sellerGrowth['items']['label'] ?? 'No comparison data' }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <small>Ongoing Rent</small>
                    <h4>{{ $sellerStats['ongoing'] ?? 0 }}</h4>
                    <p class="{{ $sellerGrowth['ongoing']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['ongoing']['icon'] ?? '•' }}
                        {{ $sellerGrowth['ongoing']['label'] ?? 'No comparison data' }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-star"></i>
                    </div>
                    <small>Average Rating</small>
                    <h4>{{ $sellerStats['rating'] ?? '0.0 / 5.0' }}</h4>
                    <p class="{{ $sellerGrowth['rating']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['rating']['icon'] ?? '•' }}
                        {{ $sellerGrowth['rating']['label'] ?? 'No comparison data' }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-people"></i>
                    </div>
                    <small>Followers</small>
                    <h4>{{ $sellerStats['followers'] ?? 0 }}</h4>
                </div>


            </div>


            {{-- Incoming Rental Requests --}}
            @php
                $pendingRentalRequests = $incomingRentalRequests ?? collect();
            @endphp

            <div class="seller-panel mb-4">
                @if (session('success'))
                    <div class="alert alert-success rounded-3">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->has('rental_request') || $errors->has('rejection_reason'))
                    <div class="alert alert-danger rounded-3">
                        {{ $errors->first('rental_request')
                            ?: $errors->first('rejection_reason') }}
                    </div>
                @endif

                <div class="seller-panel-header mb-3">
                    <div>
                        <h6 class="mb-1">Incoming Rental Requests</h6>
                        <small class="text-muted">
                            Review pending requests before the rental period is confirmed.
                        </small>
                    </div>

                    <span class="badge rounded-pill text-bg-primary px-3 py-2">
                        {{ $pendingRentalRequests->count() }} Pending
                    </span>
                </div>

                @forelse ($pendingRentalRequests as $rentalRequest)
                    @php
                        $requestProduct = $rentalRequest->product;
                        $requestImagePath = $requestProduct?->primaryImage?->image_path;
                        $requestBorrower = $rentalRequest->borrower;
                    @endphp

                    <div class="border rounded-4 p-3 mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-auto">
                                <img
                                    src="{{ $requestImagePath
                                        ? asset('storage/' . $requestImagePath)
                                        : asset('images/placeholder-product.png') }}"
                                    alt="{{ $requestProduct?->title ?? 'Rental item' }}"
                                    class="rounded-3 border"
                                    style="width: 110px; height: 90px; object-fit: cover;"
                                >
                            </div>

                            <div class="col-12 col-lg">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h6 class="fw-bold mb-0">
                                        {{ $requestProduct?->title ?? 'Product unavailable' }}
                                    </h6>

                                    <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                        Pending Approval
                                    </span>
                                </div>

                                <div class="small text-muted mb-2">
                                    Request #{{ $rentalRequest->id }}
                                    · Submitted {{ $rentalRequest->created_at?->format('d M Y, H:i') }}
                                </div>

                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <span class="text-muted">Renter:</span>
                                        <strong>{{ $requestBorrower?->name ?? 'Unknown user' }}</strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Trust Score:</span>
                                        <strong>{{ $requestBorrower?->rating_avg_as_borrower ?? 'Unknown user' }}</strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Email:</span>
                                        <strong>{{ $requestBorrower?->email ?? '-' }}</strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Rental period:</span>
                                        <strong>
                                            {{ $rentalRequest->start_date?->format('d M Y') }}
                                            –
                                            {{ $rentalRequest->end_date?->format('d M Y') }}
                                        </strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Duration:</span>
                                        <strong>{{ $rentalRequest->total_days }} day(s)</strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Rental total:</span>
                                        <strong class="text-primary">
                                            Rp {{ number_format((float) $rentalRequest->total_price, 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">Deposit:</span>
                                        <strong>
                                            Rp {{ number_format((float) ($requestProduct?->deposit_amount ?? 0), 0, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>

                                @if ($rentalRequest->notes)
                                    <div class="mt-2 p-2 rounded-3 bg-light small">
                                        <span class="text-muted">Renter note:</span>
                                        {{ $rentalRequest->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-lg-auto">
                                <div class="d-flex flex-wrap flex-lg-column gap-2">
                                    <form
                                        action="{{ route(
                                            'borrower.store.rental-requests.approve',
                                            $rentalRequest
                                        ) }}"
                                        method="POST"
                                        onsubmit="return confirm('Setujui permintaan peminjaman ini?')"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="btn btn-success rounded-pill px-4 w-100"
                                        >
                                            <i class="bi bi-check-circle me-1"></i>
                                            Approve
                                        </button>
                                    </form>

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger rounded-pill px-4"
                                        data-bs-toggle="modal"
                                        data-bs-target="#rejectRentalRequestModal{{ $rentalRequest->id }}"
                                    >
                                        <i class="bi bi-x-circle me-1"></i>
                                        Reject
                                    </button>

                                    @if ($requestProduct?->slug)
                                        <a
                                            href="{{ route('products.show', $requestProduct->slug) }}"
                                            class="btn btn-outline-primary rounded-pill px-4"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            View Item
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Reject Rental Request Modal --}}
                    <div
                        class="modal fade"
                        id="rejectRentalRequestModal{{ $rentalRequest->id }}"
                        tabindex="-1"
                        aria-labelledby="rejectRentalRequestModalLabel{{ $rentalRequest->id }}"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4">
                                <form
                                    action="{{ route(
                                        'borrower.store.rental-requests.reject',
                                        $rentalRequest
                                    ) }}"
                                    method="POST"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div class="modal-header border-0 px-4 pt-4">
                                        <div>
                                            <h5
                                                class="modal-title fw-bold text-danger"
                                                id="rejectRentalRequestModalLabel{{ $rentalRequest->id }}"
                                            >
                                                Reject Rental Request
                                            </h5>

                                            <small class="text-muted">
                                                Explain why this request cannot be accepted.
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
                                                for="rejectionReason{{ $rentalRequest->id }}"
                                                class="form-label fw-semibold"
                                            >
                                                Rejection Reason
                                            </label>

                                            <textarea
                                                id="rejectionReason{{ $rentalRequest->id }}"
                                                name="rejection_reason"
                                                class="form-control"
                                                rows="4"
                                                maxlength="500"
                                                placeholder="Example: The item is undergoing maintenance during the selected period."
                                                required
                                            ></textarea>

                                            <small class="text-muted">
                                                This reason will be shown to the renter.
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
                                            Reject Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center border rounded-4 p-4">
                        <div class="fs-2 text-muted mb-2">
                            <i class="bi bi-inbox"></i>
                        </div>

                        <h6 class="fw-bold mb-1">No pending rental requests</h6>

                        <p class="text-muted small mb-0">
                            New rental requests for your store will appear here.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- DASHBOARD GRID --}}
            <div class="seller-grid">

                {{-- Performance Trend --}}
                <div class="seller-panel performance-panel">
                    <div class="seller-panel-header">
                        <div>
                            <h6>Performance Trend</h6>
                            <small class="text-muted">
                                Revenue and rental activity based on selected period
                            </small>
                        </div>

                        <select onchange="window.location.href='{{ url('/dashboard') }}?tab=store&revenue_period=' + this.value">
                            <option value="monthly" {{ ($revenuePeriod ?? 'monthly') === 'monthly' ? 'selected' : '' }}>
                                Monthly
                            </option>
                            <option value="weekly" {{ ($revenuePeriod ?? 'monthly') === 'weekly' ? 'selected' : '' }}>
                                Weekly
                            </option>
                        </select>
                    </div>

                    <div class="performance-chart-grid">
                        <div>
                            <h6 class="chart-subtitle">Revenue Stream</h6>
                            <div class="chart-box chart-large">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>

                        <div>
                            <h6 class="chart-subtitle">Renting Trend</h6>
                            <div class="chart-box chart-large">
                                <canvas id="rentingTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Revenue By Category --}}
                <div class="seller-panel category-panel">
                    <h6>Revenue By Category</h6>

                    <div class="category-content">
                        <div class="chart-box chart-donut">
                            <canvas id="categoryChart"></canvas>
                        </div>

                        <ul class="category-list">
                            @foreach ($categoryChart as $category => $value)
                                <li>
                                    <span></span>
                                    <div>
                                        <strong>{{ $category }}</strong>
                                        <small>Rp {{ number_format((float) $value, 0, ',', '.') }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>


                {{-- Monthly Recap --}}
                <div class="seller-panel recap-panel">
                    <h6>Monthly Recap</h6>

                    <table class="table table-sm seller-table mb-3">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($monthlyRecap as $recap)
                                <tr class="{{ $loop->last ? 'active-row' : '' }}">
                                    <td>{{ $recap['month'] }}</td>
                                    <td>Rp. {{ number_format($recap['revenue'], 0, ',', '.') }}</td>
                                    <td>{{ $recap['bookings'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="seller-success-note">
                        {{ $monthlyGrowthNote ?? 'Not enough previous month revenue data for comparison.' }}
                    </div>
                </div>

                {{-- Top Items --}}
                <div class="seller-panel top-items-panel">
                    <div class="seller-panel-header">
                        <h6>Top Items Rented</h6>
                        <a href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#topItemsModal">
                            See all
                        </a>
                    </div>

                    <div class="top-items-grid">
                        @forelse ($topItems as $index => $item)
                            @php
                                $itemName = $item->title ?? 'Item';
                                $itemPrice = $item->price_per_day ?? 0;
                                $imagePath = $item->primaryImage?->image_path;
                                $rentedCount = $item->rented_count ?? $item->total_rented ?? 0;
                            @endphp

                            <div class="top-item-card">
                                <div class="item-rank">{{ $index + 1 }}</div>

                                <img
                                    src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder-product.png') }}"
                                    alt="{{ $itemName }}"
                                >

                                <h6>{{ $itemName }}</h6>
                                <p>Rp {{ number_format($itemPrice, 0, ',', '.') }}/day</p>
                                <small class="text-muted">
                                    {{ $rentedCount }}x rented
                                </small>
                            </div>
                        @empty
                            <div class="text-muted small">
                                Belum ada item yang disewakan.
                            </div>
                        @endforelse
                    </div>

                    <div class="modal fade" id="topItemsModal" tabindex="-1" aria-labelledby="topItemsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 rounded-4">
                                <div class="modal-header border-0 px-4 pt-4">
                                    <div>
                                        <h5 class="modal-title fw-bold text-primary" id="topItemsModalLabel">
                                            All Rented Items
                                        </h5>
                                        <small class="text-muted">
                                            Complete list of your store items and rental frequency.
                                        </small>
                                    </div>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body px-4 pb-4">
                                    <div class="all-items-modal-grid">
                                        @forelse ($allTopItems as $index => $item)
                                            @php
                                                $itemName = $item->title ?? 'Item';
                                                $itemPrice = $item->price_per_day ?? 0;
                                                $imagePath = $item->primaryImage?->image_path;
                                                $rentedCount = $item->rented_count ?? $item->total_rented ?? 0;
                                            @endphp

                                            <div class="all-item-card">
                                                <div class="all-item-rank">
                                                    #{{ $index + 1 }}
                                                </div>

                                                <img
                                                    src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder-product.png') }}"
                                                    alt="{{ $itemName }}"
                                                >

                                                <div class="all-item-info">
                                                    <h6>{{ $itemName }}</h6>
                                                    <p>Rp {{ number_format($itemPrice, 0, ',', '.') }}/day</p>

                                                    <span class="rented-badge">
                                                        {{ $rentedCount }}x rented
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted">
                                                Belum ada item yang disewakan.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Store Items Management --}}
                <div class="seller-panel store-items-panel">
                    <div class="seller-panel-header">
                        <div>
                            <h6>Store Items</h6>
                            <small class="text-muted">Manage your listed rental items.</small>
                        </div>

                        <button
                            type="button"
                            class="btn-add-store-item"
                            data-bs-toggle="modal"
                            data-bs-target="#addStoreItemModal"
                        >
                            + Add Item
                        </button>
                    </div>

                    <div class="store-items-list">
                        @forelse ($storeItems as $item)
                            @php
                                $itemName = $item->title ?? 'Item';
                                $itemPrice = $item->price_per_day ?? 0;
                                $imagePath = $item->primaryImage?->image_path;
                            @endphp

                            <div class="store-item-row">
                                <div class="store-item-info">
                                    <img
                                        src="{{ $imagePath
                                            ? asset('storage/' . $imagePath)
                                            : asset('images/placeholder-product.png') }}"
                                        alt="{{ $itemName }}"
                                    >

                                    <div>
                                        <h6>{{ $itemName }}</h6>

                                        <p>
                                            Rp {{ number_format((float) $itemPrice, 0, ',', '.') }}/day
                                        </p>

                                        <div class="store-item-meta">
                                            <span>
                                                {{ $item->category?->name ?? 'No Category' }}
                                            </span>

                                            <span
                                                class="{{ ($item->status ?? 'inactive') === 'active'
                                                    ? 'item-active'
                                                    : 'item-inactive' }}"
                                            >
                                                {{ ucfirst($item->status ?? 'inactive') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary rounded-pill px-4 fw-semibold"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editStoreItemModal{{ $item->id }}"
                                    >
                                        Edit
                                    </button>

                                    <form
                                        action="{{ route('borrower.store.products.delete', $item) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin mau hapus item ini dari store?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn-delete-store-item">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="store-items-empty">
                                Belum ada item di store kamu.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Edit Store Item Modals --}}
                @foreach ($storeItems as $item)
                    @php
                        $editImagePath = $item->primaryImage?->image_path;
                    @endphp

                    <div
                        class="modal fade"
                        id="editStoreItemModal{{ $item->id }}"
                        tabindex="-1"
                        aria-labelledby="editStoreItemModalLabel{{ $item->id }}"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 rounded-4">
                                <form
                                    action="{{ route('borrower.store.products.update', $item) }}"
                                    method="POST"
                                    enctype="multipart/form-data"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <div class="modal-header border-0 px-4 pt-4">
                                        <div>
                                            <h5
                                                class="modal-title fw-bold text-primary"
                                                id="editStoreItemModalLabel{{ $item->id }}"
                                            >
                                                Edit Store Item
                                            </h5>

                                            <small class="text-muted">
                                                Update the information and availability of this item.
                                            </small>
                                        </div>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="Close"
                                        ></button>
                                    </div>

                                    <div class="modal-body px-4 pb-2">
                                        <div class="mb-3">
                                            <label
                                                for="editProductTitle{{ $item->id }}"
                                                class="form-label fw-bold"
                                            >
                                                Item Name
                                            </label>

                                            <input
                                                type="text"
                                                id="editProductTitle{{ $item->id }}"
                                                name="title"
                                                class="form-control"
                                                value="{{ $item->title }}"
                                                maxlength="150"
                                                required
                                            >
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label
                                                    for="editProductCategory{{ $item->id }}"
                                                    class="form-label fw-bold"
                                                >
                                                    Category
                                                </label>

                                                <select
                                                    id="editProductCategory{{ $item->id }}"
                                                    name="category_id"
                                                    class="form-select"
                                                    required
                                                >
                                                    <option value="">Select category</option>

                                                    @foreach ($categories as $category)
                                                        <option
                                                            value="{{ $category->id }}"
                                                            @selected(
                                                                (string) $item->category_id
                                                                === (string) $category->id
                                                            )
                                                        >
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label
                                                    for="editProductCondition{{ $item->id }}"
                                                    class="form-label fw-bold"
                                                >
                                                    Item Condition
                                                </label>

                                                <select
                                                    id="editProductCondition{{ $item->id }}"
                                                    name="condition"
                                                    class="form-select"
                                                    required
                                                >
                                                    <option
                                                        value="new"
                                                        @selected($item->condition === 'new')
                                                    >
                                                        New
                                                    </option>

                                                    <option
                                                        value="like_new"
                                                        @selected($item->condition === 'like_new')
                                                    >
                                                        Like New
                                                    </option>

                                                    <option
                                                        value="good"
                                                        @selected($item->condition === 'good')
                                                    >
                                                        Good Condition
                                                    </option>

                                                    <option
                                                        value="fair"
                                                        @selected($item->condition === 'fair')
                                                    >
                                                        Fair Condition
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label
                                                for="editProductDescription{{ $item->id }}"
                                                class="form-label fw-bold"
                                            >
                                                Description
                                            </label>

                                            <textarea
                                                id="editProductDescription{{ $item->id }}"
                                                name="description"
                                                class="form-control"
                                                rows="4"
                                                required
                                            >{{ $item->description }}</textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label
                                                    for="editProductPrice{{ $item->id }}"
                                                    class="form-label fw-bold"
                                                >
                                                    Price per Day
                                                </label>

                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>

                                                    <input
                                                        type="number"
                                                        id="editProductPrice{{ $item->id }}"
                                                        name="price_per_day"
                                                        class="form-control"
                                                        value="{{ (int) $item->price_per_day }}"
                                                        min="0"
                                                        step="1000"
                                                        required
                                                    >
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label
                                                    for="editProductDeposit{{ $item->id }}"
                                                    class="form-label fw-bold"
                                                >
                                                    Deposit Amount
                                                </label>

                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>

                                                    <input
                                                        type="number"
                                                        id="editProductDeposit{{ $item->id }}"
                                                        name="deposit_amount"
                                                        class="form-control"
                                                        value="{{ (int) ($item->deposit_amount ?? 0) }}"
                                                        min="0"
                                                        step="1000"
                                                        required
                                                    >
                                                </div>

                                                <small class="text-muted">
                                                    Enter 0 if no deposit is required.
                                                </small>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5 mb-3">
                                                <label
                                                    for="editProductCity{{ $item->id }}"
                                                    class="form-label fw-bold"
                                                >
                                                    Location City
                                                </label>

                                                <input
                                                    type="text"
                                                    id="editProductCity{{ $item->id }}"
                                                    name="location_city"
                                                    class="form-control"
                                                    value="{{ $item->location_city }}"
                                                    maxlength="100"
                                                    required
                                                >
                                            </div>

                                            <div class="col-md-7 mb-3">
                                                <label
                                                    for="editProductLocationDetail{{ $item->id }}"
                                                    class="form-label fw-bold"
                                                >
                                                    Location Detail
                                                </label>

                                                <input
                                                    type="text"
                                                    id="editProductLocationDetail{{ $item->id }}"
                                                    name="location_detail"
                                                    class="form-control"
                                                    value="{{ $item->location_detail }}"
                                                    maxlength="255"
                                                >
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label
                                                for="editProductStatus{{ $item->id }}"
                                                class="form-label fw-bold"
                                            >
                                                Product Status
                                            </label>

                                            <select
                                                id="editProductStatus{{ $item->id }}"
                                                name="status"
                                                class="form-select"
                                                required
                                            >
                                                <option
                                                    value="active"
                                                    @selected($item->status === 'active')
                                                >
                                                    Active
                                                </option>

                                                <option
                                                    value="inactive"
                                                    @selected($item->status === 'inactive')
                                                >
                                                    Inactive
                                                </option>

                                                <option
                                                    value="draft"
                                                    @selected($item->status === 'draft')
                                                >
                                                    Draft
                                                </option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold d-block">
                                                Current Main Image
                                            </label>

                                            <img
                                                src="{{ $editImagePath
                                                    ? asset('storage/' . $editImagePath)
                                                    : asset('images/placeholder-product.png') }}"
                                                alt="{{ $item->title }}"
                                                class="rounded-3 border"
                                                style="width: 130px; height: 100px; object-fit: cover;"
                                            >
                                        </div>

                                        <div class="mb-3">
                                            <label
                                                for="editProductImages{{ $item->id }}"
                                                class="form-label fw-bold"
                                            >
                                                Replace Product Images
                                            </label>

                                            <input
                                                type="file"
                                                id="editProductImages{{ $item->id }}"
                                                name="images[]"
                                                class="form-control"
                                                accept="image/jpeg,image/png,image/webp"
                                                multiple
                                            >

                                            <small class="text-muted">
                                                Leave this empty to keep the existing images.
                                                Uploading new files will replace the existing image set.
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
                                            class="btn btn-primary rounded-pill px-4"
                                        >
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Add Store Item Modal --}}
                <div
                    class="modal fade"
                    id="addStoreItemModal"
                    tabindex="-1"
                    aria-labelledby="addStoreItemModalLabel"
                    aria-hidden="true"
                >
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content border-0 rounded-4">

                            <form
                                action="{{ route('borrower.store.products.store') }}"
                                method="POST"
                                enctype="multipart/form-data"
                            >
                                @csrf

                                {{-- Header --}}
                                <div class="modal-header border-0 px-4 pt-4">
                                    <div>
                                        <h5
                                            class="modal-title fw-bold text-primary"
                                            id="addStoreItemModalLabel"
                                        >
                                            Add Store Item
                                        </h5>

                                        <small class="text-muted">
                                            Add complete information about your rental item.
                                        </small>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"
                                    ></button>
                                </div>

                                {{-- Body --}}
                                <div class="modal-body px-4 pb-2">

                                    {{-- Item Name --}}
                                    <div class="mb-3">
                                        <label for="productTitle" class="form-label fw-bold">
                                            Item Name
                                        </label>

                                        <input
                                            type="text"
                                            id="productTitle"
                                            name="title"
                                            class="form-control @error('title') is-invalid @enderror"
                                            value="{{ old('title') }}"
                                            placeholder="Example: Canon EOS M50"
                                            maxlength="150"
                                            required
                                        >

                                        @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        {{-- Category --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productCategory" class="form-label fw-bold">
                                                Category
                                            </label>

                                            <select
                                                id="productCategory"
                                                name="category_id"
                                                class="form-select @error('category_id') is-invalid @enderror"
                                                required
                                            >
                                                <option value="">
                                                    Select category
                                                </option>

                                                @foreach ($categories as $category)
                                                    <option
                                                        value="{{ $category->id }}"
                                                        @selected(old('category_id') == $category->id)
                                                    >
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('category_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Condition --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productCondition" class="form-label fw-bold">
                                                Item Condition
                                            </label>

                                            <select
                                                id="productCondition"
                                                name="condition"
                                                class="form-select @error('condition') is-invalid @enderror"
                                                required
                                            >
                                                <option value="">
                                                    Select condition
                                                </option>

                                                <option
                                                    value="new"
                                                    @selected(old('condition') === 'new')
                                                >
                                                    New
                                                </option>

                                                <option
                                                    value="like_new"
                                                    @selected(old('condition') === 'like_new')
                                                >
                                                    Like New
                                                </option>

                                                <option
                                                    value="good"
                                                    @selected(old('condition') === 'good')
                                                >
                                                    Good Condition
                                                </option>

                                                <option
                                                    value="fair"
                                                    @selected(old('condition') === 'fair')
                                                >
                                                    Fair Condition
                                                </option>
                                            </select>

                                            @error('condition')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="mb-3">
                                        <label for="productDescription" class="form-label fw-bold">
                                            Description
                                        </label>

                                        <textarea
                                            id="productDescription"
                                            name="description"
                                            class="form-control @error('description') is-invalid @enderror"
                                            rows="4"
                                            placeholder="Describe the item, specifications, included accessories, and usage information..."
                                            required
                                        >{{ old('description') }}</textarea>

                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        {{-- Price --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productPrice" class="form-label fw-bold">
                                                Price per Day
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    Rp
                                                </span>

                                                <input
                                                    type="number"
                                                    id="productPrice"
                                                    name="price_per_day"
                                                    class="form-control @error('price_per_day') is-invalid @enderror"
                                                    value="{{ old('price_per_day') }}"
                                                    placeholder="50000"
                                                    min="0"
                                                    step="1000"
                                                    required
                                                >

                                                @error('price_per_day')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Deposit --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productDeposit" class="form-label fw-bold">
                                                Deposit Amount
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    Rp
                                                </span>

                                                <input
                                                    type="number"
                                                    id="productDeposit"
                                                    name="deposit_amount"
                                                    class="form-control @error('deposit_amount') is-invalid @enderror"
                                                    value="{{ old('deposit_amount', 0) }}"
                                                    placeholder="0"
                                                    min="0"
                                                    step="1000"
                                                    required
                                                >

                                                @error('deposit_amount')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <small class="text-muted">
                                                Enter 0 if no deposit is required.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- Location City --}}
                                        <div class="col-md-5 mb-3">
                                            <label for="productCity" class="form-label fw-bold">
                                                Location City
                                            </label>

                                            <input
                                                type="text"
                                                id="productCity"
                                                name="location_city"
                                                class="form-control @error('location_city') is-invalid @enderror"
                                                value="{{ old('location_city') }}"
                                                placeholder="Example: Bandung"
                                                maxlength="100"
                                                required
                                            >

                                            @error('location_city')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Location Detail --}}
                                        <div class="col-md-7 mb-3">
                                            <label for="productLocationDetail" class="form-label fw-bold">
                                                Location Detail
                                            </label>

                                            <input
                                                type="text"
                                                id="productLocationDetail"
                                                name="location_detail"
                                                class="form-control @error('location_detail') is-invalid @enderror"
                                                value="{{ old('location_detail') }}"
                                                placeholder="Example: Kecamatan Coblong"
                                                maxlength="255"
                                            >

                                            @error('location_detail')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Product Images --}}
                                    <div class="mb-3">
                                        <label for="productImages" class="form-label fw-bold">
                                            Product Images
                                        </label>

                                        <input
                                            type="file"
                                            id="productImages"
                                            name="images[]"
                                            class="form-control
                                                @error('images') is-invalid @enderror
                                                @error('images.*') is-invalid @enderror"
                                            accept="image/jpeg,image/png,image/webp"
                                            multiple
                                            required
                                        >

                                        <small class="text-muted">
                                            Maximum 5 images. The first image will become the main image.
                                        </small>

                                        @error('images')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        @error('images.*')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Status sementara --}}
                                    <input
                                        type="hidden"
                                        name="status"
                                        value="active"
                                    >
                                </div>

                                {{-- Footer --}}
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
                                        class="btn btn-primary rounded-pill px-4"
                                    >
                                        Save Item
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.initStoreCharts = function () {
    if (window.storeChartsLoaded) return;

    const revenueCanvas = document.getElementById('revenueChart');
    const trendCanvas = document.getElementById('rentingTrendChart');
    const categoryCanvas = document.getElementById('categoryChart');

    if (!revenueCanvas || !trendCanvas || !categoryCanvas) {
        return;
    }

    window.storeChartsLoaded = true;

    const revenueLabels = @json($revenueLabels ?? []);
    const revenueData = @json($revenueChart ?? []);
    
    const maxRevenue = Math.max(...revenueData, 0);

    function formatRupiahShort(value) {
        value = Number(value || 0);

        if (value >= 1000000) {
            return 'Rp ' + (value / 1000000).toFixed(1).replace('.0', '') + ' jt';
        }

        if (value >= 1000) {
            return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
        }

        return 'Rp ' + value.toLocaleString('id-ID');
    }

    const trendData = @json($rentingTrendChart ?? []);
    const categoryLabels = @json(array_keys($categoryChart ?? []));
    const categoryData = @json(array_values($categoryChart ?? []));

    const chartTextColor = '#6a6f80';
    const gridColor = '#eef0f6';

    new Chart(revenueCanvas, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [{
                data: revenueData,
                backgroundColor: '#0d3f9f',
                borderRadius: 5,
                barThickness: 9,
                maxBarThickness: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: Rp ' + Number(context.raw || 0).toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 8
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: maxRevenue === 0 ? 1000000 : undefined,
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        },
                        callback: function(value) {
                            return formatRupiahShort(value);
                        }
                    }
                }
            }
        }
    });

    new Chart(trendCanvas, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                data: trendData,
                borderColor: '#1c6bff',
                backgroundColor: 'rgba(28, 107, 255, 0.12)',
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#1c6bff',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 8
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });

    new Chart(categoryCanvas, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: [
                    '#08328D',
                    '#1C6BFF',
                    '#8C5CFF',
                    '#F9C846',
                    '#56C596',
                    '#FF6B6B',
                    '#20C997'
                ],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw;
                        }
                    }
                }
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get('tab') === 'store') {
        setTimeout(function () {
            window.initStoreCharts();
        }, 250);
    }
});
</script>
@endpush
