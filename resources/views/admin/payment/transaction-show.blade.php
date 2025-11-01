@extends('admin.layouts.master')

@section('title', 'جزئیات تراکنش')

@section('content')
<!-- Transaction Details Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">جزئیات تراکنش</h1>
            <p class="text-gray-400 text-sm lg:text-base">مشاهده اطلاعات کامل تراکنش #{{ $payment->id }}</p>
        </div>
        <a href="{{ route('admin.payment.transactions.index') }}" 
           class="bg-gray-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-arrow-right ml-2"></i>
            بازگشت به لیست
        </a>
    </div>

    <!-- Transaction Details -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Main Information -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">اطلاعات اصلی</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">شناسه تراکنش:</span>
                    <span class="text-gray-300 font-medium">#{{ $payment->id }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">مبلغ:</span>
                    <span class="text-yellow-primary font-bold text-lg">{{ number_format($payment->amount) }} تومان</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">نوع پرداخت:</span>
                    <span class="text-blue-400 font-medium">{{ $payment->payment_type_label }}</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">مدت زمان:</span>
                    <span class="text-gray-300">{{ $payment->duration_days }} روز</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">وضعیت:</span>
                    @if($payment->status === 'paid')
                        <span class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-sm font-medium">
                            پرداخت شده
                        </span>
                    @elseif($payment->status === 'pending')
                        <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-sm font-medium">
                            در انتظار
                        </span>
                    @else
                        <span class="bg-red-500/20 text-red-400 px-3 py-1 rounded-full text-sm font-medium">
                            ناموفق
                        </span>
                    @endif
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">تاریخ ایجاد:</span>
                    <span class="text-gray-300 jalali-date" data-date="{{ $payment->created_at?->toIso8601String() }}">{{ $payment->created_at?->format('Y-m-d H:i:s') }}</span>
                </div>
                @if($payment->updated_at != $payment->created_at)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">آخرین بروزرسانی:</span>
                    <span class="text-gray-300 jalali-date" data-date="{{ $payment->updated_at?->toIso8601String() }}">{{ $payment->updated_at?->format('Y-m-d H:i:s') }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Payment Gateway Information -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">اطلاعات درگاه</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">Authority:</span>
                    <span class="text-gray-300 font-mono text-sm">{{ $payment->authority }}</span>
                </div>
                @if($payment->ref_id)
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">کد پیگیری:</span>
                    <span class="text-green-400 font-mono font-medium">{{ $payment->ref_id }}</span>
                </div>
                @endif
                @if($payment->trace_no)
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">شماره پیگیری:</span>
                    <span class="text-gray-300 font-mono text-sm">{{ $payment->trace_no }}</span>
                </div>
                @endif
                @if($payment->card_pan)
                <div class="flex justify-between items-center pb-3 border-b border-gray-700">
                    <span class="text-gray-400">شماره کارت:</span>
                    <span class="text-gray-300 font-mono text-sm">{{ $payment->card_pan }}</span>
                </div>
                @endif
                @if($payment->gateway_response)
                <div class="mt-4">
                    <span class="text-gray-400 block mb-2">پاسخ درگاه:</span>
                    <pre class="bg-dark-tertiary p-3 rounded-lg text-xs text-gray-300 overflow-auto">{{ json_encode($payment->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- User and Advertisement Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- User Information -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">اطلاعات کاربر</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">نام:</span>
                    <span class="text-gray-300">{{ $payment->user->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">موبایل:</span>
                    <span class="text-gray-300">{{ $payment->user->mobile }}</span>
                </div>
                @if($payment->user->email)
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">ایمیل:</span>
                    <span class="text-gray-300">{{ $payment->user->email }}</span>
                </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('admin.users.show', $payment->user) }}" 
                       class="text-blue-400 hover:text-blue-300 text-sm">
                        <i class="fas fa-user ml-1"></i>
                        مشاهده پروفایل کاربر
                    </a>
                </div>
            </div>
        </div>

        <!-- Advertisement Information -->
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">اطلاعات آگهی</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-start">
                    <span class="text-gray-400">عنوان:</span>
                    <span class="text-gray-300 text-right">{{ $payment->advertisement->title ?? '-' }}</span>
                </div>
                @if($payment->description)
                <div class="mt-4">
                    <span class="text-gray-400 block mb-2">توضیحات:</span>
                    <p class="text-gray-300 text-sm">{{ $payment->description }}</p>
                </div>
                @endif
                <div class="mt-4">
                    <a href="{{ route('admin.advertisements.show', $payment->advertisement) }}" 
                       class="text-blue-400 hover:text-blue-300 text-sm">
                        <i class="fas fa-ad ml-1"></i>
                        مشاهده آگهی
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
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

