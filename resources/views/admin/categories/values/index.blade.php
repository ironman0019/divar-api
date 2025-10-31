@extends('admin.layouts.master')

@section('title', 'مدیریت مقادیر ویژگی‌ها')

@section('content')
<!-- Category Values Management Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مدیریت مقادیر ویژگی‌ها</h1>
            <p class="text-gray-400 text-sm lg:text-base">مدیریت مقادیر ویژگی‌های دسته‌بندی‌ها</p>
        </div>
        <a href="{{ route('admin.categories.values.create') }}" 
           class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-plus ml-2"></i>
            افزودن مقدار جدید
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
        <form method="GET" action="{{ route('admin.categories.values.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <select name="status" 
                        class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>فعال</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غیرفعال</option>
                </select>
            </div>

            <!-- Attribute Filter -->
            <div>
                <select name="category_attribute_id" 
                        class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-2 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none">
                    <option value="">همه ویژگی‌ها</option>
                    @foreach($attributes as $attr)
                        <option value="{{ $attr->id }}" {{ request('category_attribute_id') == $attr->id ? 'selected' : '' }}>
                            {{ $attr->name }} ({{ $attr->category->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <div class="md:col-span-3 mt-4">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-search ml-2"></i>
                    جستجو
                </button>
            </div>
        </form>
    </div>

    <!-- Values Table -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-dark-tertiary">
                    <tr>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">مقدار</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">ویژگی</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">دسته‌بندی</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">نوع</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">وضعیت</th>
                        <th class="text-right text-gray-400 font-medium py-4 px-6 text-sm">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($values as $value)
                        <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50 transition-colors duration-200">
                            <td class="py-4 px-6">
                                <div class="text-gray-300 font-medium">{{ $value->value }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">{{ $value->categoryAttribute->name ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-gray-300 text-sm">{{ $value->categoryAttribute->category->name ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded-full text-xs">
                                    {{ $value->type_label }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <form action="{{ route('admin.categories.values.toggle-status', $value) }}" method="POST" class="inline toggle-form"
                                      data-message="آیا می‌خواهید وضعیت مقدار '{{ $value->value }}' را تغییر دهید؟"
                                      data-title="تغییر وضعیت مقدار">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="px-3 py-1 rounded-full text-xs font-medium transition-colors duration-200
                                                   {{ $value->status ? 'bg-green-500/20 text-green-400 hover:bg-green-500/30' : 'bg-red-500/20 text-red-400 hover:bg-red-500/30' }}">
                                        {{ $value->status ? 'فعال' : 'غیرفعال' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.categories.values.show', $value) }}" 
                                       class="text-blue-400 hover:text-blue-300 p-2 rounded-lg hover:bg-blue-500/20 transition-colors duration-200"
                                       title="مشاهده">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.categories.values.edit', $value) }}" 
                                       class="text-yellow-primary hover:text-yellow-secondary p-2 rounded-lg hover:bg-yellow-primary/20 transition-colors duration-200"
                                       title="ویرایش">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.categories.values.destroy', $value) }}" method="POST" class="inline delete-form"
                                          data-message="آیا از حذف مقدار '{{ $value->value }}' اطمینان دارید؟ این عمل قابل بازگشت نیست."
                                          data-title="حذف مقدار">
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
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <i class="fas fa-list text-4xl mb-4 block"></i>
                                <p>هیچ مقداری یافت نشد</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($values->hasPages())
        <div class="mt-6">
            {{ $values->links() }}
        </div>
    @endif
</main>
@endsection

