@extends('admin.layouts.master')

@section('title', 'مدیریت منوها')

@section('content')
<!-- Menu Management Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مدیریت منوها</h1>
            <p class="text-gray-400 text-sm lg:text-base">مدیریت و سازماندهی منوهای سایت</p>
        </div>
        <a href="{{ route('admin.menus.create') }}" 
           class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-plus ml-2"></i>
            افزودن منو جدید
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        @include('admin.components.alerts.success', ['message' => session('success')])
    @endif

    @if(session('error'))
        @include('admin.components.alerts.error', ['message' => session('error')])
    @endif

    <!-- Menu Table -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">تصویر</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">عنوان</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">آدرس</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">موقعیت</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">والد</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">وضعیت</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $menu)
                        <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50 transition-colors duration-200">
                            <td class="py-4 px-6">
                                @if($menu->icon)
                                    <img src="{{ asset($menu->icon) }}" alt="{{ $menu->title }}" 
                                         class="w-10 h-10 rounded-lg object-cover">
                                @else
                                    <div class="w-10 h-10 bg-gray-700 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-500"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 font-medium">{{ $menu->title }}</div>
                                <div class="text-gray-500 text-xs">{{ $menu->slug }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">
                                    @if($menu->url)
                                        <a href="{{ $menu->url }}" target="_blank" 
                                           class="text-yellow-primary hover:text-yellow-secondary">
                                            {{ Str::limit($menu->url, 30) }}
                                        </a>
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded-full text-xs">
                                    {{ $menu->position }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($menu->parent)
                                    <span class="text-gray-300 text-sm">{{ $menu->parent->title }}</span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <form action="{{ route('admin.menus.toggle-status', $menu) }}" method="POST" class="inline toggle-form"
                                      data-message="آیا می‌خواهید وضعیت منو '{{ $menu->title }}' را تغییر دهید؟"
                                      data-title="تغییر وضعیت منو">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200
                                                   {{ $menu->status ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' : 'bg-red-500/20 text-red-400 hover:bg-red-500/30' }}">
                                        {{ $menu->status ? 'فعال' : 'غیرفعال' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.menus.show', $menu) }}" 
                                       class="text-blue-400 hover:text-blue-300 p-2 rounded-lg hover:bg-blue-500/20 transition-colors duration-200"
                                       title="مشاهده">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.menus.edit', $menu) }}" 
                                       class="text-yellow-primary hover:text-yellow-secondary p-2 rounded-lg hover:bg-yellow-primary/20 transition-colors duration-200"
                                       title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline delete-form"
                                          data-message="آیا از حذف منو '{{ $menu->title }}' اطمینان دارید؟ این عمل قابل بازگشت نیست."
                                          data-title="حذف منو">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300 p-2 rounded-lg hover:bg-red-500/20 transition-colors duration-200"
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-400">
                                <i class="fas fa-list-ul text-4xl mb-4 block"></i>
                                <p>هیچ منویی یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($menus->hasPages())
        <div class="mt-6">
            {{ $menus->links() }}
        </div>
    @endif
</main>
@endsection
