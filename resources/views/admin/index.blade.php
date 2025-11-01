@extends('admin.layouts.master')

@section('title', 'پنل ادمین')

@section('content')
<!-- Dashboard Content -->
<main class="p-4 lg:p-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">کل کاربران</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">{{ number_format($totalUsers) }}</p>
                    @if($userGrowthRate >= 0)
                        <p class="text-green-400 text-xs lg:text-sm">↑ {{ number_format($userGrowthRate, 1) }}% از ماه قبل</p>
                    @else
                        <p class="text-red-400 text-xs lg:text-sm">↓ {{ number_format(abs($userGrowthRate), 1) }}% از ماه قبل</p>
                    @endif
                </div>
                <div class="bg-yellow-primary/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-users text-yellow-primary text-lg lg:text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">درآمد امروز</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">{{ number_format($todayRevenue) }} تومان</p>
                    @if($revenueGrowthRate >= 0)
                        <p class="text-green-400 text-xs lg:text-sm">↑ {{ number_format($revenueGrowthRate, 1) }}% از دیروز</p>
                    @else
                        <p class="text-red-400 text-xs lg:text-sm">↓ {{ number_format(abs($revenueGrowthRate), 1) }}% از دیروز</p>
                    @endif
                </div>
                <div class="bg-green-500/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-coins text-green-400 text-lg lg:text-xl"></i>
                </div>
            </div>
        </div>

        {{-- <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">سفارشات جدید</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">156</p>
                    <p class="text-red-400 text-xs lg:text-sm">↓ 3% از دیروز</p>
                </div>
                <div class="bg-blue-500/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-shopping-cart text-blue-400 text-lg lg:text-xl"></i>
                </div>
            </div>
        </div> --}}

        {{-- <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20 card-hover">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-xs lg:text-sm">بازدید سایت</p>
                    <p class="text-xl lg:text-2xl font-bold text-yellow-primary">89,420</p>
                    <p class="text-green-400 text-xs lg:text-sm">↑ 15% از هفته قبل</p>
                </div>
                <div class="bg-purple-500/20 p-3 lg:p-4 rounded-full">
                    <i class="fas fa-eye text-purple-400 text-lg lg:text-xl"></i>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Charts and Activity Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Chart -->
        <div class="xl:col-span-2 bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <h3 class="text-yellow-primary font-bold text-base lg:text-lg mb-4">نمودار درآمد</h3>
            <div class="h-48 lg:h-64" style="position: relative;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <h3 class="text-yellow-primary font-bold text-base lg:text-lg mb-4">فعالیت‌های اخیر</h3>
            <div class="space-y-3 lg:space-y-4">
                @php
                    $activities = [];
                    
                    // Add recent users
                    foreach($recentUsers->take(2) as $user) {
                        $activities[] = [
                            'type' => 'user',
                            'icon' => 'fa-user-plus',
                            'color' => 'green',
                            'message' => 'کاربر جدید عضو شد: ' . ($user->name ?? $user->mobile),
                            'time' => $user->created_at,
                        ];
                    }
                    
                    // Add recent ads
                    foreach($recentAds->take(2) as $ad) {
                        $activities[] = [
                            'type' => 'ad',
                            'icon' => 'fa-newspaper',
                            'color' => 'blue',
                            'message' => 'آگهی جدید: ' . \Illuminate\Support\Str::limit($ad->title, 30),
                            'time' => $ad->created_at,
                        ];
                    }
                    
                    // Add recent payments
                    foreach($recentPayments->take(2) as $payment) {
                        $activities[] = [
                            'type' => 'payment',
                            'icon' => 'fa-coins',
                            'color' => 'yellow-primary',
                            'message' => 'پرداخت جدید: ' . number_format($payment->amount) . ' تومان',
                            'time' => $payment->created_at,
                        ];
                    }
                    
                    // Sort by time desc and take 4 most recent
                    usort($activities, function($a, $b) {
                        return $b['time']->timestamp <=> $a['time']->timestamp;
                    });
                    $activities = array_slice($activities, 0, 4);
                    
                    function formatTimeAgo($time) {
                        $diff = $time->diffForHumans(null, false, true);
                        return str_replace(['ثانیه', 'دقیقه', 'ساعت', 'روز'], ['ثانیه', 'دقیقه', 'ساعت', 'روز'], $diff);
                    }
                @endphp
                
                @foreach($activities as $activity)
                    <div class="flex items-center gap-3">
                        @if($activity['color'] === 'yellow-primary')
                            <div class="w-6 h-6 lg:w-8 lg:h-8 bg-yellow-primary/20 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas {{ $activity['icon'] }} text-yellow-primary text-xs"></i>
                            </div>
                        @else
                            <div class="w-6 h-6 lg:w-8 lg:h-8 bg-{{ $activity['color'] }}-500 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas {{ $activity['icon'] }} text-white text-xs"></i>
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-gray-300 text-xs lg:text-sm">{{ $activity['message'] }}</p>
                            <p class="text-gray-500 text-xs">{{ formatTimeAgo($activity['time']) }}</p>
                        </div>
                    </div>
                @endforeach
                
                @if(empty($activities))
                    <p class="text-gray-400 text-sm text-center py-4">هیچ فعالیتی یافت نشد</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6 mb-6 lg:mb-8">
        <!-- Recent Advertisements Table -->
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                <h3 class="text-yellow-primary font-bold text-base lg:text-lg">آخرین آگهی ها</h3>
                <a href="{{ route('admin.advertisements.index') }}"
                    class="text-yellow-primary hover:text-yellow-secondary text-xs lg:text-sm self-start sm:self-auto">مشاهده
                    همه</a>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full min-w-[500px]">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                شماره</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                کاربر</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                درآمد</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestAds as $ad)
                            @php
                                $totalRevenue = $ad->payments()->where('status', \App\Models\Payment::STATUS_PAID)->sum('amount');
                                $statusLabels = [
                                    0 => ['label' => 'غیرفعال', 'class' => 'bg-gray-500/20 text-gray-400'],
                                    1 => ['label' => 'در حال بررسی', 'class' => 'bg-yellow-500/20 text-yellow-400'],
                                    2 => ['label' => 'تایید شده', 'class' => 'bg-green-500/20 text-green-400'],
                                    3 => ['label' => 'رد شده', 'class' => 'bg-red-500/20 text-red-400'],
                                    4 => ['label' => 'منقضی شده', 'class' => 'bg-gray-500/20 text-gray-400'],
                                ];
                                $status = $statusLabels[$ad->status] ?? $statusLabels[0];
                            @endphp
                            <tr class="border-b border-gray-800">
                                <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">#{{ $ad->id }}</td>
                                <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">{{ $ad->user->name ?? $ad->user->mobile }}</td>
                                <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">{{ number_format($totalRevenue) }} تومان</td>
                                <td class="py-2 lg:py-3">
                                    <span class="px-2 py-1 rounded-full text-xs {{ $status['class'] }}">{{ $status['label'] }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-gray-400 text-sm">هیچ آگهی‌ای یافت نشد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Viewed Advertisements Table -->
        <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                <h3 class="text-yellow-primary font-bold text-base lg:text-lg">آگهی های پربازدید</h3>
                <a href="{{ route('admin.advertisements.index') }}"
                    class="text-yellow-primary hover:text-yellow-secondary text-xs lg:text-sm self-start sm:self-auto">مشاهده
                    همه</a>
            </div>
            <div class="table-container overflow-x-auto">
                <table class="w-full min-w-[400px]">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                آگهی</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                بازدید</th>
                            <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                                درآمد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topViewedAds as $ad)
                            @php
                                $totalRevenue = $ad->payments()->where('status', \App\Models\Payment::STATUS_PAID)->sum('amount');
                            @endphp
                            <tr class="border-b border-gray-800">
                                <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">{{ \Illuminate\Support\Str::limit($ad->title, 25) }}</td>
                                <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm">{{ number_format($ad->view) }}</td>
                                <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">{{ number_format($totalRevenue) }} تومان</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-8 text-center text-gray-400 text-sm">هیچ آگهی‌ای یافت نشد</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Full Width Table -->
    <div class="bg-dark-secondary rounded-xl p-4 lg:p-6 border border-yellow-primary/20">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
            <h3 class="text-yellow-primary font-bold text-base lg:text-lg">گزارش مالی ماهانه</h3>
            <div class="flex gap-2">
                <a href="{{ route('admin.payment.income-report.index') }}" 
                   class="text-yellow-primary hover:text-yellow-secondary text-xs">گزارش کامل</a>
            </div>
        </div>
        <div class="table-container overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">ماه
                        </th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">کل فروش
                        </th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">
                            هزینه‌ها</th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">سود
                            خالص</th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">تعداد
                            تراکنش</th>
                        <th class="text-right text-gray-400 font-medium py-2 lg:py-3 text-xs lg:text-sm">رشد
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyFinancial as $month)
                        @php
                            $monthDate = $month['month_date'];
                        @endphp
                        <tr class="border-b border-gray-800">
                            <td class="py-2 lg:py-3 text-yellow-primary text-xs lg:text-sm font-medium">
                                <span class="jalali-date" data-date="{{ $monthDate->toIso8601String() }}">
                                    {{ $monthDate->format('Y-m') }}
                                </span>
                            </td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">{{ number_format($month['total_sales']) }} تومان</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">{{ number_format($month['costs']) }} تومان</td>
                            <td class="py-2 lg:py-3 text-green-400 text-xs lg:text-sm">{{ number_format($month['net_profit']) }} تومان</td>
                            <td class="py-2 lg:py-3 text-gray-300 text-xs lg:text-sm">{{ number_format($month['order_count']) }}</td>
                            <td class="py-2 lg:py-3 text-xs lg:text-sm">
                                @if($month['growth'] !== null)
                                    @if($month['growth'] >= 0)
                                        <span class="text-green-400">↑ {{ number_format($month['growth'], 1) }}%</span>
                                    @else
                                        <span class="text-red-400">↓ {{ number_format(abs($month['growth']), 1) }}%</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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

        // Revenue Chart
        const ctx = document.getElementById('revenueChart');
        if (!ctx) return;
        
        const chartContext = ctx.getContext('2d');
        
        // Monthly revenue data
        const monthlyData = {!! json_encode($monthlyRevenue) !!};
        const labels = monthlyData.map(function(item) {
            try {
                var d = new Date(item.month);
                if (!isNaN(d.getTime())) {
                    return d.toLocaleDateString('fa-IR', { year: 'numeric', month: 'short' });
                }
            } catch (e) {
                console.error('Error parsing date:', item.month, e);
            }
            return item.month;
        });
        
        const revenueData = monthlyData.map(function(item) {
            return item.revenue;
        });
        
        const revenueChart = new Chart(chartContext, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'درآمد (تومان)',
                    data: revenueData,
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
    });
</script>
@endpush
@endsection