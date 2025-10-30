@extends('admin.layouts.master')

@section('title', 'ویرایش آگهی')

@section('content')
<!-- Edit Advertisement Content -->
<main class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
        <div>
            <h1 class="text-yellow-primary font-bold text-xl lg:text-2xl mb-2">ویرایش آگهی</h1>
            <p class="text-gray-400 text-sm lg:text-base">{{ $advertisement->title }}</p>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <a href="{{ route('admin.advertisements.show', $advertisement) }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-600 transition-colors duration-200">
                <i class="fas fa-eye ml-2"></i>
                مشاهده
            </a>
            <a href="{{ route('admin.advertisements.index') }}" 
               class="bg-gray-600 text-gray-300 px-4 py-2 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                <i class="fas fa-arrow-right ml-2"></i>
                بازگشت
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

    <!-- Edit Form -->
    <div class="bg-dark-secondary rounded-xl border border-yellow-primary/20 p-6">
        <form action="{{ route('admin.advertisements.update', $advertisement) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="lg:col-span-2">
                    <label for="title" class="block text-gray-300 font-medium mb-2">عنوان *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title', $advertisement->title) }}"
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label for="description" class="block text-gray-300 font-medium mb-2">توضیحات *</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('description') border-red-500 @enderror">{{ old('description', $advertisement->description) }}</textarea>
                    @error('description')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category_id" class="block text-gray-300 font-medium mb-2">دسته‌بندی *</label>
                    <select name="category_id" 
                            id="category_id"
                            class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('category_id') border-red-500 @enderror">
                        <option value="">انتخاب دسته‌بندی</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ old('category_id', $advertisement->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city_id" class="block text-gray-300 font-medium mb-2">شهر *</label>
                    <select name="city_id" 
                            id="city_id"
                            class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('city_id') border-red-500 @enderror">
                        <option value="">انتخاب شهر</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" 
                                    {{ old('city_id', $advertisement->city_id) == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('city_id')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-gray-300 font-medium mb-2">قیمت (تومان)</label>
                    <input type="number" 
                           id="price" 
                           name="price" 
                           value="{{ old('price', $advertisement->price) }}"
                           min="0"
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact -->
                <div>
                    <label for="contact" class="block text-gray-300 font-medium mb-2">شماره تماس</label>
                    <input type="text" 
                           id="contact" 
                           name="contact" 
                           value="{{ old('contact', $advertisement->contact) }}"
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('contact') border-red-500 @enderror">
                    @error('contact')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ads Type -->
                <div>
                    <label for="ads_type" class="block text-gray-300 font-medium mb-2">نوع آگهی</label>
                    <input type="text" 
                           id="ads_type" 
                           name="ads_type" 
                           value="{{ old('ads_type', $advertisement->ads_type) }}"
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('ads_type') border-red-500 @enderror">
                    @error('ads_type')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ads Status -->
                <div>
                    <label for="ads_status" class="block text-gray-300 font-medium mb-2">وضعیت آگهی</label>
                    <input type="text" 
                           id="ads_status" 
                           name="ads_status" 
                           value="{{ old('ads_status', $advertisement->ads_status) }}"
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('ads_status') border-red-500 @enderror">
                    @error('ads_status')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tags -->
                <div>
                    <label for="tags" class="block text-gray-300 font-medium mb-2">برچسب‌ها</label>
                    <input type="text" 
                           id="tags" 
                           name="tags" 
                           value="{{ old('tags', $advertisement->tags) }}"
                           placeholder="برچسب‌ها را با کاما جدا کنید"
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('tags') border-red-500 @enderror">
                    @error('tags')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-gray-300 font-medium mb-2">وضعیت *</label>
                    <select name="status" 
                            id="status"
                            class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('status') border-red-500 @enderror">
                        <option value="0" {{ old('status', $advertisement->status) == 0 ? 'selected' : '' }}>غیرفعال</option>
                        <option value="1" {{ old('status', $advertisement->status) == 1 ? 'selected' : '' }}>فعال</option>
                        <option value="2" {{ old('status', $advertisement->status) == 2 ? 'selected' : '' }}>تایید شده</option>
                        <option value="3" {{ old('status', $advertisement->status) == 3 ? 'selected' : '' }}>در انتظار</option>
                        <option value="4" {{ old('status', $advertisement->status) == 4 ? 'selected' : '' }}>منقضی شده</option>
                    </select>
                    @error('status')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expired At -->
                <div>
                    <label for="expired_at" class="block text-gray-300 font-medium mb-2">تاریخ انقضا</label>
                    <input type="datetime-local" 
                           id="expired_at" 
                           name="expired_at" 
                           value="{{ old('expired_at', $advertisement->expired_at ? $advertisement->expired_at->format('Y-m-d\TH:i') : '') }}"
                           class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('expired_at') border-red-500 @enderror">
                    @error('expired_at')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location -->
                <div class="lg:col-span-2">
                    <h3 class="text-yellow-primary font-bold text-lg mb-4">موقعیت جغرافیایی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="lat" class="block text-gray-300 font-medium mb-2">عرض جغرافیایی</label>
                            <input type="text" 
                                   id="lat" 
                                   name="lat" 
                                   value="{{ old('lat', $advertisement->lat) }}"
                                   class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('lat') border-red-500 @enderror">
                            @error('lat')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="lng" class="block text-gray-300 font-medium mb-2">طول جغرافیایی</label>
                            <input type="text" 
                                   id="lng" 
                                   name="lng" 
                                   value="{{ old('lng', $advertisement->lng) }}"
                                   class="w-full px-4 py-3 bg-dark-tertiary border border-gray-600 rounded-lg text-gray-300 focus:border-yellow-primary focus:ring-1 focus:ring-yellow-primary focus:outline-none @error('lng') border-red-500 @enderror">
                            @error('lng')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Willing to Trade -->
                <div class="lg:col-span-2">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="willing_to_trade" 
                               name="willing_to_trade" 
                               value="1"
                               {{ old('willing_to_trade', $advertisement->willing_to_trade) ? 'checked' : '' }}
                               class="w-4 h-4 text-yellow-primary bg-dark-tertiary border-gray-600 rounded focus:ring-yellow-primary focus:ring-2">
                        <label for="willing_to_trade" class="mr-2 text-gray-300 font-medium">
                            آماده معاوضه
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 mt-8">
                <button type="submit" 
                        class="bg-yellow-primary text-dark-primary px-6 py-3 rounded-lg font-medium hover:bg-yellow-secondary transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    ذخیره تغییرات
                </button>
                <a href="{{ route('admin.advertisements.show', $advertisement) }}" 
                   class="bg-gray-600 text-gray-300 px-6 py-3 rounded-lg font-medium hover:bg-gray-500 transition-colors duration-200">
                    <i class="fas fa-times ml-2"></i>
                    انصراف
                </a>
            </div>
        </form>
    </div>
</main>
@endsection
