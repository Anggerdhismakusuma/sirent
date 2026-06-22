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
                                   style="font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--primary-blue-light); color: var(--primary-blue-light);">
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
                           style="font-family:'Mona Sans',sans-serif; font-size:14px; border-color: var(--primary-blue-light); color: var(--primary-blue-light);">{{ __('ui.transaction_detail') }}</a>
                        @if($request->status === 'completed')
                            <button class="btn btn-outline-warning btn-sm rounded-3 d-block"
                                    style="font-family:'Mona Sans',sans-serif; font-size:14px;"
                                    onclick="openRatingModal({{ $request->id }}, '{{ $request->product->title ?? 'Product' }}')">
                                ⭐ {{ __('ui.rate_owner') }}
                            </button>
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
</script>
@endPushOnce
