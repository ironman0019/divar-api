@extends('admin.layouts.master')

@section('title', 'افزودن کاربر جدید')

@section('content')
<!-- Create User Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">افزودن کاربر جدید</h1>
            <p class="text-gray-400 text-sm lg:text-base">ایجاد کاربر جدید برای سایت</p>
        </div>
        <a href="{{ route('admin.users.index') }}" 
           class="text-gray-400 hover:text-yellow-primary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-arrow-right ml-2"></i>
            بازگشت به لیست کاربران
        </a>
    </div>

    <!-- Form -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-gray-300 font-medium mb-2">نام</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="نام کاربر را وارد کنید">
                    @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mobile -->
                <div>
                    <label for="mobile" class="block text-gray-300 font-medium mb-2">
                        شماره موبایل <span class="text-red-400">*</span>
                    </label>
                    <input type="text" 
                           id="mobile" 
                           name="mobile" 
                           value="{{ old('mobile') }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="09123456789"
                           required>
                    @error('mobile')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-300 font-medium mb-2">ایمیل</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="email@example.com">
                    @error('email')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city_id" class="block text-gray-300 font-medium mb-2">شهر</label>
                    <select id="city_id" 
                            name="city_id"
                            class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200">
                        <option value="">انتخاب شهر</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-gray-300 font-medium mb-2">
                        رمز عبور <span class="text-red-400">*</span>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="رمز عبور"
                           required>
                    @error('password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-gray-300 font-medium mb-2">
                        تکرار رمز عبور <span class="text-red-400">*</span>
                    </label>
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="تکرار رمز عبور"
                           required>
                </div>

                <!-- Is Active -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', false) ? 'checked' : '' }}
                               class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                        <span class="text-gray-300 font-medium mr-3">فعال</span>
                    </label>
                    <p class="text-gray-500 text-xs mt-1">کاربر می‌تواند وارد سیستم شود</p>
                </div>

                <!-- Is Admin -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_admin" 
                               value="1" 
                               {{ old('is_admin', false) ? 'checked' : '' }}
                               class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                        <span class="text-gray-300 font-medium mr-3">ادمین</span>
                    </label>
                    <p class="text-gray-500 text-xs mt-1">کاربر دسترسی ادمین داشته باشد</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-700">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    ایجاد کاربر
                </button>
                <a href="{{ route('admin.users.index') }}" 
                   class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</main>
@endsection

