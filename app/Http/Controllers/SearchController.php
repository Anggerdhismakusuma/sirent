<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        // Build search + filter query via scopes
        $query = Product::with(['primaryImage', 'owner'])
            ->active()
            ->search($request->input('q'))
            ->inCity($request->input('location'))
            ->priceBetween(
                $request->has('min_price') ? $request->float('min_price') : null,
                $request->has('max_price') ? $request->float('max_price') : null,
            )
            ->minRating($request->has('rating') ? $request->float('rating') : null)
            ->verifiedOwner($request->boolean('verified'))
            ->sortBy($request->input('sort', 'latest'));

        $products = $query->paginate(18)->withQueryString();

        // Distinct city list for location filter dropdown
        $locations = Product::where('status', Product::STATUS_ACTIVE)
            ->whereNotNull('location_city')
            ->distinct()
            ->pluck('location_city')
            ->sort()
            ->values();

        return view('products.search', compact('products', 'locations'));
    }
}
