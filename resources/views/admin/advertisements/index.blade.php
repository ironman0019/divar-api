@extends('admin.layouts.master')

@section('title', 'مدیریت آگهی‌ها')

@section('content')
<!-- Advertisements Management Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مدیریت آگهی‌ها</h1>
            <p class="text-gray-400 text-sm lg:text-base">جستجو، فیلتر و مدیریت آگهی‌ها</p>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <!-- Search and Filters -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6 mb-6">
        <form method="GET" class="space-y-4">
            <!-- Search Bar -->
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-gray-300 font-medium mb-2">جستجو</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="جستجو در عنوان و توضیحات..."
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                </div>
                
                <!-- Status Filter -->
                <div class="lg:w-48">
                    <label for="status" class="block text-gray-300 font-medium mb-2">وضعیت</label>
                    <select name="status" id="status" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="">همه وضعیت‌ها</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غیرفعال</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>فعال</option>
                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>تایید شده</option>
                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>در انتظار</option>
                        <option value="4" {{ request('status') == '4' ? 'selected' : '' }}>منقضی شده</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div class="lg:w-48">
                    <label for="category_id" class="block text-gray-300 font-medium mb-2">دسته‌بندی</label>
                    <select name="category_id" id="category_id" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="">همه دسته‌ها</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- City Filter -->
                <div class="lg:w-48">
                    <label for="city_id" class="block text-gray-300 font-medium mb-2">شهر</label>
                    <select name="city_id" id="city_id" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="">همه شهرها</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Additional Filters -->
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Ads Type Filter -->
                <div class="lg:w-48">
                    <label for="ads_type" class="block text-gray-300 font-medium mb-2">نوع آگهی</label>
                    <input type="text" 
                           name="ads_type" 
                           id="ads_type"
                           value="{{ request('ads_type') }}"
                           placeholder="نوع آگهی..."
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                </div>

                <!-- Ads Status Filter -->
                <div class="lg:w-48">
                    <label for="ads_status" class="block text-gray-300 font-medium mb-2">وضعیت آگهی</label>
                    <input type="text" 
                           name="ads_status" 
                           id="ads_status"
                           value="{{ request('ads_status') }}"
                           placeholder="وضعیت آگهی..."
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                </div>

                <!-- Sort -->
                <div class="lg:w-48">
                    <label for="sort" class="block text-gray-300 font-medium mb-2">مرتب‌سازی</label>
                    <select name="sort" id="sort" class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>جدیدترین</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>قیمت</option>
                        <option value="view" {{ request('sort') == 'view' ? 'selected' : '' }}>بازدید</option>
                        <option value="published_at" {{ request('sort') == 'published_at' ? 'selected' : '' }}>تاریخ انتشار</option>
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

            <!-- Filter Buttons -->
            <div class="flex gap-3">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-search ml-2"></i>
                    اعمال فیلتر
                </button>
                <a href="{{ route('admin.advertisements.index') }}" 
                   class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    پاک کردن
                </a>
            </div>
        </form>
    </div>

    <!-- Advertisements Table -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1200px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium w-20">تصویر</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[200px]">عنوان</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[150px]">کاربر</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[120px]">دسته‌بندی</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[100px]">شهر</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[120px]">قیمت</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[100px]">وضعیت</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[80px]">بازدید</th>
                        <th class="px-3 py-4 text-right text-gray-300 font-medium min-w-[140px]">عملیات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($advertisements as $advertisement)
                        <tr class="hover:bg-dark-tertiary/50 transition-colors duration-200">
                            <!-- Image -->
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-2">
                                    @if($advertisement->image)
                                        <img src="{{ asset($advertisement->image) }}" 
                                             alt="{{ $advertisement->title }}" 
                                             class="w-10 h-10 rounded-lg object-cover">
                                    @else
                                        <div class="w-10 h-10 bg-gray-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    @if($advertisement->galleries_count > 0)
                                        <span class="bg-yellow-primary text-dark-primary text-xs px-1.5 py-0.5 rounded-full">
                                            +{{ $advertisement->galleries_count }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Title -->
                            <td class="px-3 py-4">
                                <div class="min-w-[200px]">
                                    <h3 class="text-gray-300 font-medium text-sm leading-tight mb-1">{{ Str::limit($advertisement->title, 40) }}</h3>
                                    <p class="text-gray-400 text-xs leading-tight">{{ Str::limit($advertisement->description, 50) }}</p>
                                </div>
                            </td>

                            <!-- User -->
                            <td class="px-3 py-4">
                                <div class="text-gray-300 min-w-[150px]">
                                    <p class="font-medium text-sm">{{ Str::limit($advertisement->user->name ?? 'نامشخص', 15) }}</p>
                                    <p class="text-xs text-gray-400">{{ Str::limit($advertisement->user->mobile ?? '', 20) }}</p>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-3 py-4">
                                <span class="text-gray-300 text-sm min-w-[120px] block">{{ Str::limit($advertisement->category->name ?? 'نامشخص', 15) }}</span>
                            </td>

                            <!-- City -->
                            <td class="px-3 py-4">
                                <span class="text-gray-300 text-sm min-w-[100px] block">{{ Str::limit($advertisement->city->name ?? 'نامشخص', 12) }}</span>
                            </td>

                            <!-- Price -->
                            <td class="px-3 py-4">
                                <div class="min-w-[120px]">
                                    @if($advertisement->price)
                                        <span class="text-yellow-primary font-medium text-sm">{{ number_format($advertisement->price) }} تومان</span>
                                    @else
                                        <span class="text-gray-400 text-sm">توافقی</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-3 py-4">
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
                                <span class="px-2 py-1 rounded-full text-xs font-medium text-white {{ $status['class'] }} whitespace-nowrap">
                                    {{ $status['label'] }}
                                </span>
                            </td>

                            <!-- Views -->
                            <td class="px-3 py-4">
                                <span class="text-gray-300 text-sm min-w-[80px] block">{{ number_format($advertisement->view) }}</span>
                            </td>

                            <!-- Actions -->
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-1.5 min-w-[140px]">
                                    <!-- View -->
                                    <a href="{{ route('admin.advertisements.show', $advertisement) }}" 
                                       class="text-blue-400 hover:text-blue-300 transition-colors duration-200 p-1.5 rounded hover:bg-blue-500/20"
                                       title="مشاهده">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.advertisements.edit', $advertisement) }}" 
                                       class="text-yellow-400 hover:text-yellow-300 transition-colors duration-200 p-1.5 rounded hover:bg-yellow-500/20"
                                       title="ویرایش">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>

                                    <!-- Toggle Status -->
                                    @if(in_array($advertisement->status, [0, 2]))
                                        <form method="POST" action="{{ route('admin.advertisements.toggle-status', $advertisement) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" 
                                                    class="text-green-400 hover:text-green-300 transition-colors duration-200 p-1.5 rounded hover:bg-green-500/20"
                                                    title="{{ $advertisement->status == 2 ? 'غیرفعال کردن' : 'فعال کردن' }}">
                                                <i class="fas fa-{{ $advertisement->status == 2 ? 'pause' : 'play' }} text-sm"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Delete -->
                                    <form method="POST" 
                                          action="{{ route('admin.advertisements.destroy', $advertisement) }}" 
                                          class="inline delete-form"
                                          data-title="حذف آگهی"
                                          data-message="آیا از حذف این آگهی اطمینان دارید؟">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300 transition-colors duration-200 p-1.5 rounded hover:bg-red-500/20"
                                                title="حذف">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-search text-4xl mb-4"></i>
                                <p>هیچ آگهی‌ای یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($advertisements->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $advertisements->links() }}
            </div>
        @endif
    </div>
</main>
@endsection
