@extends('admin.layouts.master')

@section('title', 'گزارش درآمد')

@section('content')
<!-- Income Report Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">گزارش درآمد</h1>
            <p class="text-gray-400 text-sm lg:text-base">تحلیل و بررسی درآمدهای سیستم</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <!-- Filters -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <form method="GET" action="{{ route('admin.payment.income-report.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Start Date -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">از تاریخ</label>
                <input type="date" 
                       name="start_date" 
                       value="{{ request('start_date', $startDate->format('Y-m-d')) }}"
                       class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">تا تاریخ</label>
                <input type="date" 
                       name="end_date" 
                       value="{{ request('end_date', $endDate->format('Y-m-d')) }}"
                       class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
            </div>

            <!-- Payment Type Filter -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">نوع پرداخت</label>
                <select name="payment_type" 
                        class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                    <option value="">همه انواع</option>
                    <option value="ladder" {{ request('payment_type') == 'ladder' ? 'selected' : '' }}>نردبان</option>
                    <option value="special" {{ request('payment_type') == 'special' ? 'selected' : '' }}>ویژه</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex items-end">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 w-full">
                    <i class="fas fa-filter ml-2"></i>
                    فیلتر
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Total Revenue -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-500/20 p-3 rounded-lg">
                    <i class="fas fa-coins text-green-400 text-xl"></i>
                </div>
                <span class="text-{{ $growthRate >= 0 ? 'green' : 'red' }}-400 text-sm font-medium">
                    {{ $growthRate >= 0 ? '+' : '' }}{{ number_format($growthRate, 1) }}%
                </span>
            </div>
            <h3 class="text-gray-400 text-sm mb-1">کل درآمد</h3>
            <p class="text-yellow-primary font-bold text-2xl">{{ number_format($totalRevenue) }} تومان</p>
            <p class="text-gray-500 text-xs mt-2">دوره قبل: {{ number_format($previousRevenue) }} تومان</p>
        </div>

        <!-- Total Payments -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-500/20 p-3 rounded-lg">
                    <i class="fas fa-receipt text-blue-400 text-xl"></i>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm mb-1">تعداد تراکنش‌ها</h3>
            <p class="text-yellow-primary font-bold text-2xl">{{ number_format($totalPayments) }}</p>
            <p class="text-gray-500 text-xs mt-2">میانگین: {{ $totalPayments > 0 ? number_format($totalRevenue / $totalPayments) : 0 }} تومان</p>
        </div>

        <!-- Average Payment -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-500/20 p-3 rounded-lg">
                    <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                </div>
            </div>
            <h3 class="text-gray-400 text-sm mb-1">میانگین تراکنش</h3>
            <p class="text-yellow-primary font-bold text-2xl">{{ $totalPayments > 0 ? number_format($totalRevenue / $totalPayments) : 0 }} تومان</p>
            <p class="text-gray-500 text-xs mt-2">در بازه انتخابی</p>
        </div>
    </div>

    <!-- Revenue by Type -->
    @if($revenueByType->count() > 0)
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <h3 class="text-yellow-primary font-bold text-lg mb-4">درآمد بر اساس نوع</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($revenueByType as $type)
            <div class="bg-dark-tertiary rounded-lg p-4 border border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-300 font-medium">{{ $type['type'] }}</span>
                    <span class="text-gray-400 text-sm">{{ $type['count'] }} تراکنش</span>
                </div>
                <p class="text-yellow-primary font-bold text-xl">{{ number_format($type['total']) }} تومان</p>
                <div class="mt-2">
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div class="bg-yellow-primary h-2 rounded-full" style="width: {{ $totalRevenue > 0 ? ($type['total'] / $totalRevenue * 100) : 0 }}%"></div>
                    </div>
                    <span class="text-gray-500 text-xs mt-1 block">{{ $totalRevenue > 0 ? number_format($type['total'] / $totalRevenue * 100, 1) : 0 }}%</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Daily Revenue Chart -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <h3 class="text-yellow-primary font-bold text-lg mb-4">نمودار درآمد روزانه</h3>
        <div style="height: 300px; position: relative;">
            <canvas id="dailyRevenueChart"></canvas>
        </div>
    </div>

    <!-- Monthly Revenue -->
    @if($monthlyRevenue->count() > 0)
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <h3 class="text-yellow-primary font-bold text-lg mb-4">درآمد ماهانه</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">ماه/سال</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">تعداد تراکنش</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">مبلغ کل</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">میانگین</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyRevenue as $month)
                    <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50">
                        <td class="py-3 px-4 text-gray-300">
                            <span class="jalali-date" data-date="{{ \Carbon\Carbon::create($month->year, $month->month, 1)->toIso8601String() }}">
                                {{ \Carbon\Carbon::create($month->year, $month->month, 1)->format('Y-m') }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-300">{{ number_format($month->count) }}</td>
                        <td class="py-3 px-4 text-yellow-primary font-medium">{{ number_format($month->total) }} تومان</td>
                        <td class="py-3 px-4 text-gray-400">{{ number_format($month->total / $month->count) }} تومان</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Top Earning Advertisements -->
    @if($topAds->count() > 0)
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
        <h3 class="text-yellow-primary font-bold text-lg mb-4">پردرآمدترین آگهی‌ها</h3>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">عنوان آگهی</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">تعداد پرداخت</th>
                        <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">کل درآمد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topAds as $ad)
                    <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50">
                        <td class="py-3 px-4 text-gray-300">{{ Str::limit($ad->title, 50) }}</td>
                        <td class="py-3 px-4 text-gray-300">{{ number_format($ad->payment_count) }}</td>
                        <td class="py-3 px-4 text-yellow-primary font-medium">{{ number_format($ad->total) }} تومان</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Convert chart labels to Jalali
    document.addEventListener('DOMContentLoaded', function () {
        // Convert all .jalali-date elements to Persian calendar
        try {
            var nodes = document.querySelectorAll('.jalali-date[data-date]');
            nodes.forEach(function (el) {
                var iso = el.getAttribute('data-date');
                if (!iso) return;
                var d = new Date(iso);
                if (isNaN(d.getTime())) return;
                var faDate = d.toLocaleDateString('fa-IR');
                el.textContent = faDate;
            });
        } catch (e) {
            // noop
        }
    });

    // Daily Revenue Chart
    const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
    
    // Convert dates to Jalali for chart labels
    const chartLabels = {!! json_encode($dailyRevenue['days']) !!}.map(function(dateStr) {
        try {
            var d = new Date(dateStr);
            if (!isNaN(d.getTime())) {
                return d.toLocaleDateString('fa-IR', { year: 'numeric', month: '2-digit', day: '2-digit' });
            }
        } catch (e) {}
        return dateStr;
    });
    
    const dailyRevenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'درآمد (تومان)',
                data: {!! json_encode($dailyRevenue['revenue']) !!},
                borderColor: '#ffd700',
                backgroundColor: 'rgba(255, 215, 0, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#9ca3af'
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return 'درآمد: ' + new Intl.NumberFormat('fa-IR').format(context.parsed.y) + ' تومان';
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: '#9ca3af'
                    },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                },
                y: {
                    ticks: {
                        color: '#9ca3af',
                        callback: function(value) {
                            return new Intl.NumberFormat('fa-IR').format(value) + ' تومان';
                        }
                    },
                    grid: {
                        color: 'rgba(156, 163, 175, 0.1)'
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection

