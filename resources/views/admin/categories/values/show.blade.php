@extends('admin.layouts.master')

@section('title', 'مشاهده مقدار')

@section('content')
<!-- Show Category Value Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">مشاهده مقدار</h1>
            <p class="text-gray-400 text-sm lg:text-base">جزئیات مقدار: {{ Str::limit($value->value, 50) }}</p>
        </div>
        <div class="flex items-center gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.categories.values.edit', $value) }}" 
               class="bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                <i class="fas fa-edit ml-2"></i>
                ویرایش
            </a>
            <a href="{{ route('admin.categories.values.index') }}" 
               class="text-gray-400 hover:text-yellow-primary transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                بازگشت به لیست
            </a>
        </div>
    </div>

    <!-- Value Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-6">اطلاعات کلی</h3>
                
                <div class="space-y-4">
                    <!-- Value -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">مقدار:</div>
                        <div class="text-gray-300">{{ $value->value }}</div>
                    </div>

                    <!-- Type -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">نوع:</div>
                        <div>
                            <span class="bg-blue-500/20 text-blue-400 px-3 py-1 rounded-full text-sm">
                                {{ $value->type_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Attribute -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">ویژگی:</div>
                        <div class="text-gray-300">
                            @if($value->categoryAttribute)
                                <a href="{{ route('admin.categories.attributes.show', $value->categoryAttribute) }}" 
                                   class="text-yellow-primary hover:text-yellow-secondary">
                                    {{ $value->categoryAttribute->name }}
                                </a>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </div>

                    <!-- Category -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">دسته‌بندی:</div>
                        <div class="text-gray-300">
                            @if($value->categoryAttribute && $value->categoryAttribute->category)
                                <a href="{{ route('admin.categories.show', $value->categoryAttribute->category) }}" 
                                   class="text-yellow-primary hover:text-yellow-secondary">
                                    {{ $value->categoryAttribute->category->name }}
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
                                       {{ $value->status ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' }}">
                                {{ $value->status ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">تاریخ ایجاد:</div>
                        <div class="text-gray-300">{{ $value->created_at->format('Y/m/d H:i') }}</div>
                    </div>

                    <!-- Updated At -->
                    <div class="flex items-start gap-4">
                        <div class="w-32 text-gray-400 font-medium">آخرین به‌روزرسانی:</div>
                        <div class="text-gray-300">{{ $value->updated_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="space-y-6">
            <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
                <h3 class="text-yellow-primary font-bold text-lg mb-4">عملیات سریع</h3>
                
                <div class="space-y-3">
                    <form action="{{ route('admin.categories.values.toggle-status', $value) }}" method="POST" class="toggle-form"
                          data-message="آیا می‌خواهید وضعیت مقدار '{{ Str::limit($value->value, 30) }}' را تغییر دهید؟"
                          data-title="تغییر وضعیت مقدار">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="w-full text-center px-4 py-2 rounded-lg font-medium transition-colors duration-200
                                       {{ $value->status ? 'bg-red-500/20 text-red-400 hover:bg-red-500/30' : 'bg-green-500/20 text-green-400 hover:bg-green-500/30' }}">
                            <i class="fas fa-power-off ml-2"></i>
                            {{ $value->status ? 'غیرفعال کردن' : 'فعال کردن' }}
                        </button>
                    </form>

                    <a href="{{ route('admin.categories.values.edit', $value) }}" 
                       class="block w-full text-center bg-yellow-primary text-dark-primary px-4 py-2 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                        <i class="fas fa-edit ml-2"></i>
                        ویرایش مقدار
                    </a>

                    <form action="{{ route('admin.categories.values.destroy', $value) }}" method="POST" class="delete-form"
                          data-message="آیا از حذف این مقدار اطمینان دارید؟ این عمل قابل بازگشت نیست."
                          data-title="حذف مقدار">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-500/20 text-red-400 px-4 py-2 rounded-lg font-medium hover:bg-red-500/30 transition-colors duration-200">
                            <i class="fas fa-trash ml-2"></i>
                            حذف مقدار
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

