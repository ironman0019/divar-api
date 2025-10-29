@extends('admin.layouts.master')

@section('title', 'مشاهده منو')

@section('content')
<!-- Show Menu Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مشاهده منو</h1>
            <p class="text-gray-400 text-sm lg:text-base">جزئیات منو: {{ $menu->title }}</p>
        </div>
        <div class="flex items-center gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.menus.edit', $menu) }}" 
               class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                <i class="fas fa-edit ml-2"></i>
                ویرایش
            </a>
            <a href="{{ route('admin.menus.index') }}" 
               class="text-gray-400 hover:text-yellow-primary transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                بازگشت به لیست
            </a>
        </div>
    </div>

    <!-- Menu Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-6">اطلاعات کلی</h3>
                
                <div class="space-y-4">
                    <!-- Title -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">عنوان:</div>
                        <div class="text-gray-300">{{ $menu->title }}</div>
                    </div>

                    <!-- Slug -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">اسلاگ:</div>
                        <div class="text-gray-300 font-mono text-sm bg-dark-tertiary px-2 py-1 rounded">
                            {{ $menu->slug }}
                        </div>
                    </div>

                    <!-- URL -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">آدرس:</div>
                        <div class="text-gray-300">
                            @if($menu->url)
                                <a href="{{ $menu->url }}" target="_blank" 
                                   class="text-yellow-primary hover:text-yellow-secondary">
                                    {{ $menu->url }}
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>

                    <!-- Position -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">موقعیت:</div>
                        <div>
                            <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-sm">
                                {{ $menu->position }}
                            </span>
                        </div>
                    </div>

                    <!-- Parent -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">والد:</div>
                        <div class="text-gray-300">
                            @if($menu->parent)
                                <a href="{{ route('admin.menus.show', $menu->parent) }}" 
                                   class="text-yellow-primary hover:text-yellow-secondary">
                                    {{ $menu->parent->title }}
                                </a>
                            @else
                                <span class="text-gray-500">منوی اصلی</span>
                            @endif
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">وضعیت:</div>
                        <div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                       {{ $menu->status ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $menu->status ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">تاریخ ایجاد:</div>
                        <div class="text-gray-300">{{ $menu->created_at->format('Y/m/d H:i') }}</div>
                    </div>

                    <!-- Updated At -->
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-gray-400 font-medium">آخرین به‌روزرسانی:</div>
                        <div class="text-gray-300">{{ $menu->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Icon & Actions -->
        <div class="space-y-6">
            <!-- Icon -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">آیکون منو</h3>
                
                @if($menu->icon)
                    <div class="text-center">
                        <img src="{{ asset($menu->icon) }}" 
                             alt="{{ $menu->title }}" 
                             class="w-24 h-24 rounded-lg object-cover mx-auto border border-gray-600">
                        <p class="text-gray-400 text-sm mt-2">{{ basename($menu->icon) }}</p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-image text-gray-600 text-4xl mb-2"></i>
                        <p class="text-gray-500">آیکون تعریف نشده</p>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">عملیات سریع</h3>
                
                <div class="space-y-3">
                    <form action="{{ route('admin.menus.toggle-status', $menu) }}" method="POST" class="toggle-form"
                          data-message="آیا می‌خواهید وضعیت منو '{{ $menu->title }}' را تغییر دهید؟"
                          data-title="تغییر وضعیت منو">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full text-center px-4 py-2 rounded-lg font-medium transition-colors duration-200
                                       {{ $menu->status ? 'bg-red-500/20 text-red-400 hover:bg-red-500/30' : 'bg-green-500/20 text-green-400 hover:bg-green-500/30' }}">
                            <i class="fas fa-power-off ml-2"></i>
                            {{ $menu->status ? 'غیرفعال کردن' : 'فعال کردن' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.menus.edit', $menu) }}" 
                       class="block w-full text-center bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                        <i class="fas fa-edit ml-2"></i>
                        ویرایش منو
                    </a>

                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="delete-form"
                          data-message="آیا از حذف منو '{{ $menu->title }}' اطمینان دارید؟ این عمل قابل بازگشت نیست."
                          data-title="حذف منو">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-500/20 text-red-400 px-4 py-2 rounded-lg font-medium hover:bg-red-500/30 transition-colors duration-200">
                            <i class="fas fa-trash ml-2"></i>
                            حذف منو
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Children Menus -->
    @if($menu->children->count() > 0)
        <div class="mt-6">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">منوهای فرزند</h3>
                
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[600px]">
                        <thead class="bg-dark-tertiary">
                            <tr>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">عنوان</th>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">آدرس</th>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">وضعیت</th>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($menu->children as $child)
                                <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50 transition-colors duration-200">
                                    <td class="py-3 px-4">
                                        <div class="text-gray-300 font-medium">{{ $child->title }}</div>
                                        <div class="text-gray-500 text-xs">{{ $child->slug }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        @if($child->url)
                                            <a href="{{ $child->url }}" target="_blank" 
                                               class="text-yellow-primary hover:text-yellow-secondary text-sm">
                                                {{ Str::limit($child->url, 30) }}
                                            </a>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                                   {{ $child->status ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                            {{ $child->status ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.menus.show', $child) }}" 
                                               class="text-blue-400 hover:text-blue-300 p-1 rounded"
                                               title="مشاهده">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.menus.edit', $child) }}" 
                                               class="text-yellow-primary hover:text-yellow-secondary p-1 rounded"
                                               title="ویرایش">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</main>
@endsection
