<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $user = $request->user();

        abort_unless(
            $user->is_owner_active,
            403,
            'You need to open your store first.'
        );

        $validated = $request->validateWithBag(
            'addProduct',
            [
                'title' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'category_id' => [
                    'required',
                    'exists:categories,id',
                ],

                'description' => [
                    'required',
                    'string',
                ],

                'condition' => [
                    'required',
                    'in:new,like_new,good,fair',
                ],

                'price_per_day' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'deposit_amount' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'location_city' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'location_detail' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'status' => [
                    'required',
                    'in:active,inactive,draft',
                ],

                'images' => [
                    'required',
                    'array',
                    'min:1',
                    'max:5',
                ],

                'images.*' => [
                    'required',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ],
            ]
        );

        $storedImagePaths = [];

        try {
            DB::transaction(function () use (
                $request,
                $user,
                $validated,
                &$storedImagePaths
            ): void {
                $slugBase = Str::slug($validated['title']);

                if ($slugBase === '') {
                    $slugBase = 'item';
                }

                $slug = $slugBase
                    . '-'
                    . Str::lower(Str::random(8));

                $product = Product::create([
                    'owner_id' => $user->id,
                    'category_id' => $validated['category_id'],
                    'title' => $validated['title'],
                    'slug' => $slug,
                    'description' => $validated['description'],
                    'condition' => $validated['condition'],
                    'price_per_day' => $validated['price_per_day'],
                    'deposit_amount' => $validated['deposit_amount'],
                    'location_city' => $validated['location_city'],
                    'location_detail' =>
                        $validated['location_detail'] ?? null,
                    'status' => $validated['status'],
                    'rating_avg' => 0,
                    'total_rented' => 0,
                ]);

                foreach (
                    $request->file('images', [])
                    as $index => $image
                ) {
                    $imagePath = $image->store(
                        'products',
                        'public'
                    );

                    $storedImagePaths[] = $imagePath;

                    $product->images()->create([
                        'image_path' => $imagePath,
                        'is_primary' => $index === 0,
                        'sort_order' => $index,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            /*
            * Kalau database gagal setelah gambar sempat tersimpan,
            * hapus gambar agar tidak menjadi file orphan.
            */
            foreach ($storedImagePaths as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        return redirect()
            ->route('borrower.dashboard', ['tab' => 'store'])
            ->with(
                'success',
                'Item berhasil ditambahkan.'
            );
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

    public function updateProduct(Request $request, Product $product)
    {
        abort_unless(
            (int) $product->owner_id === (int) $request->user()->id,
            403,
            'Unauthorized access.'
        );

        $validated = $request->validate([
            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'description' => [
                'required',
                'string',
            ],

            'condition' => [
                'required',
                'in:new,like_new,good,fair',
            ],

            'price_per_day' => [
                'required',
                'numeric',
                'min:0',
            ],

            'deposit_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'location_city' => [
                'required',
                'string',
                'max:100',
            ],

            'location_detail' => [
                'nullable',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:active,inactive,draft',
            ],

            'images' => [
                'nullable',
                'array',
                'max:5',
            ],

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
        ]);

        $product->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'condition' => $validated['condition'],
            'price_per_day' => $validated['price_per_day'],
            'deposit_amount' => $validated['deposit_amount'],
            'location_city' => $validated['location_city'],
            'location_detail' => $validated['location_detail'] ?? null,
            'status' => $validated['status'],
        ]);

        /*
        * Kalau seller upload gambar baru:
        * semua gambar lama diganti dengan gambar baru.
        *
        * Kalau tidak upload gambar:
        * gambar lama tetap dipakai.
        */
        if ($request->hasFile('images')) {
            foreach ($product->images as $existingImage) {
                Storage::disk('public')->delete(
                    $existingImage->image_path
                );
            }

            $product->images()->delete();

            foreach ($request->file('images') as $index => $image) {
                $imagePath = $image->store('products', 'public');

                $product->images()->create([
                    'image_path' => $imagePath,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);
            }
        }

        return redirect()
            ->route('borrower.dashboard', ['tab' => 'store'])
            ->with('success', 'Item berhasil diperbarui.');
    }
}