<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Rating;
use App\Models\RentalRequest;

class ProductController extends Controller
{
    public function show($slug)
    {
        $product = Product::with([
            'primaryImage',
            'images',
            'owner',
            'category',
            'availabilities',
        ])->where('slug', $slug)
          ->where('status', Product::STATUS_ACTIVE)
          ->firstOrFail();

        // Blocked dates from availability table
        $blockedDates = $product->availabilities->pluck('blocked_date')->map(fn($d) => $d->format('Y-m-d'))->values()->toArray();

        // Reviews: completed rental requests that have ratings (to_owner)
        $reviews = $product->rentalRequests()
            ->where('status', RentalRequest::STATUS_COMPLETED)
            ->whereHas('ratings', fn($q) => $q->where('type', Rating::TYPE_TO_OWNER))
            ->with(['ratings' => fn($q) => $q->where('type', Rating::TYPE_TO_OWNER)->with('rater')])
            ->latest('completed_at')
            ->take(5)
            ->get()
            ->map(fn($rr) => $rr->ratings->first())
            ->filter();

        // Related/recommended products (same category, exclude current)
        $recommended = Product::with(['primaryImage', 'owner'])
            ->where('status', Product::STATUS_ACTIVE)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(6)
            ->get();

        // Owner stats
        $owner = $product->owner;
        $ownerTotalProducts = $owner->products()->where('status', Product::STATUS_ACTIVE)->count();
        $ownerCompletedRentals = RentalRequest::where('owner_id', $owner->id)
            ->where('status', RentalRequest::STATUS_COMPLETED)
            ->count();

        return view('products.show', compact(
            'product',
            'blockedDates',
            'reviews',
            'recommended',
            'ownerTotalProducts',
            'ownerCompletedRentals'
        ));
    }
}
