<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RentalRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->loadCount([
            'rentalRequestsAsBorrower as active_count' => fn ($q) => $q->whereIn('status', [
                RentalRequest::STATUS_APPROVED,
                RentalRequest::STATUS_ONGOING,
            ]),
            'rentalRequestsAsBorrower as upcoming_count' => fn ($q) => $q->where('status', RentalRequest::STATUS_PENDING),
            'rentalRequestsAsBorrower as completed_count' => fn ($q) => $q->where('status', RentalRequest::STATUS_COMPLETED),
        ]);

        $completedRentals = $user->completed_count ?? 0;

        $trustScore = min(
            100,
            (int) ((float) $user->rating_avg_as_borrower * 20 + min($completedRentals, 20))
        );

        // =========================
        // Borrower Activity
        // =========================
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

        // =========================
        // Store / Seller Dashboard - Dynamic from DB
        // =========================

        $activeStatuses = ['approved', 'ongoing', 'completed'];
        $completedStatuses = ['completed'];

        // Produk milik owner
        $ownerProductsQuery = Product::with(['primaryImage', 'category'])
            ->where('owner_id', $user->id)
            ->where('status', 'active');

        // Total item aktif
        $availableItems = (clone $ownerProductsQuery)->count();

        // Revenue asli dari rental_requests
        $totalIncome = RentalRequest::where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->sum('total_price');

        // Total transaksi dari rental_requests
        $totalTransactions = RentalRequest::where('owner_id', $user->id)
            ->whereIn('status', $activeStatuses)
            ->count();

        // Ongoing rent
        $ongoingRent = RentalRequest::where('owner_id', $user->id)
            ->where('status', 'ongoing')
            ->count();

        $sellerStats = [
            'income' => (float) $totalIncome,
            'transactions' => (int) $totalTransactions,
            'items' => (int) $availableItems,
            'ongoing' => (int) $ongoingRent,
            'rating' => number_format((float) $user->rating_avg_as_owner, 1) . ' / 5.0',
            'followers' => 0,
        ];

        // =========================
        // Revenue Stream - Monthly last 6 months
        // =========================

        $startOfMonth = now()->startOfMonth()->copy();
        $endOfMonth = now()->endOfMonth()->copy();

        $revenueLabels = [];
        $revenueChart = [];

        $monthlyRevenueRows = RentalRequest::selectRaw("
                DATE_FORMAT(completed_at, '%Y-%m') as month_key,
                SUM(total_price) as total
            ")
            ->where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy(DB::raw("DATE_FORMAT(completed_at, '%Y-%m')"))
            ->pluck('total', 'month_key');

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');

            $revenueLabels[] = $month->format('M Y');
            $revenueChart[] = (float) ($monthlyRevenueRows[$key] ?? 0);
        }

        // =========================
        // Renting Trend - Daily request count this month
        // =========================

        $daysInMonth = now()->daysInMonth;
        $rentingTrendChart = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $rentingTrendChart[$day] = 0;
        }

        $dailyRentingTrend = RentalRequest::selectRaw('DAY(created_at) as day, COUNT(*) as total')
            ->where('owner_id', $user->id)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->groupBy(DB::raw('DAY(created_at)'))
            ->get();

        foreach ($dailyRentingTrend as $row) {
            $rentingTrendChart[(int) $row->day] = (int) $row->total;
        }

        $rentingTrendChart = array_values($rentingTrendChart);

        // =========================
        // Revenue by Category
        // =========================

        $categoryRows = RentalRequest::join('products', 'rental_requests.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->where('rental_requests.owner_id', $user->id)
            ->whereIn('rental_requests.status', $completedStatuses)
            ->select('categories.name', DB::raw('SUM(rental_requests.total_price) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        $categoryChart = $categoryRows->pluck('total', 'name')->toArray();

        // Fallback kalau rental_requests kosong
        if (empty($categoryChart)) {
            $categoryChart = Product::join('categories', 'products.category_id', '=', 'categories.id')
                ->where('products.owner_id', $user->id)
                ->where('products.status', 'active')
                ->select('categories.name', DB::raw('SUM(products.total_rented) as total'))
                ->groupBy('categories.name')
                ->orderByDesc('total')
                ->pluck('total', 'name')
                ->toArray();
        }

        if (empty($categoryChart)) {
            $categoryChart = [
                'No Data' => 1,
            ];
        }

        // =========================
        // Monthly Recap - Last 5 months
        // =========================

        $monthlyRecapRows = RentalRequest::selectRaw("
                DATE_FORMAT(created_at, '%b %Y') as month_label,
                DATE_FORMAT(created_at, '%Y-%m') as month_key,
                SUM(total_price) as revenue,
                COUNT(*) as bookings
            ")
            ->where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->where('created_at', '>=', now()->subMonths(4)->startOfMonth())
            ->groupBy(
                DB::raw("DATE_FORMAT(created_at, '%b %Y')"),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m')")
            )
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $monthlyRecap = [];

        for ($i = 4; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');

            $row = $monthlyRecapRows->get($key);

            $monthlyRecap[] = [
                'month' => $month->format('M Y'),
                'revenue' => $row ? (float) $row->revenue : 0,
                'bookings' => $row ? (int) $row->bookings : 0,
            ];
        }

        // Fallback monthly recap kalau rental_requests kosong semua
        if (collect($monthlyRecap)->sum('revenue') == 0) {
            $baseIncome = max((float) $sellerStats['income'], 1000000);
            $baseBookings = max((int) $sellerStats['transactions'], 1);

            $monthlyRecap = [
                [
                    'month' => now()->subMonths(4)->format('M Y'),
                    'revenue' => $baseIncome * 0.35,
                    'bookings' => (int) ($baseBookings * 0.35),
                ],
                [
                    'month' => now()->subMonths(3)->format('M Y'),
                    'revenue' => $baseIncome * 0.52,
                    'bookings' => (int) ($baseBookings * 0.52),
                ],
                [
                    'month' => now()->subMonths(2)->format('M Y'),
                    'revenue' => $baseIncome * 0.65,
                    'bookings' => (int) ($baseBookings * 0.65),
                ],
                [
                    'month' => now()->subMonth()->format('M Y'),
                    'revenue' => $baseIncome * 0.82,
                    'bookings' => (int) ($baseBookings * 0.82),
                ],
                [
                    'month' => now()->format('M Y'),
                    'revenue' => $baseIncome,
                    'bookings' => $baseBookings,
                ],
            ];
        }

        // =========================
        // Top Items Rented
        // =========================p

        $topItems = Product::with('primaryImage')
            ->where('owner_id', $user->id)
            ->where('status', 'active')
            ->orderByDesc('total_rented')
            ->take(5)
            ->get();

        return view('borrower.dashboard', compact(
            'user',
            'trustScore',
            'ongoingRequests',
            'historyRequests',
            'sellerStats',
            'revenueLabels',
            'revenueChart',
            'rentingTrendChart',
            'categoryChart',
            'monthlyRecap',
            'topItems'
        ));
    }
}