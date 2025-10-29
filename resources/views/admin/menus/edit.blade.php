@extends('admin.layouts.master')

@section('title', 'ویرایش منو')

@section('content')
<!-- Edit Menu Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">ویرایش منو</h1>
            <p class="text-gray-400 text-sm lg:text-base">ویرایش اطلاعات منو: {{ $menu->title }}</p>
        </div>
        <a href="{{ route('admin.menus.index') }}" 
           class="text-gray-400 hover:text-yellow-primary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-arrow-right ml-2"></i>
            بازگشت به لیست منوها
        </a>
    </div>

    <!-- Form -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="lg:col-span-2">
                    <label for="title" class="block text-gray-300 font-medium mb-2">
                        عنوان منو <span class="text-red-400">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $menu->title) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="عنوان منو را وارد کنید"
                           required>
                    @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- URL -->
                <div>
                    <label for="url" class="block text-gray-300 font-medium mb-2">آدرس منو</label>
                    <input type="url" 
                           id="url" 
                           name="url" 
                           value="{{ old('url', $menu->url) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="https://example.com">
                    @error('url')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label for="position" class="block text-gray-300 font-medium mb-2">
                        موقعیت <span class="text-red-400">*</span>
                    </label>
                    <select id="position" 
                            name="position"
                            class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                            required>
                        <option value="">انتخاب موقعیت</option>
                        <option value="header" {{ old('position', $menu->position) == 'header' ? 'selected' : '' }}>هدر</option>
                        <option value="footer" {{ old('position', $menu->position) == 'footer' ? 'selected' : '' }}>فوتر</option>
                        <option value="sidebar" {{ old('position', $menu->position) == 'sidebar' ? 'selected' : '' }}>نوار کناری</option>
                        <option value="mobile" {{ old('position', $menu->position) == 'mobile' ? 'selected' : '' }}>موبایل</option>
                    </select>
                    @error('position')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Menu -->
                <div>
                    <label for="parent_id" class="block text-gray-300 font-medium mb-2">منوی والد</label>
                    <select id="parent_id" 
                            name="parent_id"
                            class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200">
                        <option value="">بدون والد (منوی اصلی)</option>
                        @foreach($parentMenus as $parentMenu)
                            <option value="{{ $parentMenu->id }}" {{ old('parent_id', $menu->parent_id) == $parentMenu->id ? 'selected' : '' }}>
                                {{ $parentMenu->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Icon -->
                @if($menu->icon)
                    <div class="lg:col-span-2">
                        <label class="block text-gray-300 font-medium mb-2">آیکون فعلی</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ asset($menu->icon) }}" alt="{{ $menu->title }}" 
                                 class="w-16 h-16 rounded-lg object-cover border border-gray-600">
                            <div>
                                <p class="text-gray-300 text-sm">{{ basename($menu->icon) }}</p>
                                <p class="text-gray-500 text-xs">برای تغییر آیکون، فایل جدید انتخاب کنید</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Icon Upload -->
                <div>
                    <label for="icon" class="block text-gray-300 font-medium mb-2">
                        {{ $menu->icon ? 'تغییر آیکون منو' : 'آیکون منو' }}
                    </label>
                    <div class="relative">
                        <input type="file" 
                               id="icon" 
                               name="icon" 
                               accept="image/*"
                               class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-primary file:text-dark-primary hover:file:bg-yellow-secondary">
                    </div>
                    <p class="text-gray-500 text-xs mt-1">فرمت‌های مجاز: JPG, PNG, GIF, SVG - حداکثر 2MB</p>
                    @error('icon')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="status" 
                               value="1" 
                               {{ old('status', $menu->status) ? 'checked' : '' }}
                               class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                        <span class="text-gray-300 font-medium mr-3">فعال</span>
                    </label>
                    <p class="text-gray-500 text-xs mt-1">منو در سایت نمایش داده شود</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-700">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    به‌روزرسانی منو
                </button>
                <a href="{{ route('admin.menus.index') }}" 
                   class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
