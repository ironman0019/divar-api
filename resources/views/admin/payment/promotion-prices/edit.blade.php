@extends('admin.layouts.master')

@section('title', 'ویرایش تعرفه تبلیغ')

@section('content')
<main class="p-4 lg:p-6">
    <div class="mb-6">
        <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">ویرایش تعرفه تبلیغ</h1>
        <a href="{{ route('admin.payment.promotion-prices.index') }}" class="text-gray-400 hover:text-yellow-primary"><i class="fas fa-arrow-right ml-2"></i> بازگشت</a>
    </div>

    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
        <form action="{{ route('admin.payment.promotion-prices.update', $promotionPrice) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-300 mb-2">نوع <span class="text-red-400">*</span></label>
                    <select name="type" class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300">
                        <option value="ladder" @selected(old('type', $promotionPrice->type) === 'ladder')>نردبان</option>
                        <option value="special" @selected(old('type', $promotionPrice->type) === 'special')>ویژه</option>
                    </select>
                    @error('type')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-gray-300 mb-2">مدت (روز) <span class="text-red-400">*</span></label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', $promotionPrice->duration_days) }}" min="1" max="365"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300">
                    @error('duration_days')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-gray-300 mb-2">قیمت (تومان) <span class="text-red-400">*</span></label>
                    <input type="number" name="price" value="{{ old('price', $promotionPrice->price) }}" min="0" step="0.01"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300">
                    @error('price')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $promotionPrice->is_active)) class="w-4 h-4 mr-3">
                        <span class="text-gray-300">فعال</span>
                    </label>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-gray-700">
                <button type="submit" class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg">ذخیره</button>
            </div>
        </form>
    </div>
</main>
@endsection
