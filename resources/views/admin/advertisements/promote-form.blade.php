@extends('admin.layouts.master')

@section('title', 'تبلیغ آگهی')

@section('content')
<!-- Promote Advertisement Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">تبلیغ آگهی</h1>
            <p class="text-gray-400 text-sm lg:text-base">{{ $advertisement->title }}</p>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.advertisements.show', $advertisement) }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-600 transition-colors duration-200">
                <i class="fas fa-eye ml-2"></i>
                مشاهده آگهی
            </a>
            <a href="{{ route('admin.advertisements.featured') }}" 
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
        <!-- Advertisement Preview -->
        <div class="lg:col-span-1">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 sticky top-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">پیش‌نمایش آگهی</h3>
                
                <!-- Image -->
                <div class="mb-4">
                    @if($advertisement->image)
                        <img src="{{ asset($advertisement->image) }}" 
                             alt="{{ $advertisement->title }}" 
                             class="w-full h-48 rounded-lg object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Title -->
                <h4 class="text-gray-300 font-bold text-lg mb-2">{{ $advertisement->title }}</h4>
                
                <!-- Description -->
                <p class="text-gray-400 text-sm mb-4 line-clamp-3">{{ $advertisement->description }}</p>

                <!-- Price -->
                <div class="mb-4">
                    @if($advertisement->price)
                        <span class="text-yellow-primary font-bold text-xl">{{ number_format($advertisement->price) }} تومان</span>
                    @else
                        <span class="text-gray-400">توافقی</span>
                    @endif
                </div>

                <!-- Category & City -->
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-300">دسته‌بندی:</span>
                        <span class="text-gray-400">{{ $advertisement->category->name ?? 'نامشخص' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">شهر:</span>
                        <span class="text-gray-400">{{ $advertisement->city->name ?? 'نامشخص' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">کاربر:</span>
                        <span class="text-gray-400">{{ $advertisement->user->name ?? 'نامشخص' }}</span>
                    </div>
                </div>

                <!-- Current Featured Status -->
                @if($currentFeatured)
                    <div class="mt-6 p-4 bg-yellow-500/20 rounded-lg border border-yellow-500/30">
                        <h5 class="text-yellow-primary font-bold mb-2">وضعیت تبلیغ فعلی</h5>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-300">نوع:</span>
                                <span class="text-gray-400">{{ $currentFeatured->type_label }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-300">وضعیت:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $currentFeatured->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $currentFeatured->is_active ? 'فعال' : 'غیرفعال' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-300">انقضا:</span>
                                <span class="text-gray-400">{{ $currentFeatured->expires_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-300">باقی‌مانده:</span>
                                <span class="text-gray-400">{{ $currentFeatured->remaining_days }} روز</span>
                            </div>
                        </div>

                        <!-- Extend Featured Duration -->
                        <div class="mt-4 pt-4 border-t border-yellow-500/30">
                            <form action="{{ route('admin.advertisements.extend-featured', $currentFeatured) }}" method="POST" class="flex items-end gap-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="additional_days" class="block text-gray-300 font-medium mb-2">افزودن روز</label>
                                    <input type="number" name="additional_days" id="additional_days" min="1" max="365" value="7"
                                           class="w-28 px-3 py-2 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                                </div>
                                <button type="submit" class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                                    <i class="fas fa-plus ml-2"></i>
                                    افزودن
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Promotion Form -->
        <div class="lg:col-span-2">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-6">تنظیمات تبلیغ</h3>
                
                <form action="{{ route('admin.advertisements.promote', $advertisement) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-6">
                        <!-- Type Selection -->
                        <div>
                            <label class="block text-gray-300 font-medium mb-4">نوع تبلیغ *</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Ladder Option -->
                                <div class="relative">
                                    <input type="radio" 
                                           id="type_ladder" 
                                           name="type" 
                                           value="ladder"
                                           {{ old('type') == 'ladder' ? 'checked' : '' }}
                                           class="sr-only">
                                    <label for="type_ladder" 
                                           class="block p-4 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 transition-colors duration-200 radio-label">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-sort-amount-up text-white"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-gray-300 font-bold">نردبان</h4>
                                                <p class="text-gray-400 text-sm">آگهی در بالای لیست نمایش داده می‌شود</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                                <!-- Special Option -->
                                <div class="relative">
                                    <input type="radio" 
                                           id="type_special" 
                                           name="type" 
                                           value="special"
                                           {{ old('type') == 'special' ? 'checked' : '' }}
                                           class="sr-only">
                                    <label for="type_special" 
                                           class="block p-4 border-2 border-gray-600 rounded-lg cursor-pointer hover:border-yellow-500 transition-colors duration-200 radio-label">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                                <i class="fas fa-crown text-white"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-gray-300 font-bold">ویژه</h4>
                                                <p class="text-gray-400 text-sm">آگهی با برچسب ویژه نمایش داده می‌شود</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            @error('type')
                                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label for="duration_days" class="block text-gray-300 font-medium mb-2">مدت تبلیغ (روز) *</label>
                            <div class="flex gap-4">
                                <input type="number" 
                                       id="duration_days" 
                                       name="duration_days" 
                                       value="{{ old('duration_days', 7) }}"
                                       min="1" 
                                       max="365"
                                       class="flex-1 px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('duration_days') border-red-500 @enderror">
                                <div class="flex flex-col gap-2">
                                    <button type="button" 
                                            onclick="setDuration(7)"
                                            class="px-3 py-2 bg-gray-600 text-gray-300 rounded-lg text-sm hover:bg-gray-500 transition-colors duration-200">
                                        7 روز
                                    </button>
                                    <button type="button" 
                                            onclick="setDuration(30)"
                                            class="px-3 py-2 bg-gray-600 text-gray-300 rounded-lg text-sm hover:bg-gray-500 transition-colors duration-200">
                                        30 روز
                                    </button>
                                    <button type="button" 
                                            onclick="setDuration(90)"
                                            class="px-3 py-2 bg-gray-600 text-gray-300 rounded-lg text-sm hover:bg-gray-500 transition-colors duration-200">
                                        90 روز
                                    </button>
                                </div>
                            </div>
                            @error('duration_days')
                                <p class="text-red-400 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expires At Preview -->
                        <div class="p-4 bg-dark-tertiary rounded-lg">
                            <h4 class="text-gray-300 font-medium mb-2">تاریخ انقضا</h4>
                            <p class="text-gray-400" id="expires-preview">
                                {{ now()->addDays(7)->format('Y/m/d H:i') }}
                            </p>
                        </div>

                        <!-- Warning -->
                        <div class="p-4 bg-yellow-500/20 rounded-lg border border-yellow-500/30">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                                <div>
                                    <h4 class="text-yellow-primary font-bold mb-1">توجه</h4>
                                    <p class="text-gray-300 text-sm">
                                        این تبلیغ به صورت دستی توسط ادمین ایجاد می‌شود و نیازی به پرداخت ندارد. 
                                        در صورت وجود تبلیغ فعال از همان نوع، تبلیغ جدید ایجاد نخواهد شد.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex gap-4 mt-8">
                        <button type="submit" 
                                class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                            <i class="fas fa-star ml-2"></i>
                            ایجاد تبلیغ
                        </button>
                        <a href="{{ route('admin.advertisements.show', $advertisement) }}" 
                           class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                            <i class="fas fa-times ml-2"></i>
                            انصراف
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<style>
/* Keep the chosen option visually selected */
input[type="radio"]:checked + label {
    border-color: #fbbf24 !important;
    background-color: rgba(251, 191, 36, 0.1) !important;
}
</style>

<script>
// Set duration function
function setDuration(days) {
    document.getElementById('duration_days').value = days;
    updateExpiresPreview();
}

// Update expires preview
function updateExpiresPreview() {
    const days = document.getElementById('duration_days').value;
    const expiresDate = new Date();
    expiresDate.setDate(expiresDate.getDate() + parseInt(days));
    
    const formattedDate = expiresDate.toLocaleDateString('fa-IR') + ' ' + 
                         expiresDate.toLocaleTimeString('fa-IR', {hour: '2-digit', minute: '2-digit'});
    
    document.getElementById('expires-preview').textContent = formattedDate;
}

// No JS needed for selection highlight; handled by CSS (input:checked + label)

// Duration input change
document.getElementById('duration_days').addEventListener('input', updateExpiresPreview);

// Initialize
updateExpiresPreview();
</script>
@endsection
