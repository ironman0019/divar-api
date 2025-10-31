@extends('admin.layouts.master')

@section('title', 'آمار و تحلیل')

@section('content')
<!-- Statistics Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">آمار و تحلیل</h1>
            <p class="text-gray-400 text-sm lg:text-base">نمای کلی از آمار و اطلاعات سیستم</p>
        </div>
        
        <!-- Date Range Filter -->
        <form method="GET" action="{{ route('admin.statistics.index') }}" class="mt-4 sm:mt-0">
            <select name="days" 
                    onchange="this.form.submit()"
                    class="bg-dark-secondary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 روز اخیر</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 روز اخیر</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 روز اخیر</option>
                <option value="365" {{ $days == 365 ? 'selected' : '' }}>سال جاری</option>
            </select>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Users -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-500/20 p-3 rounded-lg">
                    <i class="fas fa-users text-blue-400 text-xl"></i>
                </div>
                <span class="text-green-400 text-sm font-medium">+{{ $userStats['new_this_month'] }} این ماه</span>
            </div>
            <h3 class="text-gray-400 text-sm mb-1">کل کاربران</h3>
            <p class="text-yellow-primary font-bold text-2xl">{{ number_format($userStats['total']) }}</p>
            <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                <span>فعال: {{ number_format($userStats['active']) }}</span>
                <span>غیرفعال: {{ number_format($userStats['inactive']) }}</span>
            </div>
        </div>

        <!-- Total Advertisements -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-purple-500/20 p-3 rounded-lg">
                    <i class="fas fa-ad text-purple-400 text-xl"></i>
                </div>
                <span class="text-green-400 text-sm font-medium">+{{ $advertisementStats['new'] }} در بازه انتخابی</span>
            </div>
            <h3 class="text-gray-400 text-sm mb-1">کل آگهی‌ها</h3>
            <p class="text-yellow-primary font-bold text-2xl">{{ number_format($advertisementStats['total']) }}</p>
            <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                <span>فعال: {{ number_format($advertisementStats['active']) }}</span>
                <span>در انتظار: {{ number_format($advertisementStats['pending']) }}</span>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-500/20 p-3 rounded-lg">
                    <i class="fas fa-coins text-green-400 text-xl"></i>
                </div>
                <span class="text-green-400 text-sm font-medium">{{ number_format($paymentStats['revenue_this_month']) }} تومان این ماه</span>
            </div>
            <h3 class="text-gray-400 text-sm mb-1">درآمد کل</h3>
            <p class="text-yellow-primary font-bold text-2xl">{{ number_format($paymentStats['total_revenue']) }} تومان</p>
            <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                <span>پرداخت شده: {{ number_format($paymentStats['paid']) }}</span>
                <span>در انتظار: {{ number_format($paymentStats['pending']) }}</span>
            </div>
        </div>

        <!-- Total Views -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-500/20 p-3 rounded-lg">
                    <i class="fas fa-eye text-yellow-400 text-xl"></i>
                </div>
                <span class="text-green-400 text-sm font-medium">میانگین: {{ number_format($advertisementStats['avg_views']) }}</span>
            </div>
            <h3 class="text-gray-400 text-sm mb-1">کل بازدیدها</h3>
            <p class="text-yellow-primary font-bold text-2xl">{{ number_format($advertisementStats['total_views']) }}</p>
            <div class="flex items-center gap-4 mt-4 text-xs text-gray-500">
                <span>آگهی ویژه: {{ number_format($advertisementStats['featured']) }}</span>
                <span>نردبان: {{ number_format($advertisementStats['ladder']) }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Daily Statistics Chart -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">آمار روزانه</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>

        <!-- Payment Statistics Chart -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">آمار پرداخت‌ها</h3>
            <div style="height: 300px; position: relative;">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Top Categories -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">پربازدیدترین دسته‌بندی‌ها</h3>
            <div class="space-y-3">
                @forelse($advertisementStats['by_category'] as $category)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300 text-sm">{{ $category->name }}</span>
                        <span class="text-yellow-primary font-medium">{{ number_format($category->count) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">اطلاعاتی موجود نیست</p>
                @endforelse
            </div>
        </div>

        <!-- Top Cities by Users -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">شهرهای برتر (کاربران)</h3>
            <div class="space-y-3">
                @forelse($cityStats['top_by_users'] as $city)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300 text-sm">{{ $city->name }}</span>
                        <span class="text-yellow-primary font-medium">{{ number_format($city->user_count) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">اطلاعاتی موجود نیست</p>
                @endforelse
            </div>
        </div>

        <!-- Top Cities by Ads -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">شهرهای برتر (آگهی‌ها)</h3>
            <div class="space-y-3">
                @forelse($cityStats['top_by_ads'] as $city)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-300 text-sm">{{ $city->name }}</span>
                        <span class="text-yellow-primary font-medium">{{ number_format($city->ad_count) }}</span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">اطلاعاتی موجود نیست</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Users -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">کاربران اخیر</h3>
            <div class="space-y-3">
                @forelse($recentActivity['users'] as $user)
                    <div class="flex items-center justify-between pb-3 border-b border-gray-700">
                        <div>
                            <p class="text-gray-300 text-sm font-medium">{{ $user->name ?? $user->mobile }}</p>
                            <p class="text-gray-500 text-xs">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs {{ $user->is_active ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ $user->is_active ? 'فعال' : 'غیرفعال' }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">کاربری ثبت نشده است</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Advertisements -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">آگهی‌های اخیر</h3>
            <div class="space-y-3">
                @forelse($recentActivity['ads'] as $ad)
                    <div class="pb-3 border-b border-gray-700">
                        <p class="text-gray-300 text-sm font-medium mb-1">{{ Str::limit($ad->title, 40) }}</p>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span>{{ $ad->category->name ?? '-' }}</span>
                            <span>•</span>
                            <span>{{ $ad->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">آگهی‌ای ثبت نشده است</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">پرداخت‌های اخیر</h3>
            <div class="space-y-3">
                @forelse($recentActivity['payments'] as $payment)
                    <div class="flex items-center justify-between pb-3 border-b border-gray-700">
                        <div>
                            <p class="text-gray-300 text-sm font-medium">{{ number_format($payment->amount) }} تومان</p>
                            <p class="text-gray-500 text-xs">{{ $payment->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="px-2 py-1 rounded text-xs bg-green-500/20 text-green-400">
                            {{ $payment->payment_type_label }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">پرداختی انجام نشده است</p>
                @endforelse
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Wait for DOM to be ready and destroy existing charts
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Statistics Chart
        const dailyCanvas = document.getElementById('dailyChart');
        if (!dailyCanvas) return;
        
        // Destroy existing chart if it exists
        if (window.dailyChartInstance) {
            window.dailyChartInstance.destroy();
        }
        
        const dailyCtx = dailyCanvas.getContext('2d');
        window.dailyChartInstance = new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: @json($dailyStats['days']),
            datasets: [
                {
                    label: 'کاربران جدید',
                    data: @json($dailyStats['users']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'آگهی‌های جدید',
                    data: @json($dailyStats['ads']),
                    borderColor: 'rgb(168, 85, 247)',
                    backgroundColor: 'rgba(168, 85, 247, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#9ca3af'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#6b7280'
                    },
                    grid: {
                        color: '#374151'
                    }
                },
                x: {
                    ticks: {
                        color: '#6b7280',
                        maxRotation: 45,
                        minRotation: 45,
                        maxTicksLimit: 20
                    },
                    grid: {
                        color: '#374151',
                        display: false
                    }
                }
            },
            animation: {
                duration: 750
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
        });

        // Payment Statistics Chart
        const paymentCanvas = document.getElementById('paymentChart');
        if (!paymentCanvas) return;
        
        // Destroy existing chart if it exists
        if (window.paymentChartInstance) {
            window.paymentChartInstance.destroy();
        }
        
        const paymentCtx = paymentCanvas.getContext('2d');
        window.paymentChartInstance = new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: ['پرداخت شده', 'در انتظار', 'ناموفق'],
            datasets: [{
                data: [
                    {{ $paymentStats['paid'] }},
                    {{ $paymentStats['pending'] }},
                    {{ $paymentStats['failed'] }}
                ],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(239, 68, 68, 0.8)'
                ],
                borderColor: [
                    'rgb(34, 197, 94)',
                    'rgb(251, 191, 36)',
                    'rgb(239, 68, 68)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#9ca3af',
                        padding: 15
                    }
                }
            }
        }
        });
    });
</script>
@endpush
@endsection

