@extends('admin.layouts.master')

@section('title', 'ویرایش شهر')

@section('content')
<main class="p-4 lg:p-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">ویرایش شهر</h1>
            <p class="text-gray-400">{{ $city->name }}</p>
        </div>
        <a href="{{ route('admin.cities.index') }}" class="text-gray-400 hover:text-yellow-primary mt-4 sm:mt-0">
            <i class="fas fa-arrow-right ml-2"></i> بازگشت
        </a>
    </div>

    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
        <form action="{{ route('admin.cities.update', $city) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-gray-300 font-medium mb-2">نام شهر <span class="text-red-400">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $city->name) }}"
                           class="w-full bg-dark-tertiary border border-gray-600 rounded-lg px-4 py-3 text-gray-300 focus:border-yellow-primary focus:outline-none">
                    @error('name')<p class="text-red-400 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-end">
                    <label class="flex items-center">
                        <input type="checkbox" name="status" value="1" @checked(old('status', $city->status))
                               class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded">
                        <span class="text-gray-300 font-medium mr-3">فعال</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center gap-4 mt-8 pt-6 border-t border-gray-700">
                <button type="submit" class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary">ذخیره</button>
                <a href="{{ route('admin.cities.index') }}" class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg">انصراف</a>
            </div>
        </form>
    </div>
</main>
@endsection
