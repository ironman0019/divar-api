@extends('admin.layouts.master')

@section('title', 'مشاهده کاربر')

@section('content')
<!-- User Details Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مشاهده کاربر</h1>
            <p class="text-gray-400 text-sm lg:text-base">اطلاعات کامل کاربر</p>
        </div>
        <div class="flex items-center gap-4 mt-4 sm:mt-0">
            <a href="{{ route('admin.users.edit', $user) }}" 
               class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                <i class="fas fa-edit ml-2"></i>
                ویرایش
            </a>
            <a href="{{ route('admin.users.index') }}" 
               class="text-gray-400 hover:text-yellow-primary transition-colors duration-200">
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

    <!-- User Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-6">اطلاعات کلی</h3>
                
                <div class="space-y-4">
                    <!-- Name -->
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-gray-700 pb-4">
                        <span class="text-gray-400 font-medium sm:w-32 mb-2 sm:mb-0">نام:</span>
                        <span class="text-gray-300">{{ $user->name ?? '-' }}</span>
                    </div>

                    <!-- Mobile -->
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-gray-700 pb-4">
                        <span class="text-gray-400 font-medium sm:w-32 mb-2 sm:mb-0">شماره موبایل:</span>
                        <span class="text-gray-300">{{ $user->mobile }}</span>
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-gray-700 pb-4">
                        <span class="text-gray-400 font-medium sm:w-32 mb-2 sm:mb-0">ایمیل:</span>
                        <span class="text-gray-300">{{ $user->email ?? '-' }}</span>
                    </div>

                    <!-- City -->
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-gray-700 pb-4">
                        <span class="text-gray-400 font-medium sm:w-32 mb-2 sm:mb-0">شهر:</span>
                        <span class="text-gray-300">{{ $user->city->name ?? '-' }}</span>
                    </div>

                    <!-- Created At -->
                    <div class="flex flex-col sm:flex-row sm:items-center border-b border-gray-700 pb-4">
                        <span class="text-gray-400 font-medium sm:w-32 mb-2 sm:mb-0">تاریخ ثبت:</span>
                        <span class="text-gray-300">{{ $user->created_at->format('Y/m/d H:i') }}</span>
                    </div>

                    <!-- Updated At -->
                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <span class="text-gray-400 font-medium sm:w-32 mb-2 sm:mb-0">آخرین به‌روزرسانی:</span>
                        <span class="text-gray-300">{{ $user->updated_at->format('Y/m/d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Actions -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-6">وضعیت</h3>
                
                <div class="space-y-4">
                    <!-- Active Status -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 font-medium">وضعیت کاربر:</span>
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline toggle-form"
                                  data-message="آیا می‌خواهید وضعیت کاربر را تغییر دهید؟"
                                  data-title="تغییر وضعیت">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200
                                               {{ $user->is_active ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' : 'bg-red-500/20 text-red-400 hover:bg-red-500/30' }}">
                                    {{ $user->is_active ? 'فعال' : 'غیرفعال' }}
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Admin Status -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-400 font-medium">نوع کاربر:</span>
                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline toggle-form"
                                      data-message="آیا می‌خواهید سطح دسترسی کاربر را تغییر دهید؟"
                                      data-title="تغییر سطح دسترسی">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200
                                                   {{ $user->is_admin ? 'bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500/30' : 'bg-blue-500/20 text-blue-400 hover:bg-blue-500/30' }}">
                                        {{ $user->is_admin ? 'ادمین' : 'کاربر عادی' }}
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-400">
                                    ادمین
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-6">عملیات</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}" 
                       class="w-full bg-yellow-primary text-dark-primary px-4 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-edit ml-2"></i>
                        ویرایش کاربر
                    </a>
                    
                    @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="delete-form"
                              data-message="آیا از حذف کاربر '{{ $user->name ?? $user->mobile }}' اطمینان دارید؟ این عمل قابل بازگشت نیست."
                              data-title="حذف کاربر">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-red-700 transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-trash ml-2"></i>
                                حذف کاربر
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

