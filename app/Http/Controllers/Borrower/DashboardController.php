<?php

namespace App\Http\Controllers\Borrower;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\RentalRequest;
use App\Models\Category;
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
        // Seller Growth Cards
        // Compare last completed month vs previous month
        // =========================
        $comparisonMonth = now()->subMonth();
        $previousMonth = now()->subMonths(2);

        $comparisonStart = $comparisonMonth->copy()->startOfMonth();
        $comparisonEnd = $comparisonMonth->copy()->endOfMonth();

        $previousStart = $previousMonth->copy()->startOfMonth();
        $previousEnd = $previousMonth->copy()->endOfMonth();

        $previousLabel = $previousMonth->format('M Y');

        $makeGrowth = function ($current, $previous, $mode = 'percent') use ($previousLabel) {
            $current = (float) $current;
            $previous = (float) $previous;

            if ($previous == 0) {
                if ($current > 0) {
                    return [
                        'icon' => '▲',
                        'class' => 'text-success',
                        'label' => 'New vs ' . $previousLabel,
                    ];
                }

                return [
                    'icon' => '•',
                    'class' => 'text-muted',
                    'label' => 'No change vs ' . $previousLabel,
                ];
            }

            $diff = $current - $previous;

            if ($diff == 0) {
                return [
                    'icon' => '•',
                    'class' => 'text-muted',
                    'label' => 'No change vs ' . $previousLabel,
                ];
            }

            $isUp = $diff > 0;

            if ($mode === 'diff') {
                $value = number_format(abs($diff), 1);
            } else {
                $value = number_format(abs(($diff / $previous) * 100), 1) . '%';
            }

            return [
                'icon' => $isUp ? '▲' : '▼',
                'class' => $isUp ? 'text-success' : 'text-danger',
                'label' => $value . ' vs ' . $previousLabel,
            ];
        };

        $currentIncome = RentalRequest::where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$comparisonStart, $comparisonEnd])
            ->sum('total_price');

        $previousIncome = RentalRequest::where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$previousStart, $previousEnd])
            ->sum('total_price');

        $currentTransactions = RentalRequest::where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$comparisonStart, $comparisonEnd])
            ->count();

        $previousTransactions = RentalRequest::where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [$previousStart, $previousEnd])
            ->count();

        $currentItems = Product::where('owner_id', $user->id)
            ->where('status', 'active')
            ->where('created_at', '<=', $comparisonEnd)
            ->count();

        $previousItems = Product::where('owner_id', $user->id)
            ->where('status', 'active')
            ->where('created_at', '<=', $previousEnd)
            ->count();

        $currentOngoing = RentalRequest::where('owner_id', $user->id)
            ->whereNotIn('status', [
                RentalRequest::STATUS_CANCELLED,
                RentalRequest::STATUS_REJECTED,
            ])
            ->whereDate('start_date', '<=', $comparisonEnd)
            ->whereDate('end_date', '>=', $comparisonStart)
            ->count();

        $previousOngoing = RentalRequest::where('owner_id', $user->id)
            ->whereNotIn('status', [
                RentalRequest::STATUS_CANCELLED,
                RentalRequest::STATUS_REJECTED,
            ])
            ->whereDate('start_date', '<=', $previousEnd)
            ->whereDate('end_date', '>=', $previousStart)
            ->count();

        $currentRating = DB::table('ratings')
            ->where('ratee_id', $user->id)
            ->where('type', 'to_owner')
            ->whereBetween('created_at', [$comparisonStart, $comparisonEnd])
            ->avg('score') ?? 0;

        $previousRating = DB::table('ratings')
            ->where('ratee_id', $user->id)
            ->where('type', 'to_owner')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->avg('score') ?? 0;

        $sellerGrowth = [
            'income' => $makeGrowth($currentIncome, $previousIncome),
            'transactions' => $makeGrowth($currentTransactions, $previousTransactions),
            'items' => $makeGrowth($currentItems, $previousItems),
            'ongoing' => $makeGrowth($currentOngoing, $previousOngoing),
            'rating' => $makeGrowth($currentRating, $previousRating, 'diff'),
        ];


        // =========================
        // Revenue Stream + Renting Trend - Monthly / Weekly
        // =========================

        $revenuePeriod = request()->query('revenue_period', 'monthly');

        if (! in_array($revenuePeriod, ['monthly', 'weekly'])) {
            $revenuePeriod = 'monthly';
        }

        $revenueLabels = [];
        $revenueChart = [];
        $rentingTrendChart = [];

        if ($revenuePeriod === 'weekly') {
            // Weekly revenue untuk 6 minggu terakhir
            $startPeriod = now()->subWeeks(5)->startOfWeek();
            $endPeriod = now()->endOfWeek();

            $weeklyRows = RentalRequest::selectRaw("
                    YEARWEEK(completed_at, 1) as week_key,
                    MIN(DATE(completed_at)) as week_start,
                    MAX(DATE(completed_at)) as week_end,
                    SUM(total_price) as total_revenue,
                    COUNT(*) as total_booking
                ")
                ->where('owner_id', $user->id)
                ->whereIn('status', $completedStatuses)
                ->whereNotNull('completed_at')
                ->whereBetween('completed_at', [$startPeriod, $endPeriod])
                ->groupBy(DB::raw("YEARWEEK(completed_at, 1)"))
                ->get()
                ->keyBy('week_key');

            for ($i = 5; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                $weekKey = $weekStart->format('oW');

                $row = $weeklyRows->get($weekKey);

                $revenueLabels[] = $weekStart->format('d M') . ' - ' . $weekEnd->format('d M');
                $revenueChart[] = $row ? (float) $row->total_revenue : 0;
                $rentingTrendChart[] = $row ? (int) $row->total_booking : 0;
            }
        } else {
            // Monthly revenue untuk 6 bulan terakhir
            $startPeriod = now()->subMonths(5)->startOfMonth();

            $monthlyRows = RentalRequest::selectRaw("
                    DATE_FORMAT(completed_at, '%Y-%m') as period_key,
                    SUM(total_price) as total_revenue,
                    COUNT(*) as total_booking
                ")
                ->where('owner_id', $user->id)
                ->whereIn('status', $completedStatuses)
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', $startPeriod)
                ->groupBy(DB::raw("DATE_FORMAT(completed_at, '%Y-%m')"))
                ->get()
                ->keyBy('period_key');

            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $key = $month->format('Y-m');

                $row = $monthlyRows->get($key);

                $revenueLabels[] = $month->format('M Y');
                $revenueChart[] = $row ? (float) $row->total_revenue : 0;
                $rentingTrendChart[] = $row ? (int) $row->total_booking : 0;
            }
        }

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
                DATE_FORMAT(completed_at, '%Y-%m') as month_key,
                SUM(total_price) as revenue,
                COUNT(*) as bookings
            ")
            ->where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->whereNotNull('completed_at')
            ->whereBetween('completed_at', [
                    now()->subMonths(5)->startOfMonth(),
                    now()->subMonth()->endOfMonth(),
                ])
            ->groupBy(DB::raw("DATE_FORMAT(completed_at, '%Y-%m')"))
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $monthlyRecap = [];

        for ($i = 5; $i >= 1; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');

            $row = $monthlyRecapRows->get($key);

            $monthlyRecap[] = [
                'month' => $month->format('M Y'),
                'revenue' => $row ? (float) $row->revenue : 0,
                'bookings' => $row ? (int) $row->bookings : 0,
                'growth' => null
            ];
        }

        for ($i = 1; $i < count($monthlyRecap); $i++) {
            $previousRevenue = (float) $monthlyRecap[$i - 1]['revenue'];
            $currentRevenue = (float) $monthlyRecap[$i]['revenue'];

            if ($previousRevenue > 0) {
                $monthlyRecap[$i]['growth'] = (($currentRevenue - $previousRevenue) / $previousRevenue) * 100;
            }
        }

        // =========================
        // Monthly Growth Note
        // =========================

        $monthlyGrowthNote = 'Not enough previous month revenue data for comparison.';

        $lastMonth = end($monthlyRecap);
        $previousMonth = $monthlyRecap[count($monthlyRecap) - 2] ?? null;

        if (
            $lastMonth &&
            $previousMonth &&
            (float) $previousMonth['revenue'] > 0
        ) {
            $growth = (($lastMonth['revenue'] - $previousMonth['revenue']) / $previousMonth['revenue']) * 100;
            $direction = $growth >= 0 ? 'increased' : 'decreased';

            $monthlyGrowthNote = sprintf(
                'Revenue in %s %s %.1f%% compared to %s',
                $lastMonth['month'],
                $direction,
                abs($growth),
                $previousMonth['month']
            );
        }


        // =========================
        // Top Items Rented
        // =========================

        $rentedCountSubquery = RentalRequest::select(
                'product_id',
                DB::raw('COUNT(*) as rented_count')
            )
            ->where('owner_id', $user->id)
            ->whereIn('status', $completedStatuses)
            ->whereNotNull('completed_at')
            ->groupBy('product_id');

        $allTopItems = Product::with('primaryImage')
            ->leftJoinSub($rentedCountSubquery, 'rental_counts', function ($join) {
                $join->on('products.id', '=', 'rental_counts.product_id');
            })
            ->where('products.owner_id', $user->id)
            ->where('products.status', 'active')
            ->select(
                'products.*',
                DB::raw('COALESCE(rental_counts.rented_count, 0) as rented_count')
            )
            ->orderByDesc('rented_count')
            ->orderByDesc('products.total_rented')
            ->get();

        $topItems = $allTopItems->take(5);

        $storeItems = Product::with(['primaryImage', 'category'])
            ->where('owner_id', $user->id)
            ->latest()
            ->get();

        $categories = Category::orderBy('name')->get();

        $incomingRentalRequests = collect();

        if ($user->is_owner_active) {
            $incomingRentalRequests = RentalRequest::with([
                    'product.primaryImage',
                    'borrower',
                ])
                ->where('owner_id', $user->id)
                ->where('status', RentalRequest::STATUS_PENDING)
                ->latest()
                ->get();
        }

        return view('borrower.dashboard', compact(
            'user',
            'trustScore',
            'ongoingRequests',
            'historyRequests',
            'sellerStats',
            'sellerGrowth',
            'revenuePeriod',
            'revenueLabels',
            'revenueChart',
            'rentingTrendChart',
            'categoryChart',
            'monthlyRecap',
            'monthlyGrowthNote',
            'topItems',
            'allTopItems',
            'storeItems',
            'categories',
            'incomingRentalRequests',
        ));
    }
}