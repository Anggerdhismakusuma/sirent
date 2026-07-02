<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Models\RentalRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->loadCount([
            'rentalRequestsAsBorrower as active_count' => fn($q) => $q->whereIn('status', [RentalRequest::STATUS_APPROVED, RentalRequest::STATUS_ONGOING]),
            'rentalRequestsAsBorrower as upcoming_count' => fn($q) => $q->where('status', RentalRequest::STATUS_PENDING),
            'rentalRequestsAsBorrower as completed_count' => fn($q) => $q->where('status', RentalRequest::STATUS_COMPLETED),
        ]);

        $completedRentals = $user->completed_count ?? 0;
        $trustScore = min(100, (int)((float)$user->rating_avg_as_borrower * 20 + min($completedRentals, 20)));

        // Activity: all rental requests for this borrower
        $rentalRequests = RentalRequest::with(['product.primaryImage', 'owner'])
            ->where('borrower_id', $user->id)
            ->latest()
            ->get();

        $ongoingRequests = $rentalRequests->whereIn('status', [
            RentalRequest::STATUS_PENDING,
            RentalRequest::STATUS_APPROVED,
            RentalRequest::STATUS_ONGOING,
        ]);

        $historyRequests = $rentalRequests->whereIn('status', [
            RentalRequest::STATUS_COMPLETED,
            RentalRequest::STATUS_CANCELLED,
            RentalRequest::STATUS_REJECTED,
        ]);

        return view('borrower.dashboard', compact(
            'user',
            'trustScore',
            'ongoingRequests',
            'historyRequests'
        ));
    }
}
