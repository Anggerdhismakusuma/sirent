<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\RentalRequest;
use App\Notifications\DisputeStatusChanged;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BorrowerDisputeController extends Controller
{
    /**
     * File a dispute against the store owner for a rental request.
     */
    public function store(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $user = $request->user();

        // Verify the authenticated user is the borrower of this rental
        abort_unless(
            (int) $rentalRequest->borrower_id === (int) $user->id,
            403,
            'You are not authorized to dispute this rental request.'
        );

        $rentalRequest->load('product');

        // Only allow disputes for approved, ongoing, or completed rentals
        $allowedStatuses = [
            RentalRequest::STATUS_APPROVED,
            RentalRequest::STATUS_ONGOING,
            RentalRequest::STATUS_COMPLETED,
        ];

        if (! in_array($rentalRequest->status, $allowedStatuses, true)) {
            return response()->json([
                'success' => false,
                'message' => __('ui.dispute_not_eligible'),
            ], 422);
        }

        // Prevent duplicate active disputes
        $hasActiveDispute = Dispute::query()
            ->where('rental_request_id', $rentalRequest->id)
            ->whereIn('status', [Dispute::STATUS_OPEN, Dispute::STATUS_IN_REVIEW])
            ->exists();

        if ($hasActiveDispute) {
            return response()->json([
                'success' => false,
                'message' => __('ui.dispute_already_active'),
            ], 422);
        }

        $validated = $request->validate([
            'reason' => [
                'required',
                'string',
                'min:20',
                'max:1000',
            ],
            'evidence' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:4096',
            ],
        ]);

        $evidencePath = null;

        if ($request->hasFile('evidence')) {
            $evidencePath = $request
                ->file('evidence')
                ->store('dispute-evidence', 'public');
        }

        $dispute = Dispute::create([
            'rental_request_id' => $rentalRequest->id,
            'reporter_id'       => $user->id,
            'reason'            => $validated['reason'],
            'evidence'          => $evidencePath,
            'status'            => Dispute::STATUS_OPEN,
        ]);

        // Notify the reporter that the dispute has been submitted
        $user->notify(new DisputeStatusChanged(
            disputeId: $dispute->id,
            productName: $rentalRequest->product?->title ?? 'Unknown Product',
            status: 'submitted',
        ));

        return response()->json([
            'success' => true,
            'message' => __('ui.dispute_success'),
        ], 201);
    }
}
