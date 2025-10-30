@extends('admin.layouts.master')

@section('title', 'تنظیمات عمومی')

@section('content')
<!-- General Settings Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">تنظیمات عمومی</h1>
            <p class="text-gray-400 text-sm lg:text-base">مدیریت تنظیمات کلی سایت</p>
        </div>
        <button id="toggleFormBtn" 
                class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-edit ml-2"></i>
            <span id="toggleText">{{ $setting->id ? 'ویرایش تنظیمات' : 'ایجاد تنظیمات' }}</span>
        </button>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <!-- Current Settings Table -->
    @if($setting->id)
        <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
            <h3 class="text-yellow-primary font-bold text-lg mb-4">تنظیمات فعلی</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Site Title -->
                <div class="bg-dark-tertiary rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-tag text-yellow-primary"></i>
                        <span class="text-gray-300 font-medium">عنوان سایت</span>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $setting->title ?? 'تعریف نشده' }}</p>
                </div>

                <!-- Email -->
                <div class="bg-dark-tertiary rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-envelope text-yellow-primary"></i>
                        <span class="text-gray-300 font-medium">ایمیل</span>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $setting->email ?? 'تعریف نشده' }}</p>
                </div>

                <!-- Phone -->
                <div class="bg-dark-tertiary rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-phone text-yellow-primary"></i>
                        <span class="text-gray-300 font-medium">تلفن</span>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $setting->phone ?? 'تعریف نشده' }}</p>
                </div>

                <!-- Description -->
                <div class="bg-dark-tertiary rounded-lg p-4 lg:col-span-2">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-info-circle text-yellow-primary"></i>
                        <span class="text-gray-300 font-medium">توضیحات</span>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $setting->description ?? 'تعریف نشده' }}</p>
                </div>

                <!-- Keywords -->
                <div class="bg-dark-tertiary rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-key text-yellow-primary"></i>
                        <span class="text-gray-300 font-medium">کلمات کلیدی</span>
                    </div>
                    <p class="text-gray-400 text-sm">{{ $setting->keywords ?? 'تعریف نشده' }}</p>
                </div>

                <!-- Logo -->
                <div class="bg-dark-tertiary rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-image text-yellow-primary"></i>
                        <span class="text-gray-300 font-medium">لوگو</span>
                    </div>
                    @if($setting->logo)
                        <img src="{{ asset($setting->logo) }}" alt="Logo" class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <p class="text-gray-400 text-sm">تعریف نشده</p>
                    @endif
                </div>

                <!-- Favicon -->
                <div class="bg-dark-tertiary rounded-lg p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-star text-yellow-primary"></i>
                        <span class="text-gray-300 font-medium">فاویکون</span>
                    </div>
                    @if($setting->favicon)
                        <img src="{{ asset($setting->favicon) }}" alt="Favicon" class="w-8 h-8 rounded object-cover">
                    @else
                        <p class="text-gray-400 text-sm">تعریف نشده</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Settings Form (Hidden by default) -->
    <div id="settingsForm" class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 {{ $setting->id ? 'hidden' : '' }}">
        <h3 class="text-yellow-primary font-bold text-lg mb-6">فرم تنظیمات</h3>
        <form action="{{ route('admin.settings.update-general') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Site Title -->
                <div>
                    <label for="title" class="block text-gray-300 font-medium mb-2">عنوان سایت</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $setting->title) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="عنوان سایت را وارد کنید">
                    @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-300 font-medium mb-2">ایمیل</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $setting->email) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="admin@example.com">
                    @error('email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-gray-300 font-medium mb-2">تلفن</label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $setting->phone) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="09123456789">
                    @error('phone')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Keywords -->
                <div>
                    <label for="keywords" class="block text-gray-300 font-medium mb-2">کلمات کلیدی</label>
                    <input type="text" 
                           id="keywords" 
                           name="keywords" 
                           value="{{ old('keywords', $setting->keywords) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="کلمات کلیدی را با کاما جدا کنید">
                    @error('keywords')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-gray-300 font-medium mb-2">توضیحات سایت</label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                              placeholder="توضیحات کوتاه درباره سایت">{{ old('description', $setting->description) }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Logo -->
                @if($setting->logo)
                    <div class="lg:col-span-2">
                        <label class="block text-gray-300 font-medium mb-2">لوگوی فعلی</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset($setting->logo) }}" alt="Logo" 
                                 class="w-20 h-20 rounded-lg object-cover border border-gray-600">
                            <div>
                                <p class="text-gray-300 text-sm">{{ basename($setting->logo) }}</p>
                                <p class="text-gray-500 text-xs">برای تغییر لوگو، فایل جدید انتخاب کنید</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Logo Upload -->
                <div>
                    <label for="logo" class="block text-gray-300 font-medium mb-2">
                        {{ $setting->logo ? 'تغییر لوگو' : 'لوگو سایت' }}
                    </label>
                    <div class="relative">
                        <input type="file" 
                               id="logo" 
                               name="logo" 
                               accept="image/*"
                               class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-primary file:text-dark-primary hover:file:bg-yellow-secondary">
                    </div>
                    <p class="text-gray-500 text-xs mt-1">فرمت‌های مجاز: JPG, PNG, GIF, SVG - حداکثر 2MB</p>
                    @error('logo')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Favicon -->
                @if($setting->favicon)
                    <div>
                        <label class="block text-gray-300 font-medium mb-2">فاویکون فعلی</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset($setting->favicon) }}" alt="Favicon" 
                                 class="w-8 h-8 rounded object-cover border border-gray-600">
                            <div>
                                <p class="text-gray-300 text-sm">{{ basename($setting->favicon) }}</p>
                                <p class="text-gray-500 text-xs">برای تغییر فاویکون، فایل جدید انتخاب کنید</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Favicon Upload -->
                <div>
                    <label for="favicon" class="block text-gray-300 font-medium mb-2">
                        {{ $setting->favicon ? 'تغییر فاویکون' : 'فاویکون سایت' }}
                    </label>
                    <div class="relative">
                        <input type="file" 
                               id="favicon" 
                               name="favicon" 
                               accept="image/*"
                               class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-primary file:text-dark-primary hover:file:bg-yellow-secondary">
                    </div>
                    <p class="text-gray-500 text-xs mt-1">فرمت‌های مجاز: JPG, PNG, GIF, SVG - حداکثر 1MB</p>
                    @error('favicon')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-700">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    ذخیره تنظیمات
                </button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggleFormBtn');
    const form = document.getElementById('settingsForm');
    const toggleText = document.getElementById('toggleText');
    
    if (toggleBtn && form) {
        toggleBtn.addEventListener('click', function() {
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                toggleText.textContent = 'بستن فرم';
                toggleBtn.innerHTML = '<i class="fas fa-times ml-2"></i>بستن فرم';
            } else {
                form.classList.add('hidden');
                toggleText.textContent = '{{ $setting->id ? "ویرایش تنظیمات" : "ایجاد تنظیمات" }}';
                toggleBtn.innerHTML = '<i class="fas fa-edit ml-2"></i>{{ $setting->id ? "ویرایش تنظیمات" : "ایجاد تنظیمات" }}';
            }
        });
    }
});
</script>
@endsection
