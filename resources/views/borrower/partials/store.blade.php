<div class="store-dashboard-wrapper py-4">

    @if (!$user->is_owner_active)
        {{-- EMPTY STORE STATE --}}
        <div class="empty-store-state">
            <h1>OOPS, YOU HAVE NO STORE!</h1>

            <form action="{{ route('borrower.store.open') }}" method="POST">
                @csrf
                <button type="submit" class="btn-open-store">
                    Open Store NOW!
                </button>
            </form>
        </div>
    @else
        {{-- SELLER DASHBOARD --}}
        <div class="seller-dashboard">

            <h5 class="seller-section-title mb-4">Rental Statistics</h5>

            {{-- STATS CARDS --}}
            <div class="seller-stats-grid mb-4">

                <div class="seller-stat-card">
                    <div class="stat-icon bg-primary-subtle text-primary">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <small>Total Rent Income</small>
                    <h4>Rp {{ number_format($sellerStats['income'] ?? 0, 0, ',', '.') }}</h4>
                    <p>▲ 11.8% vs Jan 2026</p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-success-subtle text-success">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <small>Total Transaction</small>
                    <h4>{{ $sellerStats['transactions'] ?? 0 }}</h4>
                    <p>▲ 15.2% vs Jan 2026</p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-info-subtle text-info">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <small>Available Items</small>
                    <h4>{{ $sellerStats['items'] ?? 0 }}</h4>
                    <p>▲ 8.4% vs Jan 2026</p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-arrow-left-right"></i>
                    </div>
                    <small>Ongoing Rent</small>
                    <h4>{{ $sellerStats['ongoing'] ?? 0 }}</h4>
                    <p>▲ 6.7% vs Jan 2026</p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-warning-subtle text-warning">
                        <i class="bi bi-star"></i>
                    </div>
                    <small>Average Rating</small>
                    <h4>{{ $sellerStats['rating'] ?? '0 / 5.0' }}</h4>
                    <p>▲ 0.4 vs Jan 2026</p>
                </div>

                <div class="seller-stat-card">
                    <div class="stat-icon bg-danger-subtle text-danger">
                        <i class="bi bi-people"></i>
                    </div>
                    <small>Followers</small>
                    <h4>{{ $sellerStats['followers'] ?? 0 }}</h4>
                    <p>▲ 12 vs Jan 2026</p>
                </div>

            </div>

            {{-- DASHBOARD GRID --}}
            <div class="seller-grid">

                {{-- Revenue Stream --}}
                <div class="seller-panel revenue-panel">
                    <div class="seller-panel-header">
                        <h6>Revenue Stream</h6>
                        <select>
                            <option>Monthly</option>
                        </select>
                    </div>

                    <div class="chart-box chart-large">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                {{-- Revenue By Category --}}
                <div class="seller-panel category-panel">
                    <h6>Revenue By Category</h6>

                    <div class="category-content">
                        <div class="chart-box chart-donut">
                            <canvas id="categoryChart"></canvas>
                        </div>

                        <ul class="category-list">
                            @foreach ($categoryChart as $category => $value)
                                <li>
                                    <span></span>
                                    <div>
                                        <strong>{{ $category }}</strong>
                                        <small>{{ $value }}</small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Renting Trend --}}
                <div class="seller-panel trend-panel">
                    <div class="seller-panel-header">
                        <h6>Renting Trend</h6>
                        <select>
                            <option>Monthly</option>
                        </select>
                    </div>

                    <div class="chart-box chart-large">
                        <canvas id="rentingTrendChart"></canvas>
                    </div>
                </div>

                {{-- Monthly Recap --}}
                <div class="seller-panel recap-panel">
                    <h6>Monthly Recap</h6>

                    <table class="table table-sm seller-table mb-3">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Revenue</th>
                                <th>Bookings</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($monthlyRecap as $recap)
                                <tr class="{{ $loop->last ? 'active-row' : '' }}">
                                    <td>{{ $recap['month'] }}</td>
                                    <td>Rp. {{ number_format($recap['revenue'], 0, ',', '.') }}</td>
                                    <td>{{ $recap['bookings'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="seller-success-note">
                        Revenue in February 2026 increased 11.8% compared to January 2026
                    </div>
                </div>

                {{-- Top Items --}}
                <div class="seller-panel top-items-panel">
                    <div class="seller-panel-header">
                        <h6>Top Items Rented</h6>
                        <a href="#">See all</a>
                    </div>

                    <div class="top-items-grid">
                        @forelse ($topItems as $index => $item)
                            @php
                                $itemName = $item->name ?? $item->title ?? 'Item';
                                $itemPrice = $item->price_per_day ?? $item->price ?? 0;
                                $imagePath = $item->primaryImage->image_path ?? null;
                            @endphp

                            <div class="top-item-card">
                                <div class="item-rank">{{ $index + 1 }}</div>

                                <img
                                    src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('images/placeholder-product.png') }}"
                                    alt="{{ $itemName }}"
                                >

                                <h6>{{ $itemName }}</h6>
                                <p>Rp {{ number_format($itemPrice, 0, ',', '.') }}/day</p>
                            </div>
                        @empty
                            <div class="text-muted small">
                                Belum ada item yang disewakan.
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
window.initStoreCharts = function () {
    if (window.storeChartsLoaded) return;
    window.storeChartsLoaded = true;

    const revenueCanvas = document.getElementById('revenueChart');
    const trendCanvas = document.getElementById('rentingTrendChart');
    const categoryCanvas = document.getElementById('categoryChart');

    if (!revenueCanvas || !trendCanvas || !categoryCanvas) {
        return;
    }

    const revenueLabels = @json($revenueLabels ?? []);
    const revenueData = @json($revenueChart ?? []);
    const trendData = @json($rentingTrendChart ?? []);
    const categoryLabels = @json(array_keys($categoryChart ?? []));
    const categoryData = @json(array_values($categoryChart ?? []));

    const chartTextColor = '#6a6f80';
    const gridColor = '#eef0f6';

    new Chart(revenueCanvas, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [{
                data: revenueData,
                backgroundColor: '#0d3f9f',
                borderRadius: 5,
                barThickness: 9,
                maxBarThickness: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + Number(context.raw).toLocaleString('id-ID');
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 8
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        },
                        callback: function(value) {
                            return value / 1000000 + ' jt';
                        }
                    }
                }
            }
        }
    });

    new Chart(trendCanvas, {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                data: trendData,
                borderColor: '#1c6bff',
                backgroundColor: 'rgba(28, 107, 255, 0.12)',
                borderWidth: 3,
                tension: 0.4,
                pointRadius: 3,
                pointHoverRadius: 5,
                pointBackgroundColor: '#1c6bff',
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        },
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 8
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: gridColor
                    },
                    ticks: {
                        color: chartTextColor,
                        font: {
                            size: 10
                        }
                    }
                }
            }
        }
    });

    new Chart(categoryCanvas, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryData,
                backgroundColor: [
                    '#08328D',
                    '#1C6BFF',
                    '#8C5CFF',
                    '#F9C846',
                    '#56C596',
                    '#FF6B6B',
                    '#20C997'
                ],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw;
                        }
                    }
                }
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get('tab') === 'store') {
        setTimeout(function () {
            window.initStoreCharts();
        }, 250);
    }
});
</script>
@endpush