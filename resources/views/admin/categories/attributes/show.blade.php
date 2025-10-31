@extends('admin.layouts.master')

@section('title', 'مشاهده ویژگی')

@section('content')
<!-- Show Category Attribute Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مشاهده ویژگی</h1>
            <p class="text-gray-400 text-sm lg:text-base">جزئیات ویژگی: {{ $attribute->name }}</p>
        </div>
        <div class="flex items-center gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.categories.attributes.edit', $attribute) }}" 
               class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                <i class="fas fa-edit ml-2"></i>
                ویرایش
            </a>
            <a href="{{ route('admin.categories.attributes.index') }}" 
               class="text-gray-400 hover:text-yellow-primary transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                بازگشت به لیست
            </a>
        </div>
    </div>

  <!-- Attribute Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-6">اطلاعات کلی</h3>
                
                <div class="space-y-4">
                    <!-- Name -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">نام:</div>
                        <div class="text-gray-300">{{ $attribute->name }}</div>
                    </div>

                    <!-- Unit -->
                    @if($attribute->unit)
                        <div class="flex items-start gap-4">
                            <div class="w-32 text-gray-400 font-medium">واحد:</div>
                            <div class="text-gray-300">{{ $attribute->unit }}</div>
                        </div>
                    @endif

                    <!-- Type -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">نوع:</div>
                        <div>
                            <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-sm">
                                {{ $attribute->type_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">دسته‌بندی:</div>
                        <div class="text-gray-300">
                            @if($attribute->category)
                                <a href="{{ route('admin.categories.show', $attribute->category) }}" 
                                   class="text-yellow-primary hover:text-yellow-secondary">
                                    {{ $attribute->category->name }}
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">وضعیت:</div>
                        <div>
                            <span class="px-3 py-1 rounded-full text-sm font-medium
                                       {{ $attribute->status ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $attribute->status ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">تاریخ ایجاد:</div>
                        <div class="text-gray-300">{{ $attribute->created_at->format('Y/m/d H:i') }}</div>
                    </div>

                    <!-- Updated At -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">آخرین به‌روزرسانی:</div>
                        <div class="text-gray-300">{{ $attribute->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">عملیات سریع</h3>
                
                <div class="space-y-3">
                    <form action="{{ route('admin.categories.attributes.toggle-status', $attribute) }}" method="POST" class="toggle-form"
                          data-message="آیا می‌خواهید وضعیت ویژگی '{{ $attribute->name }}' را تغییر دهید؟"
                          data-title="تغییر وضعیت ویژگی">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full text-center px-4 py-2 rounded-lg font-medium transition-colors duration-200
                                       {{ $attribute->status ? 'bg-red-500/20 text-red-400 hover:bg-red-500/30' : 'bg-green-500/20 text-green-400 hover:bg-green-500/30' }}">
                            <i class="fas fa-power-off ml-2"></i>
                            {{ $attribute->status ? 'غیرفعال کردن' : 'فعال کردن' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.categories.attributes.edit', $attribute) }}" 
                       class="block w-full text-center bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                        <i class="fas fa-edit ml-2"></i>
                        ویرایش ویژگی
                    </a>

                    <form action="{{ route('admin.categories.attributes.destroy', $attribute) }}" method="POST" class="delete-form"
                          data-message="آیا از حذف ویژگی '{{ $attribute->name }}' اطمینان دارید؟ این عمل قابل بازگشت نیست."
                          data-title="حذف ویژگی">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-500/20 text-red-400 px-4 py-2 rounded-lg font-medium hover:bg-red-500/30 transition-colors duration-200">
                            <i class="fas fa-trash ml-2"></i>
                            حذف ویژگی
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Values -->
    @if($attribute->values->count() > 0)
        <div class="mt-6">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-yellow-primary font-bold text-lg">مقادیر ویژگی</h3>
                    <a href="{{ route('admin.categories.values.create') }}?category_attribute_id={{ $attribute->id }}" 
                       class="text-yellow-primary hover:text-yellow-secondary text-sm">
                        <i class="fas fa-plus ml-1"></i>
                        افزودن مقدار
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[600px]">
                        <thead class="bg-dark-tertiary">
                            <tr>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">مقدار</th>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">نوع</th>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">وضعیت</th>
                                <th class="text-right text-gray-400 font-medium py-3 px-4 text-sm">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attribute->values as $value)
                                <tr class="border-b border-gray-800 hover:bg-dark-tertiary/50 transition-colors duration-200">
                                    <td class="py-3 px-4">
                                        <div class="text-gray-300 font-medium">{{ $value->value }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="text-gray-300 text-sm">{{ $value->type_label }}</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                                   {{ $value->status ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                            {{ $value->status ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.categories.values.show', $value) }}" 
                                               class="text-blue-400 hover:text-blue-300 p-1 rounded"
                                               title="مشاهده">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.values.edit', $value) }}" 
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

