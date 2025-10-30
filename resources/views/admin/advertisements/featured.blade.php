@extends('admin.layouts.master')

@section('title', 'آگهی‌های ویژه')

@section('content')
<!-- Featured Advertisements Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">آگهی‌های ویژه</h1>
            <p class="text-gray-400 text-sm lg:text-base">مدیریت آگهی‌های تبلیغ شده</p>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.advertisements.index') }}" 
               class="bg-gray-600 text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                همه آگهی‌ها
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

    <!-- Filter Tabs -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.advertisements.featured') }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ !request('type') ? 'bg-yellow-primary text-dark-primary' : 'bg-dark-tertiary text-gray-300 hover:bg-gray-600' }}">
                <i class="fas fa-star ml-2"></i>
                همه تبلیغات
            </a>
            <a href="{{ route('admin.advertisements.featured', ['type' => 'ladder']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ request('type') == 'ladder' ? 'bg-yellow-primary text-dark-primary' : 'bg-dark-tertiary text-gray-300 hover:bg-gray-600' }}">
                <i class="fas fa-sort-amount-up ml-2"></i>
                نردبان
            </a>
            <a href="{{ route('admin.advertisements.featured', ['type' => 'special']) }}" 
               class="px-4 py-2 rounded-lg font-medium transition-colors duration-200 {{ request('type') == 'special' ? 'bg-yellow-primary text-dark-primary' : 'bg-dark-tertiary text-gray-300 hover:bg-gray-600' }}">
                <i class="fas fa-crown ml-2"></i>
                ویژه
            </a>
        </div>
    </div>

    <!-- Search and Sort -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <form method="GET" class="space-y-4">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label for="search" class="block text-gray-300 font-medium mb-2">جستجو</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="جستجو در عنوان آگهی..."
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                </div>
                
                <!-- Sort -->
                <div class="lg:w-48">
                    <label for="sort" class="block text-gray-300 font-medium mb-2">مرتب‌سازی</label>
                    <select name="sort" id="sort" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>جدیدترین</option>
                        <option value="expires_at" {{ request('sort') == 'expires_at' ? 'selected' : '' }}>زمان انقضا</option>
                        <option value="type" {{ request('sort') == 'type' ? 'selected' : '' }}>نوع تبلیغ</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div class="lg:w-32">
                    <label for="direction" class="block text-gray-300 font-medium mb-2">ترتیب</label>
                    <select name="direction" id="direction" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>نزولی</option>
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>صعودی</option>
                    </select>
                </div>
            </div>

            <!-- Hidden type field to maintain filter -->
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif

            <!-- Filter Buttons -->
            <div class="flex gap-3">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-search ml-2"></i>
                    اعمال فیلتر
                </button>
                <a href="{{ route('admin.advertisements.featured') }}" 
                   class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- Featured Advertisements Table -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">آگهی</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">نوع تبلیغ</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">تاریخ شروع</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">تاریخ انقضا</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">باقی‌مانده</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">وضعیت</th>
                        <th class="px-6 py-4 text-right text-gray-300 font-medium">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($featuredAdvertisements as $featured)
                        <tr class="hover:bg-dark-tertiary/50 transition-colors duration-200">
                            <!-- Advertisement -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($featured->advertisement->image)
                                        <img src="{{ asset($featured->advertisement->image) }}" 
                                             alt="{{ $featured->advertisement->title }}" 
                                             class="w-12 h-12 rounded-lg object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gray-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-gray-300 font-medium">{{ $featured->advertisement->title }}</h3>
                                        <p class="text-gray-400 text-sm">{{ $featured->advertisement->user->name ?? 'نامشخص' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Type -->
                            <td class="px-6 py-4">
                                @if($featured->type === 'ladder')
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500 text-white">
                                        <i class="fas fa-sort-amount-up ml-1"></i>
                                        نردبان
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-500 text-white">
                                        <i class="fas fa-crown ml-1"></i>
                                        ویژه
                                    </span>
                                @endif
                            </td>

                            <!-- Start Date -->
                            <td class="px-6 py-4">
                                <span class="text-gray-300 jalali-date" data-date="{{ $featured->created_at?->toIso8601String() }}">{{ $featured->created_at?->format('Y-m-d') }}</span>
                            </td>

                            <!-- Expires At -->
                            <td class="px-6 py-4">
                                <span class="text-gray-300 jalali-date" data-date="{{ $featured->expires_at?->toIso8601String() }}">{{ $featured->expires_at?->format('Y-m-d') }}</span>
                            </td>

                            <!-- Remaining Days -->
                            <td class="px-6 py-4">
                                @if($featured->remaining_days > 0)
                                    <span class="text-green-400 font-medium">{{ $featured->remaining_days }} روز</span>
                                @else
                                    <span class="text-red-400 font-medium">منقضی شده</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($featured->is_active)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                        <i class="fas fa-check ml-1"></i>
                                        فعال
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-500 text-white">
                                        <i class="fas fa-pause ml-1"></i>
                                        غیرفعال
                                    </span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <!-- View Advertisement -->
                                    <a href="{{ route('admin.advertisements.show', $featured->advertisement) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors duration-200"
                                       title="مشاهده آگهی">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Toggle Status -->
                                    <form method="POST" 
                                          action="{{ route('admin.advertisements.toggle-featured', $featured) }}" 
                                          class="inline toggle-form"
                                          data-title="{{ $featured->is_active ? 'غیرفعال کردن تبلیغ' : 'فعال کردن تبلیغ' }}"
                                          data-message="آیا از تغییر وضعیت این تبلیغ اطمینان دارید؟">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="text-{{ $featured->is_active ? 'red' : 'green' }}-400 hover:text-{{ $featured->is_active ? 'red' : 'green' }}-300 transition-colors duration-200"
                                                title="{{ $featured->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                                            <i class="fas fa-{{ $featured->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>

                                    <!-- Remove Featured -->
                                    <form method="POST" 
                                          action="{{ route('admin.advertisements.remove-featured', $featured) }}" 
                                          class="inline delete-form"
                                          data-title="حذف تبلیغ"
                                          data-message="آیا از حذف این تبلیغ اطمینان دارید؟">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300 transition-colors duration-200"
                                                title="حذف تبلیغ">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-star text-4xl mb-4"></i>
                                <p>هیچ آگهی تبلیغ شده‌ای یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($featuredAdvertisements->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $featuredAdvertisements->links() }}
            </div>
        @endif
    </div>
</main>
<script>
// Shared, minimal client-side Persian date formatter
if (!window.__applyJalaliDates) {
    window.__applyJalaliDates = function () {
        try {
            var nodes = document.querySelectorAll('.jalali-date[data-date]');
            nodes.forEach(function (el) {
                var iso = el.getAttribute('data-date');
                if (!iso) return;
                var d = new Date(iso);
                if (isNaN(d.getTime())) return;
                el.textContent = d.toLocaleDateString('fa-IR');
            });
        } catch (e) {}
    };
    document.addEventListener('DOMContentLoaded', window.__applyJalaliDates);
}
</script>
@endsection
