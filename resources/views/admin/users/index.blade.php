@extends('admin.layouts.master')

@section('title', 'مدیریت کاربران')

@section('content')
<!-- User Management Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مدیریت کاربران</h1>
            <p class="text-gray-400 text-sm lg:text-base">مدیریت و سازماندهی کاربران سایت</p>
        </div>
        <a href="{{ route('admin.users.create') }}" 
           class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-plus ml-2"></i>
            افزودن کاربر جدید
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <!-- Filters -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-4 mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="جستجو..."
                       class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
            </div>

            <!-- Status Filter -->
            <div>
                <select name="is_active" 
                        class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>فعال</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غیرفعال</option>
                </select>
            </div>

            <!-- Admin Filter -->
            <div>
                <select name="is_admin" 
                        class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                    <option value="">همه کاربران</option>
                    <option value="1" {{ request('is_admin') == '1' ? 'selected' : '' }}>ادمین</option>
                    <option value="0" {{ request('is_admin') == '0' ? 'selected' : '' }}>کاربر عادی</option>
                </select>
            </div>

            <!-- City Filter -->
            <div>
                <select name="city_id" 
                        class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                    <option value="">همه شهرها</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                            {{ $city->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-4 mt-4">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-search ml-2"></i>
                    جستجو
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">نام</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">موبایل</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">ایمیل</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">شهر</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">نوع</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">وضعیت</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50 transition-colors duration-200">
                            <td class="py-4 px-6">
                                <div class="text-gray-300 font-medium">{{ $user->name ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">{{ $user->mobile }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">{{ $user->email ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">{{ $user->city->name ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($user->is_admin)
                                    <span class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-full text-xs font-medium">
                                        ادمین
                                    </span>
                                @else
                                    <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded-full text-xs font-medium">
                                        کاربر عادی
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline toggle-form"
                                      data-message="آیا می‌خواهید وضعیت کاربر '{{ $user->name ?? $user->mobile }}' را تغییر دهید؟"
                                      data-title="تغییر وضعیت کاربر">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200
                                                   {{ $user->is_active ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' : 'bg-red-500/20 text-red-400 hover:bg-red-500/30' }}">
                                        {{ $user->is_active ? 'فعال' : 'غیرفعال' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                       class="text-blue-400 hover:text-blue-300 p-2 rounded-lg hover:bg-blue-500/20 transition-colors duration-200"
                                       title="مشاهده">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="text-yellow-primary hover:text-yellow-secondary p-2 rounded-lg hover:bg-yellow-primary/20 transition-colors duration-200"
                                       title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!$user->is_admin || ($user->is_admin && $user->id !== auth()->id()))
                                        <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline toggle-form"
                                              data-message="آیا می‌خواهید سطح دسترسی کاربر '{{ $user->name ?? $user->mobile }}' را تغییر دهید؟"
                                              data-title="تغییر سطح دسترسی">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-purple-400 hover:text-purple-300 p-2 rounded-lg hover:bg-purple-500/20 transition-colors duration-200"
                                                    title="{{ $user->is_admin ? 'حذف دسترسی ادمین' : 'افزودن دسترسی ادمین' }}">
                                                <i class="fas fa-{{ $user->is_admin ? 'user-shield' : 'user' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline delete-form"
                                          data-message="آیا از حذف کاربر '{{ $user->name ?? $user->mobile }}' اطمینان دارید؟ این عمل قابل بازگشت نیست."
                                          data-title="حذف کاربر">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300 p-2 rounded-lg hover:bg-red-500/20 transition-colors duration-200"
                                                title="حذف"
                                                {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fas fa-users text-4xl mb-4 block"></i>
                                <p>هیچ کاربری یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-6">
            {{ $users->links() }}
        </ emphasizing>
    @endif
</main>
@endsection

