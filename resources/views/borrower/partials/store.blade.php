<div class="store-dashboard-wrapper py-4">

    @if (!$user->is_owner_active)
        {{-- EMPTY STORE STATE --}}
        <div class="empty-store-state">
            <h1>{{ __('ui.store.empty_store.title') }}</h1>

            <form action="{{ route('borrower.store.open') }}" method="POST">
                @csrf
                <button type="submit" class="btn-open-store">
                    {{ __('ui.store.empty_store.open_now') }}
                </button>
            </form>
        </div>
    @else
        {{-- SELLER DASHBOARD --}}
        <div class="seller-dashboard">

            <h5 class="seller-section-title mb-4">
                {{ __('ui.store.stats.title') }}
            </h5>

            {{-- STATS CARDS --}}
            <div class="seller-stats-grid mb-4">

                <div class="seller-stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <small>{{ __('ui.store.stats.income') }}</small>
                    <h4>Rp {{ number_format($sellerStats['income'] ?? 0, 0, ',', '.') }}</h4>
                    <p class="{{ $sellerGrowth['income']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['income']['icon'] ?? '•' }}
                        {{ $sellerGrowth['income']['label'] ?? __('ui.store.stats.no_comparison') }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <small>{{ __('ui.store.stats.transactions') }}</small>
                    <h4>{{ $sellerStats['transactions'] ?? 0 }}</h4>
                    <p class="{{ $sellerGrowth['transactions']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['transactions']['icon'] ?? '•' }}
                        {{ $sellerGrowth['transactions']['label'] ?? __('ui.store.stats.no_comparison') }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <small>{{ __('ui.store.stats.items') }}</small>
                    <h4>{{ $sellerStats['items'] ?? 0 }}</h4>
                    <p class="{{ $sellerGrowth['items']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['items']['icon'] ?? '•' }}
                        {{ $sellerGrowth['items']['label'] ?? __('ui.store.stats.no_comparison') }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <small>{{ __('ui.store.stats.ongoing') }}</small>
                    <h4>{{ $sellerStats['ongoing'] ?? 0 }}</h4>
                    <p class="{{ $sellerGrowth['ongoing']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['ongoing']['icon'] ?? '•' }}
                        {{ $sellerGrowth['ongoing']['label'] ?? __('ui.store.stats.no_comparison') }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-star"></i>
                    </div>
                    <small>{{ __('ui.store.stats.rating') }}</small>
                    <h4>{{ $sellerStats['rating'] ?? '0.0 / 5.0' }}</h4>
                    <p class="{{ $sellerGrowth['rating']['class'] ?? 'text-muted' }}">
                        {{ $sellerGrowth['rating']['icon'] ?? '•' }}
                        {{ $sellerGrowth['rating']['label'] ?? __('ui.store.stats.no_comparison') }}
                    </p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-people"></i>
                    </div>

                    <small>{{ __('ui.store.stats.followers') }}</small>

                    <h4>
                        {{ number_format(
                            $sellerStats['followers'] ?? 0,
                            0,
                            ',',
                            '.'
                        ) }}
                    </h4>

                    <p class="{{ $sellerGrowth['followers']['class']
                        ?? 'text-muted' }}"
                    >
                        {{ $sellerGrowth['followers']['icon'] ?? '•' }}

                        {{ $sellerGrowth['followers']['label']
                            ?? __('ui.store.stats.no_comparison') }}
                    </p>
                </div>
            </div>


            {{-- Incoming Rental Requests --}}
            @php
                $pendingRentalRequests = $incomingRentalRequests ?? collect();
            @endphp

            <div class="seller-panel mb-4">
                

                <div class="seller-panel-header mb-3">
                    <div>
                        <h6 class="mb-1">{{ __('ui.store.incoming.title') }}</h6>
                        <small class="text-muted">
                            {{ __('ui.store.incoming.subtitle') }}
                        </small>
                    </div>

                    <span class="badge rounded-pill text-bg-primary px-3 py-2">
                        {{ __('ui.store.incoming.pending_count', [
                            'count' => $pendingRentalRequests->count(),
                        ]) }}
                    </span>
                </div>

                @forelse ($pendingRentalRequests as $rentalRequest)
                    @php
                        $requestProduct = $rentalRequest->product;
                        $requestImagePath = $requestProduct?->primaryImage?->image_path;
                        $requestBorrower = $rentalRequest->borrower;

                        $isExpired = $rentalRequest->hasExpiredApprovalWindow();
                    @endphp

                    <div class="border rounded-4 p-3 mb-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-12 col-lg-auto">
                                <img
                                    src="{{ $requestImagePath
                                        ? asset('storage/' . $requestImagePath)
                                        : asset('images/placeholder-product.png') }}"
                                    alt="{{ $requestProduct?->title ?? __('ui.store.incoming.rental_item') }}"
                                    class="rounded-3 border"
                                    style="width: 110px; height: 90px; object-fit: cover;"
                                >
                            </div>

                            <div class="col-12 col-lg">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <h6 class="fw-bold mb-0">
                                        {{ $requestProduct?->title ?? __('ui.store.incoming.product_unavailable') }}
                                    </h6>

                                    @if ($isExpired)
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis">
                                            <i class="bi bi-clock-history me-1"></i>
                                            {{ __('ui.store.incoming.expired') }}
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis">
                                            {{ __('ui.store.incoming.pending_approval') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="small text-muted mb-2">
                                    {{ __('ui.store.incoming.request_submitted', [
                                        'id' => $rentalRequest->id,
                                        'date' => $rentalRequest->created_at
                                            ?->locale(app()->getLocale())
                                            ?->translatedFormat('d M Y, H:i'),
                                    ]) }}
                                </div>

                                <div class="row g-2 small">
                                    <div class="col-md-6">
                                        <span class="text-muted">{{ __('ui.store.incoming.renter') }}</span>
                                        <strong>{{ $requestBorrower?->name ?? __('ui.store.incoming.unknown_user') }}</strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">{{ __('ui.store.incoming.trust_score') }}</span>
                                        <strong>{{ $requestBorrower?->rating_avg_as_borrower
        ?? __('ui.store.incoming.unknown_user') }}</strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">{{ __('ui.store.incoming.email') }}</span>
                                        <strong>{{ $requestBorrower?->email ?? '-' }}</strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">{{ __('ui.store.incoming.rental_period') }}</span>
                                        <strong>
                                            {{ $rentalRequest->start_date
                                                ?->locale(app()->getLocale())
                                                ?->translatedFormat('d M Y') }}
                                            –
                                            {{ $rentalRequest->end_date
                                                ?->locale(app()->getLocale())
                                                ?->translatedFormat('d M Y') }}
                                        </strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">{{ __('ui.store.incoming.duration') }}</span>
                                        <strong>
                                            {{ trans_choice(
                                                'ui.store.incoming.duration_days',
                                                $rentalRequest->total_days,
                                                ['count' => $rentalRequest->total_days]
                                            ) }}
                                        </strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">{{ __('ui.store.incoming.rental_total') }}</span>
                                        <strong class="text-primary">
                                            Rp {{ number_format((float) $rentalRequest->total_price, 0, ',', '.') }}
                                        </strong>
                                    </div>

                                    <div class="col-md-6">
                                        <span class="text-muted">{{ __('ui.store.incoming.deposit') }}</span>
                                        <strong>
                                            Rp {{ number_format((float) ($requestProduct?->deposit_amount ?? 0), 0, ',', '.') }}
                                        </strong>
                                    </div>
                                </div>

                                @if ($rentalRequest->notes)
                                    <div class="mt-2 p-2 rounded-3 bg-light small">
                                        <span class="text-muted">{{ __('ui.store.incoming.renter_note') }}</span>
                                        {{ $rentalRequest->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-lg-auto">
                                <div class="d-flex flex-wrap flex-lg-column gap-2">

                                    @if ($isExpired)
                                        <div
                                            class="border rounded-3 px-3 py-2 text-center bg-light"
                                            style="min-width: 160px;"
                                        >
                                            <div class="fw-semibold text-secondary">
                                                <i class="bi bi-clock-history me-1"></i>
                                                {{ __('ui.store.incoming.request_expired') }}
                                            </div>

                                            <small class="text-muted">
                                                {{ __('ui.store.incoming.start_date_passed') }}
                                            </small>
                                        </div>
                                    @else
                                        <form
                                            action="{{ route(
                                                'borrower.store.rental-requests.approve',
                                                $rentalRequest
                                            ) }}"
                                            method="POST"
                                            data-swal-title="{{ __('ui.store.incoming.approve_confirm_title') }}"
                                            data-swal-confirm="{{ __('ui.store.incoming.approve_confirm_text') }}"
                                            data-swal-icon="question"
                                            data-swal-confirm-button="{{ __('ui.store.incoming.approve_confirm_button') }}"
                                            data-swal-cancel-button="{{ __('ui.cancel') }}"
                                            data-swal-confirm-color="#198754"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-success rounded-pill px-4 w-100"
                                            >
                                                <i class="bi bi-check-circle me-1"></i>
                                                {{ __('ui.store.incoming.approve') }}
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="btn btn-outline-danger rounded-pill px-4"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectRentalRequestModal{{ $rentalRequest->id }}"
                                        >
                                            <i class="bi bi-x-circle me-1"></i>
                                            {{ __('ui.store.incoming.reject') }}
                                        </button>
                                    @endif

                                    @if ($requestProduct?->slug)
                                        <a
                                            href="{{ route('products.show', $requestProduct->slug) }}"
                                            class="btn btn-outline-primary rounded-pill px-4"
                                            target="_blank"
                                            rel="noopener"
                                        >
                                            {{ __('ui.store.incoming.view_item') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if (! $isExpired)
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
                                                    {{ __('ui.store.incoming.reject_title') }}
                                                </h5>

                                                <small class="text-muted">
                                                    {{ __('ui.store.incoming.reject_subtitle') }}
                                                </small>
                                            </div>

                                            <button
                                                type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="{{ __('ui.close') }}"
                                            ></button>
                                        </div>

                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label
                                                    for="rejectionReason{{ $rentalRequest->id }}"
                                                    class="form-label fw-semibold"
                                                >
                                                    {{ __('ui.store.incoming.rejection_reason') }}
                                                </label>

                                                <textarea
                                                    id="rejectionReason{{ $rentalRequest->id }}"
                                                    name="rejection_reason"
                                                    class="form-control"
                                                    rows="4"
                                                    maxlength="500"
                                                    placeholder="{{ __('ui.store.incoming.rejection_placeholder') }}"
                                                    required
                                                ></textarea>

                                                <small class="text-muted">
                                                    {{ __('ui.store.incoming.rejection_help') }}
                                                </small>
                                            </div>
                                        </div>

                                        <div class="modal-footer border-0 px-4 pb-4">
                                            <button
                                                type="button"
                                                class="btn btn-light rounded-pill px-4"
                                                data-bs-dismiss="modal"
                                            >
                                                {{ __('ui.cancel') }}
                                            </button>

                                            <button
                                                type="submit"
                                                class="btn btn-danger rounded-pill px-4"
                                            >
                                                {{ __('ui.store.incoming.reject_button') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                @endif
                    @empty
                        <div class="text-center border rounded-4 p-4">
                            <div class="fs-2 text-muted mb-2">
                                <i class="bi bi-inbox"></i>
                            </div>

                            <h6 class="fw-bold mb-1">
                                {{ __('ui.store.incoming.empty_title') }}
                            </h6>

                            <p class="text-muted small mb-0">
                                {{ __('ui.store.incoming.empty_text') }}
                            </p>
                        </div>
                    @endforelse
                </div>

            {{-- Recent Store Transactions --}}
            @php
                $recentTransactions = $recentSellerTransactions ?? collect();

                $transactionStatusClasses = [
                    'PENDING' => 'bg-warning-subtle text-warning-emphasis',
                    'APPROVED' => 'bg-primary-subtle text-primary-emphasis',
                    'ONGOING' => 'bg-info-subtle text-info-emphasis',
                    'COMPLETED' => 'bg-success-subtle text-success-emphasis',
                    'CANCELLED' => 'bg-secondary-subtle text-secondary-emphasis',
                    'REJECTED' => 'bg-danger-subtle text-danger-emphasis',
                ];

                $disputeStatusClasses = [
                    'open' => 'bg-warning-subtle text-warning-emphasis',
                    'in_review' => 'bg-primary-subtle text-primary-emphasis',
                    'resolved' => 'bg-success-subtle text-success-emphasis',
                    'rejected' => 'bg-danger-subtle text-danger-emphasis',
                ];

                $disputeStatusLabels = [
                    'open' => __('ui.store.transactions.dispute_statuses.open'),
                    'in_review' => __('ui.store.transactions.dispute_statuses.in_review'),
                    'resolved' => __('ui.store.transactions.dispute_statuses.resolved'),
                    'rejected' => __('ui.store.transactions.dispute_statuses.rejected'),
                ];
            @endphp

            <div class="seller-panel mb-4">

                

                <div class="seller-panel-header mb-3">
                    <div>
                        <h6 class="mb-1">{{ __('ui.store.transactions.title') }}</h6>

                        <small class="text-muted">
                            {{ __('ui.store.transactions.subtitle') }}
                        </small>
                    </div>

                    <a
                        href="{{ route('borrower.store.transactions.history') }}"
                        class="btn btn-outline-primary rounded-pill px-4 fw-semibold"
                    >
                        {{ __('ui.store.transactions.view_all') }}
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle seller-table mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('ui.store.transactions.transaction') }}</th>
                                <th>{{ __('ui.store.transactions.item') }}</th>
                                <th>{{ __('ui.store.transactions.renter') }}</th>
                                <th>{{ __('ui.store.transactions.rental_period') }}</th>
                                <th>{{ __('ui.store.transactions.total') }}</th>
                                <th>{{ __('ui.store.transactions.status') }}</th>
                                <th class="text-end">{{ __('ui.store.transactions.action') }}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($recentTransactions as $transaction)
                                @php
                                    $product = $transaction->product;
                                    $borrower = $transaction->borrower;

                                    $status = strtoupper(
                                        (string) $transaction->status
                                    );

                                    $statusClass = $transactionStatusClasses[$status]
                                        ?? 'bg-light text-dark';

                                    $latestDispute = $transaction->latestDispute;

                                    $hasDispute = (bool) $latestDispute;

                                    $isDisputePending = $latestDispute && in_array(
                                        $latestDispute->status,
                                        [
                                            \App\Models\Dispute::STATUS_OPEN,
                                            \App\Models\Dispute::STATUS_IN_REVIEW,
                                        ],
                                        true
                                    );

                                    $isDisputeFinished = $latestDispute && in_array(
                                        $latestDispute->status,
                                        [
                                            \App\Models\Dispute::STATUS_RESOLVED,
                                            \App\Models\Dispute::STATUS_REJECTED,
                                        ],
                                        true
                                    );

                                    $canCancelDispute =
                                        $isDisputePending
                                        && (int) $latestDispute->reporter_id === (int) auth()->id();

                                    $canDispute = $status === 'COMPLETED' && ! $hasDispute;

                                    $imagePath = $product?->primaryImage?->image_path;
                                @endphp

                                <tr>
                                    <td>
                                        <strong>#{{ $transaction->id }}</strong>

                                        <div class="small text-muted">
                                            {{ $transaction->created_at
                                                ?->locale(app()->getLocale())
                                                ?->translatedFormat('d M Y, H:i') }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img
                                                src="{{ $imagePath
                                                    ? asset('storage/' . $imagePath)
                                                    : asset('images/placeholder-product.png') }}"
                                                alt="{{ $product?->title ?? __('ui.store.incoming.rental_item') }}"
                                                class="rounded-3 border"
                                                style="
                                                    width: 52px;
                                                    height: 52px;
                                                    object-fit: cover;
                                                "
                                            >

                                            <div>
                                                <strong>
                                                    {{ $product?->title ?? __('ui.store.incoming.product_unavailable') }}
                                                </strong>

                                                <div class="small text-muted">
                                                    {{ $product?->category?->name
        ?? __('ui.store.transactions.no_category') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $borrower?->name
        ?? __('ui.store.transactions.unknown_renter') }}
                                        </strong>

                                        <div class="small text-muted">
                                            {{ $borrower?->email ?? '-' }}
                                        </div>
                                    </td>

                                    <td>
                                        <div>
                                            {{ $transaction->start_date
                                                ?->locale(app()->getLocale())
                                                ?->translatedFormat('d M Y') }}
                                        </div>

                                        <div class="small text-muted">
                                            {{ __('ui.store.transactions.until', [
                                                'date' => $transaction->end_date
                                                    ?->locale(app()->getLocale())
                                                    ?->translatedFormat('d M Y'),
                                            ]) }}
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
                                        <span class="badge rounded-pill {{ $statusClass }}">
                                            {{ __(
                                                'ui.store.transactions.statuses.'
                                                . strtolower($status)
                                            ) }}
                                        </span>

                                        @if ($latestDispute)
                                            @php
                                                $disputeStatus = $latestDispute->status;

                                                $disputeStatusClass = $disputeStatusClasses[$disputeStatus]
                                                    ?? 'bg-secondary-subtle text-secondary-emphasis';

                                                $disputeStatusLabel = $disputeStatusLabels[$disputeStatus]
                                                    ?? ucwords(str_replace('_', ' ', $disputeStatus));
                                            @endphp

                                            <div class="mt-1">
                                                <span
                                                    class="badge rounded-pill {{ $disputeStatusClass }}"
                                                    @if ($latestDispute->resolution)
                                                        title="{{ $latestDispute->resolution }}"
                                                    @endif
                                                >
                                                    {{ __('ui.store.transactions.dispute_label', [
                                                        'status' => $disputeStatusLabel,
                                                    ]) }}
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
                                                data-bs-target="#sellerDisputeModal{{ $transaction->id }}"
                                            >
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                {{ __('ui.store.transactions.raise_dispute') }}
                                            </button>

                                        @elseif ($isDisputePending)
                                            <div class="d-inline-flex flex-column align-items-end gap-2">
                                                <span class="small text-muted">
                                                    <i class="bi bi-hourglass-split me-1"></i>
                                                    {{ __('ui.store.transactions.waiting_admin') }}
                                                </span>

                                                @if ($canCancelDispute)
                                                    <form
                                                        action="{{ route(
                                                            'borrower.store.disputes.destroy',
                                                            $latestDispute
                                                        ) }}"
                                                        method="POST"
                                                        data-swal-title="{{ __('ui.store.dispute.cancel_title') }}"
                                                        data-swal-confirm="{{ __('ui.store.dispute.cancel_text') }}"
                                                        data-swal-icon="warning"
                                                        data-swal-confirm-button="{{ __('ui.store.dispute.cancel_button') }}"
                                                        data-swal-cancel-button="{{ __('ui.store.dispute.keep_button') }}"
                                                        data-swal-confirm-color="#dc3545"
                                                    >
                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                                        >
                                                            <i class="bi bi-x-circle me-1"></i>
                                                            {{ __('ui.store.transactions.cancel_dispute') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>

                                        @elseif ($isDisputeFinished)
                                            <span class="small text-muted">
                                                —
                                            </span>

                                        @else
                                            <span class="small text-muted">
                                                {{ __('ui.store.transactions.no_action') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="fs-2 text-muted mb-2">
                                            <i class="bi bi-receipt"></i>
                                        </div>

                                        <h6 class="fw-bold mb-1">
                                            {{ __('ui.store.transactions.empty_title') }}
                                        </h6>

                                        <p class="small text-muted mb-0">
                                            {{ __('ui.store.transactions.empty_text') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Transaction Dispute Modals --}}
            @foreach ($recentTransactions as $transaction)
                @php
                    $modalStatus = strtoupper(
                        (string) $transaction->status
                    );

                    $modalCanDispute = $modalStatus === 'COMPLETED' && ! $transaction->latestDispute;
                @endphp

                @if ($modalCanDispute)
                    <div
                        class="modal fade"
                        id="sellerDisputeModal{{ $transaction->id }}"
                        tabindex="-1"
                        aria-labelledby="sellerDisputeModalLabel{{ $transaction->id }}"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 rounded-4">
                                <form
                                    action="{{ route( 'borrower.store.disputes.store', $transaction ) }}"
                                    method="POST"
                                    enctype="multipart/form-data"
                                >
                                    @csrf

                                    <div class="modal-header border-0 px-4 pt-4">
                                        <div>
                                            <h5
                                                class="modal-title fw-bold text-danger"
                                                id="sellerDisputeModalLabel{{ $transaction->id }}"
                                            >
                                                {{ __('ui.store.dispute.title') }}
                                            </h5>

                                            <small class="text-muted">
                                                {{ __('ui.store.dispute.transaction', [
                                                    'id' => $transaction->id,
                                                    'product' => $transaction->product?->title,
                                                ]) }}
                                            </small>
                                        </div>

                                        <button
                                            type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal"
                                            aria-label="{{ __('ui.close') }}"
                                        ></button>
                                    </div>

                                    <div class="modal-body px-4">
                                        <div class="mb-3">
                                            <label
                                                for="disputeReason{{ $transaction->id }}"
                                                class="form-label fw-semibold"
                                            >
                                                {{ __('ui.store.dispute.reason') }}
                                            </label>

                                            <textarea
                                                id="disputeReason{{ $transaction->id }}"
                                                name="reason"
                                                class="form-control"
                                                rows="5"
                                                minlength="20"
                                                maxlength="1000"
                                                placeholder="{{ __('ui.store.dispute.reason_placeholder') }}"
                                                required
                                            >{{ old('reason') }}</textarea>

                                            <small class="text-muted">
                                                {{ __('ui.store.dispute.min_chars') }}
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label
                                                for="disputeEvidence{{ $transaction->id }}"
                                                class="form-label fw-semibold"
                                            >
                                                {{ __('ui.store.dispute.evidence') }}
                                            </label>

                                            <input
                                                type="file"
                                                id="disputeEvidence{{ $transaction->id }}"
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
                                                {{ __('ui.store.dispute.evidence_help') }}
                                            </small>
                                        </div>

                                        <div class="alert alert-warning small mb-0">
                                            {{ __('ui.store.dispute.review_warning') }}
                                        </div>
                                    </div>

                                    <div class="modal-footer border-0 px-4 pb-4">
                                        <button
                                            type="button"
                                            class="btn btn-light rounded-pill px-4"
                                            data-bs-dismiss="modal"
                                        >
                                            {{ __('ui.cancel') }}
                                        </button>

                                        <button
                                            type="submit"
                                            class="btn btn-danger rounded-pill px-4"
                                        >
                                            {{ __('ui.store.dispute.submit') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
            {{-- DASHBOARD GRID --}}
            <div class="seller-grid">

                {{-- Performance Trend --}}
                <div class="seller-panel performance-panel">
                    <div class="seller-panel-header">
                        <div>
                            <h6>{{ __('ui.store.performance.title') }}</h6>
                            <small class="text-muted">
                                {{ __('ui.store.performance.subtitle') }}
                            </small>
                        </div>

                        <select onchange="window.location.href='{{ url('/dashboard') }}?tab=store&revenue_period=' + this.value">
                            <option value="monthly" {{ ($revenuePeriod ?? 'monthly') === 'monthly' ? 'selected' : '' }}>
                                {{ __('ui.store.performance.monthly') }}
                            </option>
                            <option value="weekly" {{ ($revenuePeriod ?? 'monthly') === 'weekly' ? 'selected' : '' }}>
                                {{ __('ui.store.performance.weekly') }}
                            </option>
                        </select>
                    </div>

                    <div class="performance-chart-grid">
                        <div>
                            <h6 class="chart-subtitle">
                                {{ __('ui.store.performance.revenue_stream') }}
                            </h6>
                            <div class="chart-box chart-large">
                                <canvas id="revenueChart"></canvas>
                            </div>
                        </div>

                        <div>
                            <h6 class="chart-subtitle">
                                {{ __('ui.store.performance.renting_trend') }}
                            </h6>
                            <div class="chart-box chart-large">
                                <canvas id="rentingTrendChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Revenue By Category --}}
                <div class="seller-panel category-panel">
                    <h6>{{ __('ui.store.performance.revenue_by_category') }}</h6>

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
                    <h6>{{ __('ui.store.performance.monthly_recap') }}</h6>

                    <table class="table table-sm seller-table mb-3">
                        <thead>
                            <tr>
                                <th>{{ __('ui.store.performance.month') }}</th>
                                <th>{{ __('ui.store.performance.revenue') }}</th>
                                <th>{{ __('ui.store.performance.bookings') }}</th>
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
                        {{ $monthlyGrowthNote
                            ?? __('ui.store.performance.no_growth_data') }}
                    </div>
                </div>

                {{-- Top Items --}}
                <div class="seller-panel top-items-panel">
                    <div class="seller-panel-header">
                        <h6>{{ __('ui.store.top_items.title') }}</h6>
                        <a href="#"
                            data-bs-toggle="modal"
                            data-bs-target="#topItemsModal">
                            {{ __('ui.store.top_items.see_all') }}
                        </a>
                    </div>

                    <div class="top-items-grid">
                        @forelse ($topItems as $index => $item)
                            @php
                                $itemName = $item->title
                                    ?? __('ui.store.transactions.item');
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
                                <p>Rp {{ number_format($itemPrice, 0, ',', '.') }}{{ __('ui.per_day') }}</p>
                                <small class="text-muted">
                                    {{ trans_choice(
                                        'ui.store.top_items.times_rented',
                                        $rentedCount,
                                        ['count' => $rentedCount]
                                    ) }}
                                </small>
                            </div>
                        @empty
                            <div class="text-muted small">
                                {{ __('ui.store.top_items.empty') }}
                            </div>
                        @endforelse
                    </div>

                    <div class="modal fade" id="topItemsModal" tabindex="-1" aria-labelledby="topItemsModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 rounded-4">
                                <div class="modal-header border-0 px-4 pt-4">
                                    <div>
                                        <h5 class="modal-title fw-bold text-primary" id="topItemsModalLabel">
                                            {{ __('ui.store.top_items.all_title') }}
                                        </h5>
                                        <small class="text-muted">
                                            {{ __('ui.store.top_items.all_subtitle') }}
                                        </small>
                                    </div>

                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('ui.close') }}"></button>
                                </div>

                                <div class="modal-body px-4 pb-4">
                                    <div class="all-items-modal-grid">
                                        @forelse ($allTopItems as $index => $item)
                                            @php
                                                $itemName = $item->title
                                    ?? __('ui.store.transactions.item');
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
                                                    <p>Rp {{ number_format($itemPrice, 0, ',', '.') }}{{ __('ui.per_day') }}</p>

                                                    <span class="rented-badge">
                                                        {{ trans_choice(
                                                    'ui.store.top_items.times_rented',
                                                    $rentedCount,
                                                    ['count' => $rentedCount]
                                                ) }}
                                                    </span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-muted">
                                                {{ __('ui.store.top_items.empty') }}
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
                            <h6>{{ __('ui.store.items.title') }}</h6>
                            <small class="text-muted">
                                {{ __('ui.store.items.subtitle') }}
                            </small>
                        </div>

                        <button
                            type="button"
                            class="btn-add-store-item"
                            data-bs-toggle="modal"
                            data-bs-target="#addStoreItemModal"
                        >
                            {{ __('ui.store.items.add') }}
                        </button>
                    </div>

                    <div class="store-items-list">
                        @forelse ($storeItems as $item)
                            @php
                                $itemName = $item->title
                                    ?? __('ui.store.transactions.item');
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
                                            Rp {{ number_format((float) $itemPrice, 0, ',', '.') }}{{ __('ui.per_day') }}
                                        </p>

                                        <div class="store-item-meta">
                                            <span>
                                                {{ $item->category?->name
                                                    ?? __('ui.store.items.no_category') }}
                                            </span>

                                            <span
                                                class="{{ ($item->status ?? 'inactive') === 'active'
                                                    ? 'item-active'
                                                    : 'item-inactive' }}"
                                            >
                                                {{ __(
                                                    'ui.store.form.statuses.'
                                                    . ($item->status ?? 'inactive')
                                                ) }}
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
                                        {{ __('ui.store.items.edit') }}
                                    </button>

                                    <form
                                        action="{{ route(
                                            'borrower.store.products.delete',
                                            $item
                                        ) }}"
                                        method="POST"
                                        data-swal-title="{{ __('ui.store.items.delete_title') }}"
                                        data-swal-confirm="{{ __('ui.store.items.delete_text') }}"
                                        data-swal-icon="warning"
                                        data-swal-confirm-button="{{ __('ui.store.items.delete_button') }}"
                                        data-swal-cancel-button="{{ __('ui.cancel') }}"
                                        data-swal-confirm-color="#dc3545"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn-delete-store-item">
                                            {{ __('ui.store.items.delete') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="store-items-empty">
                                {{ __('ui.store.items.empty') }}
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
                            <form
                                action="{{ route('borrower.store.products.update', $item) }}"
                                class="modal-content border-0 rounded-4"
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
                                            {{ __('ui.store.form.edit_title') }}
                                        </h5>

                                        <small class="text-muted">
                                            {{ __('ui.store.form.edit_subtitle') }}
                                        </small>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="{{ __('ui.close') }}"
                                    ></button>
                                </div>

                                <div class="modal-body px-4 pb-2">
                                    <div class="mb-3">
                                        <label
                                            for="editProductTitle{{ $item->id }}"
                                            class="form-label fw-bold"
                                        >
                                            {{ __('ui.store.form.item_name') }}
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
                                                {{ __('ui.store.form.category') }}
                                            </label>

                                            <select
                                                id="editProductCategory{{ $item->id }}"
                                                name="category_id"
                                                class="form-select"
                                                required
                                            >
                                                <option value="">
                                                    {{ __('ui.store.form.select_category') }}
                                                </option>

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
                                                {{ __('ui.store.form.condition') }}
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
                                                    {{ __('ui.store.form.conditions.new') }}
                                                </option>

                                                <option
                                                    value="like_new"
                                                    @selected($item->condition === 'like_new')
                                                >
                                                    {{ __('ui.store.form.conditions.like_new') }}
                                                </option>

                                                <option
                                                    value="good"
                                                    @selected($item->condition === 'good')
                                                >
                                                    {{ __('ui.store.form.conditions.good') }}
                                                </option>

                                                <option
                                                    value="fair"
                                                    @selected($item->condition === 'fair')
                                                >
                                                    {{ __('ui.store.form.conditions.fair') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label
                                            for="editProductDescription{{ $item->id }}"
                                            class="form-label fw-bold"
                                        >
                                            {{ __('ui.store.form.description') }}
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
                                                {{ __('ui.store.form.price_per_day') }}
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
                                                {{ __('ui.store.form.deposit_amount') }}
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
                                                {{ __('ui.store.form.deposit_help') }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5 mb-3">
                                            <label
                                                for="editProductCity{{ $item->id }}"
                                                class="form-label fw-bold"
                                            >
                                                {{ __('ui.store.form.location_city') }}
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
                                                {{ __('ui.store.form.location_detail') }}
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
                                            {{ __('ui.store.form.status') }}
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
                                                {{ __('ui.store.form.statuses.active') }}
                                            </option>

                                            <option
                                                value="inactive"
                                                @selected($item->status === 'inactive')
                                            >
                                                {{ __('ui.store.form.statuses.inactive') }}
                                            </option>

                                            <option
                                                value="draft"
                                                @selected($item->status === 'draft')
                                            >
                                                {{ __('ui.store.form.statuses.draft') }}
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-bold d-block">
                                            {{ __('ui.store.form.current_image') }}
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
                                            {{ __('ui.store.form.replace_images') }}
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
                                            {{ __('ui.store.form.replace_images_help') }}
                                        </small>
                                    </div>
                                </div>

                                <div class="modal-footer border-0 px-4 pb-4">
                                    <button
                                        type="button"
                                        class="btn btn-light rounded-pill px-4"
                                        data-bs-dismiss="modal"
                                    >
                                        {{ __('ui.cancel') }}
                                    </button>

                                    <button
                                        type="submit"
                                        class="btn btn-primary rounded-pill px-4"
                                    >
                                        {{ __('ui.store.form.save_changes') }}
                                    </button>
                                </div>
                            </form>
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
                                            {{ __('ui.store.form.add_title') }}
                                        </h5>

                                        <small class="text-muted">
                                            {{ __('ui.store.form.add_subtitle') }}
                                        </small>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="{{ __('ui.close') }}"
                                    ></button>
                                </div>

                                {{-- Body --}}
                                <div class="modal-body px-4 pb-2">

                                    {{-- Item Name --}}
                                    <div class="mb-3">
                                        <label for="productTitle" class="form-label fw-bold">
                                            {{ __('ui.store.form.item_name') }}
                                        </label>

                                        <input
                                            type="text"
                                            id="productTitle"
                                            name="title"
                                            class="form-control @error('title', 'addProduct') is-invalid @enderror"
                                            value="{{ old('title') }}"
                                            placeholder="{{ __('ui.store.form.placeholders.item_name') }}"
                                            maxlength="150"
                                            required
                                        >

                                        @error('title', 'addProduct')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        {{-- Category --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productCategory" class="form-label fw-bold">
                                                {{ __('ui.store.form.category') }}
                                            </label>

                                            <select
                                                id="productCategory"
                                                name="category_id"
                                                class="form-select @error('category_id', 'addProduct') is-invalid @enderror"
                                                required
                                            >
                                                <option value="">
                                                    {{ __('ui.store.form.select_category') }}
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

                                            @error('category_id', 'addProduct')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Condition --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productCondition" class="form-label fw-bold">
                                                {{ __('ui.store.form.condition') }}
                                            </label>

                                            <select
                                                id="productCondition"
                                                name="condition"
                                                class="form-select @error('condition', 'addProduct') is-invalid @enderror"
                                                required
                                            >
                                                <option value="">
                                                    {{ __('ui.store.form.select_condition') }}
                                                </option>

                                                <option
                                                    value="new"
                                                    @selected(old('condition') === 'new')
                                                >
                                                    {{ __('ui.store.form.conditions.new') }}
                                                </option>

                                                <option
                                                    value="like_new"
                                                    @selected(old('condition') === 'like_new')
                                                >
                                                    {{ __('ui.store.form.conditions.like_new') }}
                                                </option>

                                                <option
                                                    value="good"
                                                    @selected(old('condition') === 'good')
                                                >
                                                    {{ __('ui.store.form.conditions.good') }}
                                                </option>

                                                <option
                                                    value="fair"
                                                    @selected(old('condition') === 'fair')
                                                >
                                                    {{ __('ui.store.form.conditions.fair') }}
                                                </option>
                                            </select>

                                            @error('condition', 'addProduct')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="mb-3">
                                        <label for="productDescription" class="form-label fw-bold">
                                            {{ __('ui.store.form.description') }}
                                        </label>

                                        <textarea
                                            id="productDescription"
                                            name="description"
                                            class="form-control @error('description', 'addProduct') is-invalid @enderror"
                                            rows="4"
                                            placeholder="{{ __('ui.store.form.placeholders.description') }}"
                                            required
                                        >{{ old('description') }}</textarea>

                                        @error('description', 'addProduct')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        {{-- Price --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productPrice" class="form-label fw-bold">
                                                {{ __('ui.store.form.price_per_day') }}
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    Rp
                                                </span>

                                                <input
                                                    type="number"
                                                    id="productPrice"
                                                    name="price_per_day"
                                                    class="form-control @error('price_per_day', 'addProduct') is-invalid @enderror"
                                                    value="{{ old('price_per_day') }}"
                                                    placeholder="50000"
                                                    min="0"
                                                    step="1000"
                                                    required
                                                >

                                                @error('price_per_day', 'addProduct')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Deposit --}}
                                        <div class="col-md-6 mb-3">
                                            <label for="productDeposit" class="form-label fw-bold">
                                                {{ __('ui.store.form.deposit_amount') }}
                                            </label>

                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    Rp
                                                </span>

                                                <input
                                                    type="number"
                                                    id="productDeposit"
                                                    name="deposit_amount"
                                                    class="form-control @error('deposit_amount', 'addProduct') is-invalid @enderror"
                                                    value="{{ old('deposit_amount', 0) }}"
                                                    placeholder="0"
                                                    min="0"
                                                    step="1000"
                                                    required
                                                >

                                                @error('deposit_amount', 'addProduct')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <small class="text-muted">
                                                {{ __('ui.store.form.deposit_help') }}
                                            </small>
                                        </div>
                                    </div>

                                    <div class="row">
                                        {{-- Location City --}}
                                        <div class="col-md-5 mb-3">
                                            <label for="productCity" class="form-label fw-bold">
                                                {{ __('ui.store.form.location_city') }}
                                            </label>

                                            <input
                                                type="text"
                                                id="productCity"
                                                name="location_city"
                                                class="form-control @error('location_city', 'addProduct') is-invalid @enderror"
                                                value="{{ old('location_city') }}"
                                                placeholder="{{ __('ui.store.form.placeholders.city') }}"
                                                maxlength="100"
                                                required
                                            >

                                            @error('location_city', 'addProduct')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        {{-- Location Detail --}}
                                        <div class="col-md-7 mb-3">
                                            <label for="productLocationDetail" class="form-label fw-bold">
                                                {{ __('ui.store.form.location_detail') }}
                                            </label>

                                            <input
                                                type="text"
                                                id="productLocationDetail"
                                                name="location_detail"
                                                class="form-control @error('location_detail', 'addProduct') is-invalid @enderror"
                                                value="{{ old('location_detail') }}"
                                                placeholder="{{ __('ui.store.form.placeholders.location_detail') }}"
                                                maxlength="255"
                                            >

                                            @error('location_detail', 'addProduct')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Product Images --}}
                                    <div class="mb-3">
                                        <label for="productImages" class="form-label fw-bold">
                                            {{ __('ui.store.form.product_images') }}
                                        </label>

                                        <input
                                            type="file"
                                            id="productImages"
                                            name="images[]"
                                            class="form-control
                                                @error('images', 'addProduct') is-invalid @enderror
                                                @error('images.*', 'addProduct') is-invalid @enderror"
                                            accept="image/jpeg,image/png,image/webp"
                                            multiple
                                            required
                                        >

                                        <small class="text-muted">
                                            {{ __('ui.store.form.product_images_help') }}
                                        </small>

                                        @error('images', 'addProduct')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                        @error('images.*', 'addProduct')
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
                                        {{ __('ui.cancel') }}
                                    </button>

                                    <button
                                        type="submit"
                                        class="btn btn-primary rounded-pill px-4"
                                    >
                                        {{ __('ui.store.form.save_item') }}
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

    const numberLocale = @json(
        app()->getLocale() === 'id'
            ? 'id-ID'
            : 'en-US'
    );

    const revenueText = @json(
        __('ui.store.performance.revenue')
    );

    const millionAbbr = @json(
        __('ui.store.performance.million_abbr')
    );

    const thousandAbbr = @json(
        __('ui.store.performance.thousand_abbr')
    );
    
    const maxRevenue = Math.max(...revenueData, 0);

    function formatRupiahShort(value) {
        value = Number(value || 0);

        if (value >= 1000000) {
            return 'Rp '
                + (value / 1000000)
                    .toFixed(1)
                    .replace('.0', '')
                + ' '
                + millionAbbr;
        }

        if (value >= 1000) {
            return 'Rp '
                + (value / 1000).toFixed(0)
                + ' '
                + thousandAbbr;
        }

        return 'Rp ' + value.toLocaleString(numberLocale);
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
                            return revenueText
                                + ': Rp '
                                + Number(context.raw || 0)
                                    .toLocaleString(numberLocale);
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
                            return context.label
                                + ': Rp '
                                + Number(context.raw || 0)
                                    .toLocaleString(numberLocale);
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
@if ($errors->addProduct->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById(
                'addStoreItemModal'
            );

            if (
                modalElement &&
                typeof bootstrap !== 'undefined'
            ) {
                bootstrap.Modal
                    .getOrCreateInstance(modalElement)
                    .show();
            }
        });
    </script>
@endif

<script>
document.addEventListener('submit', async function (event) {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    const confirmationMessage = form.dataset.swalConfirm;

    if (
        !confirmationMessage ||
        form.dataset.swalConfirmed === 'true'
    ) {
        return;
    }

    event.preventDefault();

    const result = await window.Swal.fire({
        icon: form.dataset.swalIcon || 'warning',
        title: form.dataset.swalTitle
            || @js(__('ui.store.alerts.confirm_title')),
        text: confirmationMessage,
        showCancelButton: true,
        confirmButtonText:
            form.dataset.swalConfirmButton
            || @js(__('ui.store.alerts.confirm_button')),
        cancelButtonText:
            form.dataset.swalCancelButton
            || @js(__('ui.cancel')),
        confirmButtonColor:
            form.dataset.swalConfirmColor || '#0031e1',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusCancel: true,
    });

    if (!result.isConfirmed) {
        return;
    }

    form.dataset.swalConfirmed = 'true';

    if (event.submitter) {
        form.requestSubmit(event.submitter);
        return;
    }

    form.requestSubmit();
});
</script>

@php
    $storeFlashIcon = null;
    $storeFlashTitle = null;
    $storeFlashMessage = null;

    if (session('success')) {
        $storeFlashIcon = 'success';
        $storeFlashTitle = __('ui.store.alerts.success');
        $storeFlashMessage = session('success');
    } elseif (session('error')) {
        $storeFlashIcon = 'error';
        $storeFlashTitle = __('ui.store.alerts.failed');
        $storeFlashMessage = session('error');
    } elseif (session('warning')) {
        $storeFlashIcon = 'warning';
        $storeFlashTitle = __('ui.store.alerts.warning');
        $storeFlashMessage = session('warning');
    }

    $storeActionError =
        $errors->first('rental_request')
        ?: $errors->first('rejection_reason')
        ?: $errors->first('dispute');

    if (! $storeFlashMessage && $storeActionError) {
        $storeFlashIcon = 'error';
        $storeFlashTitle = __('ui.store.alerts.failed');
        $storeFlashMessage = $storeActionError;
    }
@endphp

@if ($storeFlashMessage)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (!window.Swal) {
                return;
            }

            window.Swal.fire({
                icon: @js($storeFlashIcon),
                title: @js($storeFlashTitle),
                text: @js($storeFlashMessage),
                confirmButtonText: @js(
                    __('ui.store.alerts.ok')
                ),
                confirmButtonColor: '#0031e1',
            });
        });
    </script>
@endif
@endpush
