{{-- SI-RENT Dashboard: Activity Tab —” Ongoing + History sub-tabs --}}
<div x-data="{ activityTab: 'ongoing' }">

    {{-- Sub-tabs: Ongoing / History --}}
    <nav class="d-flex gap-4 mb-4" style="font-family:'Mona Sans',sans-serif; border-bottom:2px solid #e2e2e2;">
        <a href="#" @click.prevent="activityTab='ongoing'"
           class="text-decoration-none pb-2 fw-semibold position-relative" style="font-size:16px;"
           :style="activityTab==='ongoing' ? 'color:#204be5' : 'color:#6a6a6a'">
            {{ __('ui.ongoing') }}
            <span x-show="activityTab==='ongoing'" class="position-absolute bottom-0 start-0 w-100" style="height:2px; background:#204be5;"></span>
        </a>
        <a href="#" @click.prevent="activityTab='history'"
           class="text-decoration-none pb-2 fw-semibold position-relative" style="font-size:16px;"
           :style="activityTab==='history' ? 'color:#204be5' : 'color:#6a6a6a'">
            {{ __('ui.history') }}
            <span x-show="activityTab==='history'" class="position-absolute bottom-0 start-0 w-100" style="height:2px; background:#204be5;"></span>
        </a>
    </nav>

    {{-- ===== ONGOING ===== --}}
    <div x-show="activityTab === 'ongoing'">
        <div class="row">
            <div class="col-lg-8">
                {{-- Calendar Placeholder --}}
                <div class="bg-white rounded-4 p-4 shadow-sm border mb-4" style="border-color: var(--border-default);">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold" style="font-family:'Mona Sans',sans-serif; font-size:20px;">{{ now()->format('F Y') }}</span>
                        <button class="btn btn-outline-secondary btn-sm rounded-3" style="font-family:'Mona Sans',sans-serif; font-size:14px;">{{ __('ui.clear') }}</button>
                    </div>
                    <table class="table table-borderless text-center mb-0">
                        <thead>
                            <tr style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-muted);">
                                <th>Su</th><th>Mo</th><th>Tu</th><th>We</th><th>Th</th><th>Fr</th><th>Sa</th>
                            </tr>
                        </thead>
                        <tbody style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                            @php
                                $start = now()->startOfMonth()->dayOfWeek;
                                $days = now()->daysInMonth;
                                $day = 1;
                            @endphp
                            @for($w = 0; $w < 6 && $day <= $days; $w++)
                                <tr>
                                    @for($d = 0; $d < 7; $d++)
                                        @if(($w === 0 && $d < $start) || $day > $days)
                                            <td></td>
                                        @else
                                            <td class="rounded-2" style="cursor:pointer; {{ in_array(sprintf('%d-%02d-%02d', now()->year, now()->month, $day), array_map(fn($r) => $r->start_date->format('Y-m-d'), $ongoingRequests->all())) ? 'background:#0031e1; color:#fff;' : '' }}">
                                                {{ $day }}
                                            </td>
                                            @php $day++ @endphp
                                        @endif
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                {{-- Ongoing Transactions --}}
                @forelse($ongoingRequests as $request)
                    <div class="bg-white rounded-4 p-4 shadow-sm border mb-3" style="border-color: var(--border-default);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <x-shared.status-badge :status="$request->status" />
                                <h5 class="fw-semibold mt-2 mb-1" style="font-family:'Mona Sans',sans-serif; font-size:18px;">
                                    {{ $request->product->title ?? 'Product' }}
                                </h5>
                                <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--text-muted);">
                                    {{ $request->start_date->format('d F Y') }} - {{ $request->end_date->format('d F Y') }} ({{ $request->total_days }} days)
                                </div>
                                <div class="fw-semibold mt-1" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--primary-blue);">
                                    Rp{{ number_format($request->total_price, 0, ',', '.') }}
                                </div>
                                <div style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
                                    {{ $request->owner->name ?? 'Owner' }}
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="#" class="btn btn-outline-primary btn-sm rounded-3 mb-1 d-block"
                                   style="font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--primary-blue-light); color: var(--primary-blue-light);"
                                   onclick="openTransactionDetail({{ $request->id }}); return false;">
                                    {{ __('ui.transaction_detail') }}
                                </a>
                                @if($request->status === 'pending')
                                <button class="btn btn-outline-danger btn-sm rounded-3 d-block"
                                        style="font-family:'Mona Sans',sans-serif; font-size:14px;"
                                        onclick="cancelRental({{ $request->id }}, this)">
                                    {{ __('ui.cancel') }}
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-4 p-5 text-center shadow-sm border">
                        <p class="text-muted mb-0" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.no_ongoing') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===== HISTORY ===== --}}
    <div x-show="activityTab === 'history'">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" placeholder="{{ __('ui.search_transaction') }}"
                           style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                    <option>{{ __('ui.category') }}</option>
                    <option>{{ __('ui.completed') }}</option>
                    <option>{{ __('ui.cancelled') }}</option>
                </select>
            </div>
        </div>

        @php $currentMonth = ''; @endphp
        @forelse($historyRequests as $request)
            @php
                $monthKey = $request->start_date->format('F');
            @endphp
            @if($monthKey !== $currentMonth)
                <h5 class="fw-semibold mb-3 mt-4" style="font-family:'Mona Sans',sans-serif; font-size:20px;">{{ $monthKey }}</h5>
                @php $currentMonth = $monthKey @endphp
            @endif

            <div class="bg-white rounded-4 p-4 shadow-sm border mb-3" style="border-color: var(--border-default);">
                <div class="d-flex justify-content-between align-items-start flex-wrap">
                    <div>
                        <x-shared.status-badge :status="$request->status" />
                        <h5 class="fw-semibold mt-2 mb-1" style="font-family:'Mona Sans',sans-serif; font-size:18px;">
                            {{ $request->product->title ?? 'Product' }}
                        </h5>
                        <div style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
                            {{ $request->owner->name ?? 'Owner' }}
                        </div>
                        <div style="font-family:'Mona Sans',sans-serif; font-size:11px; color: var(--text-muted);">
                            {{ $request->start_date->format('d F Y') }} - {{ $request->end_date->format('d F Y') }} ({{ $request->total_days }} days)
                        </div>
                        <div class="mt-1" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-muted);">
                            {{ __('ui.total') }}: <span class="fw-semibold text-dark">Rp{{ number_format($request->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--text-primary);">
                            {{ 'IVR/' . $request->created_at->format('Ymd') . '/XXVI/I/' . $request->id }}
                        </div>
                    </div>
                    <div class="text-end">
                        <a href="#" class="btn btn-outline-primary btn-sm rounded-3 mb-1 d-block"
                           style="font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--primary-blue-light); color: var(--primary-blue-light);"
                           onclick="openTransactionDetail({{ $request->id }}); return false;">{{ __('ui.transaction_detail') }}</a>
                        @if($request->status === 'completed')
                            @if($request->relationLoaded('ratings') && $request->ratings->isNotEmpty())
                                <span class="badge bg-success bg-opacity-10 text-success rounded-3 d-block py-1 px-2"
                                      style="font-family:'Mona Sans',sans-serif; font-size:12px;">
                                    ✓ {{ __('ui.already_rated') }}
                                </span>
                            @else
                                <button class="btn btn-outline-warning btn-sm rounded-3 d-block"
                                        style="font-family:'Mona Sans',sans-serif; font-size:14px;"
                                        onclick="openRatingModal({{ $request->id }}, '{{ $request->product->title ?? 'Product' }}')">
                                    ⭐ {{ __('ui.rate_owner') }}
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-4 p-5 text-center shadow-sm border">
                <p class="text-muted mb-0" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.no_history') }}</p>
            </div>
        @endforelse

        @if($historyRequests->count() > 0)
            <div class="text-center mt-3">
                <a href="#" class="fw-semibold" style="font-family:'Mona Sans',sans-serif; font-size:14px; color: var(--primary-blue-light);">
                    {{ __('ui.see_all') }} <i class="bi bi-chevron-right"></i>
                </a>
            </div>
        @endif
    </div>

</div>

{{-- Rating Modal —” Alpine.js --}}
<div x-data="ratingModal()" x-cloak x-show="show"
     x-on:keydown.escape.window="show = false"
     class="position-fixed top-0 start-0 w-100 h-100"
     style="background:rgba(0,0,0,0.5); z-index:9999;"
     x-on:click.self="show = false">
    <div class="bg-white rounded-4 shadow p-4 position-absolute top-50 start-50 translate-middle"
         style="width:420px; max-width:95vw;" @click.stop="">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="font-family:'Mona Sans',sans-serif;">{{ __('ui.rate_owner') }}</h5>
            <button type="button"
                    class="border-0 bg-transparent fs-3 text-muted"
                    style="width:36px; height:36px; cursor:pointer; line-height:1;"
                    @click="show = false"
                    aria-label="Close">&times;</button>
        </div>
        <p class="text-muted mb-3" style="font-family:'Mona Sans',sans-serif; font-size:14px;" x-text="'Product: ' + productTitle"></p>

        {{-- Star Rating --}}
        <div class="d-flex gap-1 mb-3 justify-content-center">
            <template x-for="s in 5" :key="s">
                <i class="bi fs-3" style="cursor:pointer;"
                   :class="s <= selectedScore ? 'bi-star-fill text-warning' : 'bi-star text-muted'"
                   @click="selectedScore = s"></i>
            </template>
        </div>

        {{-- Review Text --}}
        <textarea x-model="review" rows="3" class="form-control mb-3" placeholder="{{ __('ui.write_review_optional') }}"
                  style="font-family:'Mona Sans',sans-serif; font-size:14px;"></textarea>

        {{-- Submit --}}
        <button class="btn w-100 text-white fw-medium rounded-3"
                :disabled="submitting || selectedScore === 0"
                style="background:#0031e1; font-family:'Mona Sans',sans-serif;"
                @click="submitRating">
            <span x-show="!submitting">{{ __('ui.submit_rating') }}</span>
            <span x-show="submitting">
                <span class="spinner-border spinner-border-sm me-1"></span>{{ __('ui.sending') }}
            </span>
        </button>
    </div>
</div>

{{-- Transaction Detail Modal — Bootstrap 5 --}}
<div class="modal fade" id="transactionDetailModal" tabindex="-1"
     aria-labelledby="transactionDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" id="transactionDetailModalLabel"
                    style="font-family:'Mona Sans',sans-serif;">
                    {{ __('ui.transaction_detail') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-2" id="transactionDetailBody">
                {{-- Loading --}}
                <div id="td-loading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="text-muted mt-2 mb-0" style="font-family:'Mona Sans',sans-serif;">
                        {{ __('ui.loading') }}
                    </p>
                </div>
                {{-- Content (hidden until data loads) --}}
                <div id="td-content" style="display: none;">
                    {{-- Status badge + Invoice --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div id="td-status-badge"></div>
                        <small class="text-muted" id="td-order-ref"
                               style="font-family:'Mona Sans',sans-serif; font-size:12px;"></small>
                    </div>
                    {{-- Product Section --}}
                    <div class="d-flex gap-3 mb-4 p-3 rounded-3" style="background: #f8f9fa;">
                        <img id="td-product-image" src="" class="rounded-3 border" alt=""
                             style="width:100px; height:80px; object-fit:cover;">
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1" id="td-product-title" style="font-family:'Mona Sans',sans-serif;"></h6>
                            <div class="text-muted small" id="td-product-meta" style="font-family:'Mona Sans',sans-serif;"></div>
                            <div class="fw-semibold mt-1" id="td-product-price"
                                 style="font-family:'Mona Sans',sans-serif; color:var(--primary-blue);"></div>
                            <div class="text-muted small" id="td-product-deposit"
                                 style="font-family:'Mona Sans',sans-serif;"></div>
                        </div>
                    </div>
                    {{-- Rental Period --}}
                    <h6 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        {{ __('ui.rental_period') }}
                    </h6>
                    <div class="row g-2 mb-3 small">
                        <div class="col-md-6">
                            <span class="text-muted">{{ __('ui.start_date') }}:</span>
                            <strong id="td-start-date"></strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted">{{ __('ui.end_date') }}:</span>
                            <strong id="td-end-date"></strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted">{{ __('ui.duration') }}:</span>
                            <strong id="td-duration"></strong>
                        </div>
                        <div class="col-md-6">
                            <span class="text-muted">{{ __('ui.quantity') }}:</span>
                            <strong id="td-quantity"></strong>
                        </div>
                    </div>
                    {{-- Price Breakdown --}}
                    <h6 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        {{ __('ui.payment_summary') }}
                    </h6>
                    <div class="mb-3 p-3 rounded-3" style="background:#f8f9fa;">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted" id="td-price-per-day-label"></span>
                            <span id="td-price-per-day"></span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">{{ __('ui.service_fee') }}:</span>
                            <span id="td-service-fee"></span>
                        </div>
                        <hr class="my-1">
                        <div class="d-flex justify-content-between fw-semibold">
                            <span>{{ __('ui.total') }}:</span>
                            <span id="td-total-price" style="color:var(--primary-blue);"></span>
                        </div>
                    </div>
                    {{-- Payment Info (conditional) --}}
                    <div id="td-payment-section" style="display:none;">
                        <h6 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                            {{ __('ui.payment_info') }}
                        </h6>
                        <div class="row g-2 mb-3 small">
                            <div class="col-md-6">
                                <span class="text-muted">{{ __('ui.payment_status') }}:</span>
                                <strong id="td-payment-status"></strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">{{ __('ui.payment_method') }}:</span>
                                <strong id="td-payment-method"></strong>
                            </div>
                            <div class="col-12">
                                <span class="text-muted">{{ __('ui.transaction_id') }}:</span>
                                <strong id="td-transaction-id" class="text-break"></strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted">{{ __('ui.paid_at') }}:</span>
                                <strong id="td-paid-at"></strong>
                            </div>
                        </div>
                    </div>
                    {{-- Rejection Reason (conditional) --}}
                    <div id="td-rejection-section" style="display:none;">
                        <h6 class="fw-bold mb-2 text-danger" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                            {{ __('ui.rejection_reason_title') }}
                        </h6>
                        <p class="small p-3 rounded-3" id="td-rejection-reason"
                           style="background:#f8d7da; font-family:'Mona Sans',sans-serif;"></p>
                    </div>
                    {{-- Notes (conditional) --}}
                    <div id="td-notes-section" style="display:none;">
                        <h6 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                            {{ __('ui.notes') }}
                        </h6>
                        <p class="small p-3 rounded-3" id="td-notes"
                           style="background:#f8f9fa; font-family:'Mona Sans',sans-serif;"></p>
                    </div>
                    {{-- Timeline --}}
                    <h6 class="fw-bold mb-2" style="font-family:'Mona Sans',sans-serif; font-size:14px;">
                        {{ __('ui.timeline') }}
                    </h6>
                    <div class="small mb-3" id="td-timeline" style="font-family:'Mona Sans',sans-serif;"></div>
                    {{-- Owner --}}
                    <hr>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img id="td-owner-avatar" src="" class="rounded-circle border" alt=""
                             style="width:36px; height:36px; object-fit:cover;">
                        <div>
                            <div class="fw-semibold small" id="td-owner-name"
                                 style="font-family:'Mona Sans',sans-serif;"></div>
                            <div class="text-muted small" id="td-owner-rating"
                                 style="font-family:'Mona Sans',sans-serif;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 px-4 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                    {{ __('ui.close') }}
                </button>
            </div>
        </div>
    </div>
</div>

@pushOnce('scripts')
<script>
    // Cancel rental
    async function cancelRental(id, btn) {
        const result = await Swal.fire({
            icon: 'question',
            title: '{{ __('ui.cancel_confirm') }}',
            showCancelButton: true,
            confirmButtonText: '{{ __('ui.yes') }}',
            cancelButtonText: '{{ __('ui.no') }}',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
        });
        if (!result.isConfirmed) return;

        btn.disabled = true;
        btn.textContent = '...';

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        try {
            const res = await fetch('/peminjaman/' + id + '/batal', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
            });
            const data = await res.json();
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message, confirmButtonColor: '#0031e1' }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Oops...', text: data.message || '{{ __('ui.cancel_failed') }}', confirmButtonColor: '#0031e1' });
                btn.disabled = false;
                btn.textContent = '{{ __('ui.cancel') }}';
            }
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Network Error', text: '{{ __('ui.network_error') }}', confirmButtonColor: '#0031e1' });
            btn.disabled = false;
            btn.textContent = '{{ __('ui.cancel') }}';
        }
    }

    // Rating modal Alpine component
    function ratingModal() {
        return {
            show: false,
            rentalId: null,
            productTitle: '',
            selectedScore: 0,
            review: '',
            submitting: false,

            open(id, title) {
                this.rentalId = id;
                this.productTitle = title;
                this.selectedScore = 0;
                this.review = '';
                this.show = true;
            },

            async submitRating() {
                if (this.selectedScore === 0 || this.submitting) return;

                this.submitting = true;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

                try {
                    const res = await fetch('/peminjaman/' + this.rentalId + '/rating', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            score: this.selectedScore,
                            review: this.review,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.show = false;
                        Swal.fire({ icon: 'success', title: '{{ __('ui.success') }}', text: data.message, confirmButtonColor: '#0031e1' }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: '{{ __('ui.oops') }}', text: data.message || '{{ __('ui.rating_failed') }}', confirmButtonColor: '#0031e1' });
                    }
                } catch (e) {
                    Swal.fire({ icon: 'error', title: '{{ __('ui.network_error_title') }}', text: '{{ __('ui.network_error') }}', confirmButtonColor: '#0031e1' });
                } finally {
                    this.submitting = false;
                }
            },
        };
    }

    // Bridge function to open rating modal
    function openRatingModal(id, title) {
        const modal = document.querySelector('[x-data="ratingModal()"]');
        if (modal && modal.__x) {
            modal.__x.$data.open(id, title);
        }
    }

    // ── Transaction Detail Modal ──
    async function openTransactionDetail(rentalId) {
        const modalEl = document.getElementById('transactionDetailModal');
        const loading = document.getElementById('td-loading');
        const content = document.getElementById('td-content');

        loading.style.display = 'block';
        content.style.display = 'none';

        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        try {
            const res = await fetch('/peminjaman/' + rentalId, {
                headers: { 'Accept': 'application/json' },
            });
            const json = await res.json();

            if (!json.success) {
                Swal.fire({
                    icon: 'error',
                    title: @js(__('ui.oops')),
                    text: json.message || @js(__('ui.failed_load_detail')),
                    confirmButtonColor: '#0031e1',
                });
                modal.hide();
                return;
            }

            populateTransactionDetail(json.data);
            loading.style.display = 'none';
            content.style.display = 'block';
        } catch (e) {
            Swal.fire({
                icon: 'error',
                title: @js(__('ui.network_error_title')),
                text: @js(__('ui.network_error')),
                confirmButtonColor: '#0031e1',
            });
            modal.hide();
        }
    }

    function populateTransactionDetail(d) {
        const rp = (val) => 'Rp' + Number(val || 0).toLocaleString('id-ID');
        const fmtDate = (val) => {
            if (!val) return '-';
            const dt = new Date(val);
            return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        };
        const fmtDateTime = (val) => {
            if (!val) return '-';
            const dt = new Date(val);
            return dt.toLocaleDateString('id-ID', {
                day: 'numeric', month: 'short', year: 'numeric',
                hour: '2-digit', minute: '2-digit',
            });
        };

        // Status badge
        const statusMap = {
            pending:   { label: @js(__('ui.status_pending')),   bg: '#fff3cd', color: '#856404' },
            approved:  { label: @js(__('ui.status_approved')),  bg: '#d4edda', color: '#155724' },
            rejected:  { label: @js(__('ui.status_rejected')),  bg: '#f8d7da', color: '#721c24' },
            ongoing:   { label: @js(__('ui.status_ongoing')),   bg: '#cce5ff', color: '#004085' },
            completed: { label: @js(__('ui.status_completed')), bg: '#d4edda', color: '#155724' },
            cancelled: { label: @js(__('ui.status_cancelled')), bg: '#e2e3e5', color: '#383d41' },
        };
        const s = statusMap[d.status] || statusMap.pending;
        document.getElementById('td-status-badge').innerHTML =
            '<span class="d-inline-block px-2 py-0 rounded-pill fw-medium" ' +
            'style="background:' + s.bg + '; color:' + s.color + '; font-size:12px;">' +
            s.label + '</span>';

        document.getElementById('td-order-ref').textContent = d.order_ref || '';

        // Product
        const p = d.product || {};
        const img = document.getElementById('td-product-image');
        img.src = p.primary_image || '/images/placeholder-product.png';
        img.alt = p.title || 'Product';
        document.getElementById('td-product-title').textContent = p.title || 'Product';
        document.getElementById('td-product-meta').textContent =
            (p.category_name || '') + (p.condition ? ' · ' + p.condition : '');
        document.getElementById('td-product-price').textContent =
            rp(p.price_per_day) + ' ' + @js(__('ui.per_day'));
        document.getElementById('td-product-deposit').textContent =
            @js(__('ui.deposit')) + ': ' + rp(p.deposit_amount);

        // Rental period
        document.getElementById('td-start-date').textContent = fmtDate(d.start_date);
        document.getElementById('td-end-date').textContent = fmtDate(d.end_date);
        document.getElementById('td-duration').textContent = d.total_days + ' ' + @js(__('ui.days'));
        document.getElementById('td-quantity').textContent = d.quantity + ' ' + @js(__('ui.unit'));

        // Price breakdown
        const ppd = Number(p.price_per_day || 0);
        const subtotal = ppd * d.total_days * d.quantity;
        const serviceFee = d.total_price - subtotal;
        document.getElementById('td-price-per-day-label').textContent =
            rp(ppd) + ' x ' + d.total_days + ' ' + @js(__('ui.days')) +
            (d.quantity > 1 ? ' x ' + d.quantity + ' ' + @js(__('ui.unit')) : '');
        document.getElementById('td-price-per-day').textContent = rp(subtotal);
        document.getElementById('td-service-fee').textContent = serviceFee > 0 ? rp(serviceFee) : rp(0);
        document.getElementById('td-total-price').textContent = rp(d.total_price);

        // Payment section
        const paySection = document.getElementById('td-payment-section');
        if (d.payment_status) {
            paySection.style.display = 'block';
            const psLabel = d.payment_status === 'paid' ? @js(__('ui.status_completed'))
                : d.payment_status === 'failed' ? @js(__('ui.rental_failed'))
                : d.payment_status === 'expired' ? @js(__('ui.payment_expired'))
                : d.payment_status.charAt(0).toUpperCase() + d.payment_status.slice(1);
            const psColor = d.payment_status === 'paid' ? 'success'
                : d.payment_status === 'failed' ? 'danger'
                : d.payment_status === 'expired' ? 'secondary' : 'warning';
            document.getElementById('td-payment-status').innerHTML =
                '<span class="badge bg-' + psColor + ' bg-opacity-10 text-' + psColor + ' rounded-3 px-2 py-1">' +
                psLabel + '</span>';
            document.getElementById('td-payment-method').textContent =
                d.payment_method ? d.payment_method.replace(/_/g, ' ') : '-';
            document.getElementById('td-transaction-id').textContent = d.transaction_id || '-';
            document.getElementById('td-paid-at').textContent = d.paid_at ? fmtDateTime(d.paid_at) : '—';
        } else {
            paySection.style.display = 'none';
        }

        // Rejection reason
        const rejSection = document.getElementById('td-rejection-section');
        if (d.status === 'rejected' && d.rejection_reason) {
            rejSection.style.display = 'block';
            document.getElementById('td-rejection-reason').textContent = d.rejection_reason;
        } else {
            rejSection.style.display = 'none';
        }

        // Notes
        const notesSection = document.getElementById('td-notes-section');
        if (d.notes) {
            notesSection.style.display = 'block';
            document.getElementById('td-notes').textContent = d.notes;
        } else {
            notesSection.style.display = 'none';
        }

        // Timeline
        const timelineItems = [
            { label: @js(__('ui.created_at')),     value: d.created_at },
            { label: @js(__('ui.approved_at')),    value: d.approved_at },
            { label: @js(__('ui.paid_at')),        value: d.paid_at },
            { label: @js(__('ui.completed_at')),   value: d.completed_at },
        ];
        document.getElementById('td-timeline').innerHTML = timelineItems
            .filter(item => item.value)
            .map(item =>
                '<div class="d-flex justify-content-between mb-1">' +
                '<span class="text-muted">' + item.label + ':</span> ' +
                '<strong>' + fmtDateTime(item.value) + '</strong></div>'
            ).join('');

        // Owner
        const o = d.owner || {};
        document.getElementById('td-owner-avatar').src =
            o.avatar || '/images/placeholder-avatar.png';
        document.getElementById('td-owner-name').textContent = o.name || @js(__('ui.unknown'));
        document.getElementById('td-owner-rating').textContent =
            o.rating_avg_as_owner ? @js(__('ui.avg_rating')) + ': ' + Number(o.rating_avg_as_owner).toFixed(1) + ' / 5.0' : '';
    }
</script>
@endPushOnce
