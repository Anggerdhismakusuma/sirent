<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RentalRequest;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Redirect unverified users to onboarding
        // if (Auth::check()
        //     && Auth::user()->hasVerifiedEmail()
        //     && Auth::user()->verification_status === \App\Models\User::VERIFICATION_UNVERIFIED
        // ) {
        //     return redirect()->route('onboarding.step1');
        // }

        // Recomended: paginated, 12 per page
        $recomended = Product::with(['primaryImage', 'owner', 'category'])
            ->where('status', Product::STATUS_ACTIVE)
            ->latest()
            ->paginate(12);

        // Near You: random sample from all active products
        $nearYou = Product::with(['primaryImage', 'owner', 'category'])
            ->where('status', Product::STATUS_ACTIVE)
            ->inRandomOrder()
            ->take(12)
            ->get();

        // Available Now: newest 6 products
        $availableNow = Product::with(['primaryImage', 'owner', 'category'])
            ->where('status', Product::STATUS_ACTIVE)
            ->latest()
            ->take(6)
            ->get();

        // Featured product for CTA banner (first product or specific one)
        $featuredProduct = Product::with(['primaryImage'])
            ->where('status', Product::STATUS_ACTIVE)
            ->where('title', 'like', '%Sony ZV E10%')
            ->first()
            ?? Product::with(['primaryImage'])
                ->where('status', Product::STATUS_ACTIVE)
                ->latest()
                ->first();

        // Stats for logged-in user (F-PUB-02)
        $stats = null;
        if (Auth::check()) {
            $user = Auth::user();
            $stats = [
                'activeRentals' => RentalRequest::where('borrower_id', $user->id)
                    ->whereIn('status', [RentalRequest::STATUS_APPROVED, RentalRequest::STATUS_ONGOING])
                    ->count(),
                'upcomingRentals' => RentalRequest::where('borrower_id', $user->id)
                    ->where('status', RentalRequest::STATUS_PENDING)
                    ->count(),
                'favouriteItems' => 0, // Placeholder — no favourites model yet
                'thisMonthRented' => RentalRequest::where('borrower_id', $user->id)
                    ->where('status', RentalRequest::STATUS_COMPLETED)
                    ->whereMonth('completed_at', now()->month)
                    ->whereYear('completed_at', now()->year)
                    ->count(),
            ];
        }

        return view('home', compact(
            'recomended', 'nearYou', 'availableNow', 'featuredProduct', 'stats'
        ));
    }
}
