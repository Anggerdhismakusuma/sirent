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

    $isFinished = in_array(
        $dispute->status,
        [
            \App\Models\Dispute::STATUS_RESOLVED,
            \App\Models\Dispute::STATUS_REJECTED,
        ],
        true
    );
@endphp

<div
    class="modal fade"
    id="disputeModal{{ $dispute->id }}"
    tabindex="-1"
    aria-labelledby="disputeModalLabel{{ $dispute->id }}"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content dispute-modal">

            <div class="modal-header dispute-modal__header">
                <div>
                    <span class="dispute-modal__id">
                        DSP-{{ str_pad(
                            $dispute->id,
                            4,
                            '0',
                            STR_PAD_LEFT
                        ) }}
                    </span>

                    <h5
                        class="modal-title"
                        id="disputeModalLabel{{ $dispute->id }}"
                    >
                        Dispute Detail
                    </h5>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body dispute-modal__body">

                <div class="dispute-detail-grid">

                    <div class="dispute-detail-item">
                        <span>Reporter</span>
                        <strong>
                            {{ $dispute->reporter?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="dispute-detail-item">
                        <span>Reporter Role</span>
                        <strong>{{ $reporterRole }}</strong>
                    </div>

                    <div class="dispute-detail-item">
                        <span>Reported Party</span>
                        <strong>
                            {{ $reportedParty?->name ?? '-' }}
                        </strong>
                    </div>

                    <div class="dispute-detail-item">
                        <span>Product</span>
                        <strong>{{ $productName }}</strong>
                    </div>

                    <div class="dispute-detail-item">
                        <span>Submitted</span>
                        <strong>
                            {{ $dispute->created_at?->format('d M Y, H:i') ?? '-' }}
                        </strong>
                    </div>

                    <div class="dispute-detail-item">
                        <span>Status</span>
                        <strong>
                            {{ str($dispute->status)
                                ->replace('_', ' ')
                                ->title() }}
                        </strong>
                    </div>

                </div>

                <div class="dispute-detail-block">
                    <span>Reason</span>

                    <p>{{ $dispute->reason }}</p>
                </div>

                <div class="dispute-detail-block">
                    <span>Evidence</span>

                    @if ($dispute->evidence)
                        <a
                            href="{{ asset('storage/' . $dispute->evidence) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="dispute-evidence"
                        >
                            <img
                                src="{{ asset('storage/' . $dispute->evidence) }}"
                                alt="Dispute evidence"
                            >
                        </a>
                    @else
                        <p>No evidence attached.</p>
                    @endif
                </div>

                @if ($isFinished)
                    <div class="dispute-detail-block">
                        <span>Admin Resolution</span>

                        <p>
                            {{ $dispute->resolution
                                ?: 'No resolution provided.' }}
                        </p>
                    </div>
                @else
                    <form
                        method="POST"
                        action="{{ route(
                            'admin.disputes.approve',
                            $dispute
                        ) }}"
                        class="dispute-resolution-form"
                    >
                        @csrf
                        @method('PATCH')

                        <label
                            for="resolution{{ $dispute->id }}"
                            class="form-label"
                        >
                            Admin Decision
                        </label>

                        <textarea
                            name="resolution"
                            id="resolution{{ $dispute->id }}"
                            class="form-control"
                            rows="4"
                            maxlength="3000"
                            required
                            placeholder="Explain the admin decision..."
                        >{{ old('resolution') }}</textarea>

                        <div class="dispute-modal__actions">
                            <button
                                type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                                formaction="{{ route(
                                    'admin.disputes.reject',
                                    $dispute
                                ) }}"
                            >
                                Reject Claim
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Approve Claim
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>