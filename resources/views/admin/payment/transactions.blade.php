@extends('admin.layouts.master')

@section('title', 'تراکنش‌ها')

@section('content')
<!-- Transactions Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">تراکنش‌ها</h1>
            <p class="text-gray-400 text-sm lg:text-base">مدیریت و مشاهده تمام تراکنش‌های سیستم</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">کل تراکنش‌ها</p>
                    <p class="text-yellow-primary font-bold text-xl">{{ number_format($stats['total']) }}</p>
                </div>
                <i class="fas fa-receipt text-gray-500 text-2xl"></i>
            </div>
        </div>
        <div class="bg-dark-secondary rounded-xl border border-green-500/20 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">پرداخت شده</p>
                    <p class="text-green-400 font-bold text-xl">{{ number_format($stats['paid']) }}</p>
                </div>
                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
            </div>
        </div>
        <div class="bg-dark-secondary rounded-xl border border-yellow-500/20 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">در انتظار</p>
                    <p class="text-yellow-400 font-bold text-xl">{{ number_format($stats['pending']) }}</p>
                </div>
                <i class="fas fa-clock text-yellow-400 text-2xl"></i>
            </div>
        </div>
        <div class="bg-dark-secondary rounded-xl border border-red-500/20 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-sm mb-1">ناموفق</p>
                    <p class="text-red-400 font-bold text-xl">{{ number_format($stats['failed']) }}</p>
                </div>
                <i class="fas fa-times-circle text-red-400 text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <form method="GET" action="{{ route('admin.payment.transactions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">جستجو</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="کد پیگیری، شماره کارت، کاربر..."
                       class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">وضعیت</label>
                <select name="status" 
                        class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>پرداخت شده</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>در انتظار</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>ناموفق</option>
                </select>
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

            <!-- Date Range -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">از تاریخ</label>
                @include('admin.components.jalali-date-input', [
                    'name' => 'start_date',
                    'value' => request('start_date'),
                    'placeholder' => 'از تاریخ',
                ])
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">تا تاریخ</label>
                @include('admin.components.jalali-date-input', [
                    'name' => 'end_date',
                    'value' => request('end_date'),
                    'placeholder' => 'تا تاریخ',
                ])
            </div>

            <!-- Min Amount -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">حداقل مبلغ</label>
                <input type="number" 
                       name="min_amount" 
                       value="{{ request('min_amount') }}"
                       placeholder="تومان"
                       class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
            </div>

            <!-- Max Amount -->
            <div>
                <label class="block text-gray-300 font-medium mb-2 text-sm">حداکثر مبلغ</label>
                <input type="number" 
                       name="max_amount" 
                       value="{{ request('max_amount') }}"
                       placeholder="تومان"
                       class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-4 mt-4">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-search ml-2"></i>
                    جستجو
                </button>
                <a href="{{ route('admin.payment.transactions.index') }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200 mr-2">
                    <i class="fas fa-times ml-2"></i>
                    پاک کردن فیلترها
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">شناسه</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">کاربر</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">آگهی</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">مبلغ</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">نوع</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">وضعیت</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">کد پیگیری</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">تاریخ</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50 transition-colors duration-200">
                            <td class="py-4 px-6">
                                <div class="text-gray-300 font-medium">#{{ $payment->id }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">{{ $payment->user->name ?? $payment->user->mobile }}</div>
                                <div class="text-gray-500 text-xs">{{ $payment->user->mobile }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">{{ Str::limit($payment->advertisement->title ?? '-', 30) }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-yellow-primary font-medium">{{ number_format($payment->amount) }} تومان</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ $payment->payment_type_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($payment->status === 'paid')
                                    <span class="bg-green-500/20 text-green-400 px-2 py-1 rounded-full text-xs font-medium">
                                        پرداخت شده
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-full text-xs font-medium">
                                        در انتظار
                                    </span>
                                @else
                                    <span class="bg-red-500/20 text-red-400 px-2 py-1 rounded-full text-xs font-medium">
                                        ناموفق
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-400 text-sm font-mono">{{ $payment->ref_id ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-400 text-sm jalali-date" data-date="{{ $payment->created_at?->toIso8601String() }}">
                                    {{ $payment->created_at?->format('Y-m-d H:i') }}
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('admin.payment.transactions.show', $payment) }}" 
                                   class="text-blue-400 hover:text-blue-300 p-2 rounded-lg hover:bg-blue-500/20 transition-colors duration-200"
                                   title="مشاهده جزئیات">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-400">
                                <i class="fas fa-receipt text-4xl mb-4 block"></i>
                                <p>هیچ تراکنشی یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($payments->hasPages())
        <div class="mt-6">
            {{ $payments->links() }}
        </div>
    @endif
</main>

@push('scripts')
@include('admin.components.jalali-datepicker-assets')
<script>
    // Convert all .jalali-date elements to Persian calendar
    document.addEventListener('DOMContentLoaded', function () {
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
</script>
@endpush
@endsection

