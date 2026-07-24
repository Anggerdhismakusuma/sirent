<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RentalRequest;
use App\Models\User;
use App\Models\Dispute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),

            'total_admins' => User::where('role', User::ROLE_ADMIN)->count(),

            'total_borrowers' => User::where('role', User::ROLE_BORROWER)->count(),

            'total_owners' => User::where('role', User::ROLE_OWNER)->count(),

            'active_sellers' => User::where('is_owner_active', true)->count(),

            'total_products' => Product::count(),

            'active_products' => Product::where('status', 'active')->count(),

            'total_rentals' => RentalRequest::count(),

            'pending_rentals' => RentalRequest::where('status', RentalRequest::STATUS_PENDING)->count(),

            'approved_rentals' => RentalRequest::where('status', RentalRequest::STATUS_APPROVED)->count(),

            'ongoing_rentals' => RentalRequest::where('status', RentalRequest::STATUS_ONGOING)->count(),

            'completed_rentals' => RentalRequest::where('status', RentalRequest::STATUS_COMPLETED)->count(),

            'cancelled_rentals' => RentalRequest::where('status', RentalRequest::STATUS_CANCELLED)->count(),

            'total_completed_revenue' => RentalRequest::where('status', RentalRequest::STATUS_COMPLETED)
                ->whereNotNull('completed_at')
                ->sum('total_price'),

            'pending_disputes' => Schema::hasTable('disputes')
                ? DB::table('disputes')->where('status', 'pending')->count()
                : 0,
        ];

        $latestUsers = User::query()
            ->latest('created_at')
            ->limit(5)
            ->get();

        $allUsers = User::query()
            ->latest('created_at')
            ->get();

        $latestProducts = Product::with('owner')
            ->latest()
            ->take(5)
            ->get();

        $rentalStatusSummary = RentalRequest::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $productStatusSummary = Product::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $productCategoryStats = Product::query()
            ->selectRaw('category_id, COUNT(*) as total')
            ->with('category:id,name')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $productCategoryLabels = $productCategoryStats
            ->map(
                fn ($item) => $item->category?->name ?? 'Tanpa Kategori'
            )
            ->values();

        $productCategoryCounts = $productCategoryStats
            ->pluck('total')
            ->map(fn ($total) => (int) $total)
            ->values();

        $totalActiveProducts = $productCategoryCounts->sum();

        $users = User::orderByDesc('created_at')->get();

        $oldestDisputes = Dispute::with([
            'reporter',
            'rentalRequest.borrower',
            'rentalRequest.owner',
            'rentalRequest.product',
        ])
        ->whereIn('status', [
            Dispute::STATUS_OPEN,
            Dispute::STATUS_IN_REVIEW,
        ])
        ->oldest('created_at')
        ->limit(5)
        ->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestUsers',
            'allUsers',
            'latestProducts',
            'rentalStatusSummary',
            'productStatusSummary',
            'productCategoryStats',
            'productCategoryLabels',
            'productCategoryCounts',
            'totalActiveProducts',
            'users',
            'oldestDisputes',
        ));
    }
}