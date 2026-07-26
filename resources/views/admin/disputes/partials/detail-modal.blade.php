@php
    $rentalRequest = $dispute->rentalRequest;

    $isBorrowerReporter =
        (int) $dispute->reporter_id ===
        (int) $rentalRequest?->borrower_id;

    /*
    |--------------------------------------------------------------------------
    | Translation keys
    |--------------------------------------------------------------------------
    */
    $reporterRoleKey = $isBorrowerReporter
        ? 'borrower'
        : 'store';

    $reportedParty = $isBorrowerReporter
        ? $rentalRequest?->owner
        : $rentalRequest?->borrower;

    $productName =
        $rentalRequest?->product?->name
        ?? $rentalRequest?->product?->title
        ?? '-';

    $statusKey = strtolower($dispute->status);

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

            {{-- MODAL HEADER --}}
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
                        {{ __('ui.dispute_details_title') }}
                    </h5>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="{{ __('ui.close') }}"
                ></button>
            </div>

            {{-- MODAL BODY --}}
            <div class="modal-body dispute-modal__body">

                <div class="dispute-detail-grid">

                    {{-- Reporter --}}
                    <div class="dispute-detail-item">
                        <span>
                            {{ __('ui.dispute_column_reporter') }}
                        </span>

                        <strong>
                            {{ $dispute->reporter?->name ?? '-' }}
                        </strong>
                    </div>

                    {{-- Reporter Role --}}
                    <div class="dispute-detail-item">
                        <span>
                            {{ __('ui.dispute_reporter_role') }}
                        </span>

                        <strong>
                            {{ __("ui.dispute_role_{$reporterRoleKey}") }}
                        </strong>
                    </div>

                    {{-- Reported Party --}}
                    <div class="dispute-detail-item">
                        <span>
                            {{ __('ui.dispute_column_reported_party') }}
                        </span>

                        <strong>
                            {{ $reportedParty?->name ?? '-' }}
                        </strong>
                    </div>

                    {{-- Product --}}
                    <div class="dispute-detail-item">
                        <span>
                            {{ __('ui.dispute_column_product') }}
                        </span>

                        <strong>
                            {{ $productName }}
                        </strong>
                    </div>

                    {{-- Submitted --}}
                    <div class="dispute-detail-item">
                        <span>
                            {{ __('ui.dispute_column_submitted') }}
                        </span>

                        <strong>
                            {{ $dispute->created_at
                                ?->locale(app()->getLocale())
                                ->translatedFormat('d F Y, H:i') ?? '-' }}
                        </strong>
                    </div>

                    {{-- Status --}}
                    <div class="dispute-detail-item">
                        <span>
                            {{ __('ui.dispute_column_status') }}
                        </span>

                        <strong>
                            {{ __("ui.dispute_status_{$statusKey}") }}
                        </strong>
                    </div>

                </div>

                {{-- Reason --}}
                <div class="dispute-detail-block">
                    <span>
                        {{ __('ui.dispute_column_reason') }}
                    </span>

                    <p>
                        {{ $dispute->reason }}
                    </p>
                </div>

                {{-- Evidence --}}
                <div class="dispute-detail-block">
                    <span>
                        {{ __('ui.dispute_evidence') }}
                    </span>

                    @if ($dispute->evidence)
                        <a
                            href="{{ asset('storage/' . $dispute->evidence) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="dispute-evidence"
                        >
                            <img
                                src="{{ asset('storage/' . $dispute->evidence) }}"
                                alt="{{ __('ui.dispute_evidence_alt') }}"
                            >
                        </a>
                    @else
                        <p>
                            {{ __('ui.dispute_no_evidence') }}
                        </p>
                    @endif
                </div>

                {{-- FINISHED DISPUTE --}}
                @if ($isFinished)
                    <div class="dispute-detail-block">
                        <span>
                            {{ __('ui.dispute_admin_resolution') }}
                        </span>

                        <p>
                            {{ $dispute->resolution
                                ?: __('ui.dispute_no_resolution') }}
                        </p>
                    </div>

                {{-- OPEN / IN REVIEW DISPUTE --}}
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
                            {{ __('ui.dispute_admin_decision') }}
                        </label>

                        <textarea
                            name="resolution"
                            id="resolution{{ $dispute->id }}"
                            class="form-control"
                            rows="4"
                            maxlength="3000"
                            required
                            placeholder="{{ __('ui.dispute_decision_placeholder') }}"
                        >{{ old('resolution') }}</textarea>

                        <div class="dispute-modal__actions">
                            <button
                                type="button"
                                class="btn btn-light"
                                data-bs-dismiss="modal"
                            >
                                {{ __('ui.cancel') }}
                            </button>

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                                formaction="{{ route(
                                    'admin.disputes.reject',
                                    $dispute
                                ) }}"
                            >
                                {{ __('ui.dispute_reject_claim') }}
                            </button>

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                {{ __('ui.dispute_approve_claim') }}
                            </button>
                        </div>
                    </form>
                @endif

            </div>
        </div>
    </div>
</div>