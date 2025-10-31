@extends('admin.layouts.master')

@section('title', 'ویرایش ویژگی')

@section('content')
<!-- Edit Category Attribute Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">ویرایش ویژگی</h1>
            <p class="text-gray-400 text-sm lg:text-base">ویرایش اطلاعات ویژگی: {{ $attribute->name }}</p>
        </div>
        <a href="{{ route('admin.categories.attributes.index') }}" 
           class="text-gray-400 hover:text-yellow-primary transition-colors duration-200 mt-4 sm:mt-0">
            <i class="fas fa-arrow-right ml-2"></i>
            بازگشت به لیست ویژگی‌ها
        </a>
    </div>

    <!-- Form -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
        <form action="{{ route('admin.categories.attributes.update', $attribute) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="lg:col-span-2">
                    <label for="name" class="block text-gray-300 font-medium mb-2">
                        نام ویژگی <span class="text-red-400">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $attribute->name) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="نام ویژگی را وارد کنید"
                           required>
                    @error('name')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-gray-300 font-medium mb-2">
                        دسته‌بندی <span class="text-red-400">*</span>
                    </label>
                    <select id="category_id" 
                            name="category_id"
                            class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                            required>
                        <option value="">انتخاب دسته‌بندی</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $attribute->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-gray-300 font-medium mb-2">
                        نوع <span class="text-red-400">*</span>
                    </label>
                    <select id="type" 
                            name="type"
                            class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                            required>
                        <option value="">انتخاب نوع</option>
                        <option value="0" {{ old('type', $attribute->type) == '0' ? 'selected' : '' }}>متن</option>
                        <option value="1" {{ old('type', $attribute->type) == '1' ? 'selected' : '' }}>عدد</option>
                        <option value="2" {{ old('type', $attribute->type) == '2' ? 'selected' : '' }}>انتخاب</option>
                        <option value="3" {{ old('type', $attribute->type) == '3' ? 'selected' : '' }}>چند انتخابی</option>
                    </select>
                    @error('type')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Unit -->
                <div>
                    <label for="unit" class="block text-gray-300 font-medium mb-2">واحد</label>
                    <input type="text" 
                           id="unit" 
                           name="unit" 
                           value="{{ old('unit', $attribute->unit) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none transition-colors duration-200"
                           placeholder="مثل: متر، کیلوگرم، ...">
                    @error('unit')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="status" 
                               value="1" 
                               {{ old('status', $attribute->status) ? 'checked' : '' }}
                               class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                        <span class="text-gray-300 font-medium mr-3">فعال</span>
                    </label>
                    <p class="text-gray-500 text-xs mt-1">ویژگی در سایت نمایش داده شود</p>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-700">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    به‌روزرسانی ویژگی
                </button>
                <a href="{{ route('admin.categories.attributes.index') }}" 
                   class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</main>
@endsection

