@extends('admin.layouts.master')

@section('title', 'مشاهده آگهی')

@section('content')
<!-- Advertisement Details Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مشاهده آگهی</h1>
            <p class="text-gray-400 text-sm lg:text-base">{{ $advertisement->title }}</p>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.advertisements.edit', $advertisement) }}" 
               class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                <i class="fas fa-edit ml-2"></i>
                ویرایش
            </a>
            <a href="{{ route('admin.advertisements.index') }}" 
               class="bg-gray-600 text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                بازگشت
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Advertisement Details -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h2 class="text-yellow-primary font-bold text-lg mb-4">جزئیات آگهی</h2>
                @php
                    $adsTypeMap = [
                        'game' => 'بازی',
                        'service' => 'خدمت',
                        'product' => 'محصول',
                        'sell' => 'فروش',
                    ];
                    $adsStatusMap = [
                        'brand_new' => 'نو',
                        'as_good_as_new' => 'در حد نو',
                        'used' => 'کارکرده',
                        'needs_repair' => 'نیازمند تعمیر',
                        'excellent' => 'عالی',
                    ];
                    $toJalali = function ($date) {
                        if (!$date) return null;
                        try {
                            $carbon = \Illuminate\Support\Carbon::parse($date);
                            $tz = new \DateTimeZone(config('app.timezone', 'UTC'));
                            $fmt = new \IntlDateFormatter('fa_IR@calendar=persian', \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE, $tz, \IntlDateFormatter::TRADITIONAL, 'yyyy/MM/dd');
                            return $fmt->format($carbon);
                        } catch (\Throwable $e) {
                            return \Illuminate\Support\Carbon::parse($date)->format('Y/m/d');
                        }
                    };
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-300 font-medium mb-2">عنوان</label>
                        <p class="text-gray-400">{{ $advertisement->title }}</p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-300 font-medium mb-2">توضیحات</label>
                        <p class="text-gray-400 whitespace-pre-line">{{ $advertisement->description }}</p>
                    </div>

                    <!-- Price -->
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">قیمت</label>
                        @if($advertisement->price)
                            <p class="text-yellow-primary font-bold text-lg">{{ number_format($advertisement->price) }} تومان</p>
                        @else
                            <p class="text-gray-400">توافقی</p>
                        @endif
                    </div>

                    <!-- Contact -->
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">تماس</label>
                        <p class="text-gray-400">{{ $advertisement->contact ?? 'تعریف نشده' }}</p>
                    </div>

                    <!-- Ads Type -->
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">نوع آگهی</label>
                        <p class="text-gray-400">{{ $advertisement->ads_type ? ($adsTypeMap[$advertisement->ads_type] ?? $advertisement->ads_type) : 'تعریف نشده' }}</p>
                    </div>

                    <!-- Ads Status -->
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">وضعیت آگهی</label>
                        <p class="text-gray-400">{{ $advertisement->ads_status ? ($adsStatusMap[$advertisement->ads_status] ?? $advertisement->ads_status) : 'تعریف نشده' }}</p>
                    </div>

                    <!-- Tags -->
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">برچسب‌ها</label>
                        <p class="text-gray-400">{{ $advertisement->tags ?? 'تعریف نشده' }}</p>
                    </div>

                    <!-- Willing to Trade -->
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">آماده معاوضه</label>
                        <p class="text-gray-400">
                            @if($advertisement->willing_to_trade)
                                <span class="text-green-400">بله</span>
                            @else
                                <span class="text-red-400">خیر</span>
                            @endif
                        </p>
                    </div>

                    <!-- Location -->
                    @if($advertisement->lat && $advertisement->lng)
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 font-medium mb-2">موقعیت جغرافیایی</label>
                            <p class="text-gray-400">عرض: {{ $advertisement->lat }}, طول: {{ $advertisement->lng }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Gallery -->
            @if($advertisement->image || $advertisement->galleries->count() > 0)
                <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                    <h2 class="text-yellow-primary font-bold text-lg mb-4">گالری تصاویر</h2>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <!-- Main Image -->
                        @if($advertisement->image)
                            <div class="relative group cursor-pointer" onclick="openLightbox('{{ asset($advertisement->image) }}')">
                                <img src="{{ asset($advertisement->image) }}" 
                                     alt="تصویر اصلی" 
                                     class="w-full h-32 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-search-plus text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                </div>
                                <span class="absolute top-2 right-2 bg-yellow-primary text-dark-primary text-xs px-2 py-1 rounded-full">اصلی</span>
                            </div>
                        @endif

                        <!-- Gallery Images -->
                        @foreach($advertisement->galleries as $gallery)
                            <div class="relative group cursor-pointer" onclick="openLightbox('{{ asset($gallery->url) }}')">
                                <img src="{{ asset($gallery->url) }}" 
                                     alt="تصویر گالری" 
                                     class="w-full h-32 object-cover rounded-lg">
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-search-plus text-white text-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Payments -->
            @if($advertisement->payments->count() > 0)
                <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                    <h2 class="text-yellow-primary font-bold text-lg mb-4">پرداخت‌ها</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-dark-tertiary">
                                <tr>
                                    <th class="px-4 py-3 text-right text-gray-300 font-medium">نوع</th>
                                    <th class="px-4 py-3 text-right text-gray-300 font-medium">مبلغ</th>
                                    <th class="px-4 py-3 text-right text-gray-300 font-medium">وضعیت</th>
                                    <th class="px-4 py-3 text-right text-gray-300 font-medium">تاریخ</th>
                                    <th class="px-4 py-3 text-right text-gray-300 font-medium">شماره پیگیری</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @foreach($advertisement->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-300">{{ $payment->payment_type_label }}</td>
                                        <td class="px-4 py-3 text-gray-300">{{ number_format($payment->amount) }} تومان</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusConfig = [
                                                    'pending' => ['label' => 'در انتظار', 'class' => 'bg-yellow-500'],
                                                    'paid' => ['label' => 'پرداخت شده', 'class' => 'bg-green-500'],
                                                    'failed' => ['label' => 'ناموفق', 'class' => 'bg-red-500'],
                                                ];
                                                $status = $statusConfig[$payment->status] ?? ['label' => 'نامشخص', 'class' => 'bg-gray-500'];
                                            @endphp
                                            <span class="px-3 py-1 rounded-full text-xs font-medium text-white {{ $status['class'] }}">
                                                {{ $status['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">
                                            <span class="jalali-date" data-date="{{ $payment->created_at?->toIso8601String() }}">{{ $payment->created_at?->format('Y-m-d') }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">{{ $payment->ref_id ?? 'ندارد' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">وضعیت آگهی</h3>
                
                @php
                    $statusConfig = [
                        0 => ['label' => 'غیرفعال', 'class' => 'bg-red-500'],
                        1 => ['label' => 'فعال', 'class' => 'bg-blue-500'],
                        2 => ['label' => 'تایید شده', 'class' => 'bg-green-500'],
                        3 => ['label' => 'در انتظار', 'class' => 'bg-yellow-500'],
                        4 => ['label' => 'منقضی شده', 'class' => 'bg-gray-500'],
                    ];
                    $status = $statusConfig[$advertisement->status] ?? ['label' => 'نامشخص', 'class' => 'bg-gray-500'];
                @endphp
                
                <div class="text-center mb-4">
                    <span class="px-4 py-2 rounded-full text-sm font-medium text-white {{ $status['class'] }}">
                        {{ $status['label'] }}
                    </span>
                </div>

                <!-- Quick Actions -->
                <div class="space-y-2">
                    @if($advertisement->status == 3)
                        <form method="POST" action="{{ route('admin.advertisements.approve', $advertisement) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full bg-green-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-green-600 transition-colors duration-200">
                                <i class="fas fa-check ml-2"></i>
                                تایید آگهی
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.advertisements.reject', $advertisement) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full bg-red-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-600 transition-colors duration-200">
                                <i class="fas fa-times ml-2"></i>
                                رد آگهی
                            </button>
                        </form>
                    @elseif(in_array($advertisement->status, [0, 2]))
                        <form method="POST" action="{{ route('admin.advertisements.toggle-status', $advertisement) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-600 transition-colors duration-200">
                                <i class="fas fa-{{ $advertisement->status == 2 ? 'pause' : 'play' }} ml-2"></i>
                                {{ $advertisement->status == 2 ? 'غیرفعال کردن' : 'فعال کردن' }}
                            </button>
                        </form>
                    @endif

                    @if($advertisement->status != 4)
                        <form method="POST" action="{{ route('admin.advertisements.set-expired', $advertisement) }}" class="w-full">
                            @csrf
                            @method('PATCH')
                            <button type="submit" 
                                    class="w-full bg-gray-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-gray-600 transition-colors duration-200">
                                <i class="fas fa-clock ml-2"></i>
                                منقضی کردن
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">اطلاعات کاربر</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-gray-300 font-medium mb-1">نام</label>
                        <p class="text-gray-400">{{ $advertisement->user->name ?? 'نامشخص' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-1">ایمیل</label>
                        <p class="text-gray-400">{{ $advertisement->user->email ?? 'نامشخص' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-1">تاریخ عضویت</label>
                        <p class="text-gray-400">
                            @if(isset($advertisement->user))
                                <span class="jalali-date" data-date="{{ $advertisement->user->created_at?->toIso8601String() }}">{{ $advertisement->user->created_at?->format('Y-m-d') }}</span>
                            @else
                                نامشخص
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Category & City -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">دسته‌بندی و مکان</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="block text-gray-300 font-medium mb-1">دسته‌بندی</label>
                        <p class="text-gray-400">{{ $advertisement->category->name ?? 'نامشخص' }}</p>
                    </div>
                    <div>
                        <label class="block text-gray-300 font-medium mb-1">شهر</label>
                        <p class="text-gray-400">{{ $advertisement->city->name ?? 'نامشخص' }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">آمار</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">بازدید:</span>
                        <span class="text-yellow-primary font-bold">{{ number_format($advertisement->view) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">تاریخ ایجاد:</span>
                        <span class="text-gray-400 jalali-date" data-date="{{ $advertisement->created_at?->toIso8601String() }}">{{ $advertisement->created_at?->format('Y-m-d') }}</span>
                    </div>
                    @if($advertisement->published_at)
                        <div class="flex justify-between">
                            <span class="text-gray-300">تاریخ انتشار:</span>
                            <span class="text-gray-400 jalali-date" data-date="{{ $advertisement->published_at?->toIso8601String() }}">{{ $advertisement->published_at?->format('Y-m-d') }}</span>
                        </div>
                    @endif
                    @if($advertisement->expired_at)
                        <div class="flex justify-between">
                            <span class="text-gray-300">تاریخ انقضا:</span>
                            <span class="text-gray-400 jalali-date" data-date="{{ $advertisement->expired_at?->toIso8601String() }}">{{ $advertisement->expired_at?->format('Y-m-d') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Promotion Status -->
            @if($advertisement->featuredAdvertisements->count() > 0)
                <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                    <h3 class="text-yellow-primary font-bold text-lg mb-4">وضعیت تبلیغ</h3>
                    
                    @foreach($advertisement->featuredAdvertisements as $featured)
                        <div class="mb-4 p-3 bg-dark-tertiary rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-300 font-medium">{{ $featured->type_label }}</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $featured->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $featured->is_active ? 'فعال' : 'غیرفعال' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-400">
                                <p>انقضا: <span class="jalali-date" data-date="{{ $featured->expires_at?->toIso8601String() }}">{{ $featured->expires_at?->format('Y-m-d') }}</span></p>
                                <p>باقی‌مانده: {{ $featured->remaining_days }} روز</p>
                            </div>
                        </div>
                    @endforeach

                    <a href="{{ route('admin.advertisements.promote-form', $advertisement) }}" 
                       class="w-full bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 text-center block">
                        <i class="fas fa-star ml-2"></i>
                        مدیریت تبلیغ
                    </a>
                </div>
            @else
                <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                    <h3 class="text-yellow-primary font-bold text-lg mb-4">تبلیغ</h3>
                    <p class="text-gray-400 text-sm mb-4">این آگهی تبلیغ ندارد</p>
                    <a href="{{ route('admin.advertisements.promote-form', $advertisement) }}" 
                       class="w-full bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 text-center block">
                        <i class="fas fa-star ml-2"></i>
                        تبلیغ کردن
                    </a>
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Lightbox Modal -->
<div id="lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button onclick="closeLightbox()" 
                class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300 transition-colors duration-200">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<script>
// Convert all .jalali-date elements to Persian calendar on the client
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

function openLightbox(imageSrc) {
    document.getElementById('lightbox-image').src = imageSrc;
    document.getElementById('lightbox').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close lightbox on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLightbox();
    }
});

// Close lightbox on background click
document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLightbox();
    }
});
</script>
@endsection
