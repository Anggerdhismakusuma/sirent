<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Rating;
use App\Models\RentalRequest;
use App\Models\User;

class StoreController extends Controller
{
    public function show($userId)
    {
        $owner = User::withCount([
            'products' => fn($q) => $q->where('status', Product::STATUS_ACTIVE),
            'ratingsReceived as ratings_count' => fn($q) => $q->where('type', Rating::TYPE_TO_OWNER),
        ])->findOrFail($userId);

        // Owner's active products
        $products = Product::with(['primaryImage', 'owner', 'category'])
            ->where('owner_id', $owner->id)
            ->where('status', Product::STATUS_ACTIVE)
            ->latest()
            ->paginate(18);

        // Owner's rating stats (as owner)
        $ownerRatings = Rating::where('ratee_id', $owner->id)
            ->where('type', Rating::TYPE_TO_OWNER)
            ->get();

        $totalRatings = $ownerRatings->count();
        $avgRating = $totalRatings > 0 ? round($ownerRatings->avg('score'), 1) : 0;

        // Rating distribution (1-5 stars count)
        $ratingDistribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $ratingDistribution[$i] = $totalRatings > 0 ? $ownerRatings->where('score', $i)->count() : 0;
        }

        // Reviews with rater info
        $reviews = Rating::with('rater')
            ->where('ratee_id', $owner->id)
            ->where('type', Rating::TYPE_TO_OWNER)
            ->whereNotNull('review')
            ->latest()
            ->paginate(5);

        // Trust score (simplified: based on avg rating, response rate, completed rentals)
        $completedRentals = RentalRequest::where('owner_id', $owner->id)
            ->where('status', RentalRequest::STATUS_COMPLETED)
            ->count();
        $trustScore = $totalRatings > 0 ? min(100, (int)($avgRating * 20 + min($completedRentals, 20))) : 0;

        return view('store.show', compact(
            'owner',
            'products',
            'ownerRatings',
            'totalRatings',
            'avgRating',
            'ratingDistribution',
            'reviews',
            'trustScore',
            'completedRentals'
        ));
    }

    public function openDashboardStore(Request $request){
        $user = $request->user();

        $user->forceFill([
            'is_owner_active' => true,
        ])->save();

        return redirect()
            ->route('borrower.dashboard', ['tab' => 'store'])
            ->with('success', 'Store berhasil dibuka.');
    } 

    public function storeProduct(Request $request)
    {
        $user = auth()->user();

        if (! $user->is_owner_active) {
            return redirect()
                ->route('borrower.dashboard', ['tab' => 'store'])
                ->with('error', 'You need to open your store first.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price_per_day' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Product::create([
            'owner_id' => $user->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . time(),
            'description' => $validated['description'] ?? null,
            'price_per_day' => $validated['price_per_day'],
            'status' => $validated['status'],
            'total_rented' => 0,
        ]);

        return redirect()
            ->route('borrower.dashboard', ['tab' => 'store'])
            ->with('success', 'Item berhasil ditambahkan.');
    }

    public function deleteProduct(Product $product)
    {
        if ($product->owner_id !== auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        // Safe delete: jangan langsung hapus supaya history rental tetap aman
        $product->update([
            'status' => 'inactive',
        ]);

        return redirect()
            ->route('borrower.dashboard', ['tab' => 'store'])
            ->with('success', 'Item berhasil dihapus dari store.');
    }
}