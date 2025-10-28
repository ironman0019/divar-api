<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdvertisementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'ads_type' => ['required', 'string', Rule::in(['sell', 'buy', 'rent', 'exchange'])],
            'ads_status' => ['required', 'string', Rule::in(['as_good_as_new', 'excellent', 'good', 'fair', 'poor'])],
            'price' => ['nullable', 'integer', 'min:0'],
            'contact' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'string', 'max:500'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'willing_to_trade' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // Main advertisement image
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // Gallery images
            'category_values' => ['nullable', 'array'],
            'category_values.*' => ['integer', 'exists:category_values,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'عنوان آگهی الزامی است',
            'title.max' => 'عنوان آگهی نمی‌تواند بیش از 255 کاراکتر باشد',
            'description.required' => 'توضیحات آگهی الزامی است',
            'description.max' => 'توضیحات آگهی نمی‌تواند بیش از 5000 کاراکتر باشد',
            'category_id.required' => 'انتخاب دسته‌بندی الزامی است',
            'category_id.exists' => 'دسته‌بندی انتخاب شده نامعتبر است',
            'city_id.required' => 'انتخاب شهر الزامی است',
            'city_id.exists' => 'شهر انتخاب شده نامعتبر است',
            'ads_type.required' => 'نوع آگهی الزامی است',
            'ads_type.in' => 'نوع آگهی نامعتبر است',
            'ads_status.required' => 'وضعیت آگهی الزامی است',
            'ads_status.in' => 'وضعیت آگهی نامعتبر است',
            'price.integer' => 'قیمت باید عدد باشد',
            'price.min' => 'قیمت نمی‌تواند منفی باشد',
            'contact.max' => 'اطلاعات تماس نمی‌تواند بیش از 255 کاراکتر باشد',
            'tags.max' => 'برچسب‌ها نمی‌توانند بیش از 500 کاراکتر باشند',
            'lat.numeric' => 'عرض جغرافیایی باید عدد باشد',
            'lat.between' => 'عرض جغرافیایی باید بین -90 تا 90 باشد',
            'lng.numeric' => 'طول جغرافیایی باید عدد باشد',
            'lng.between' => 'طول جغرافیایی باید بین -180 تا 180 باشد',
            'willing_to_trade.boolean' => 'آمادگی معاوضه باید true یا false باشد',
            'image.image' => 'تصویر اصلی باید فایل تصویری باشد',
            'image.mimes' => 'فرمت تصویر اصلی باید jpeg، png، jpg یا gif باشد',
            'image.max' => 'حجم تصویر اصلی نمی‌تواند بیش از 5 مگابایت باشد',
            'images.array' => 'تصاویر گالری باید به صورت آرایه ارسال شوند',
            'images.max' => 'حداکثر 10 تصویر قابل آپلود است',
            'images.*.image' => 'فایل ارسالی باید تصویر باشد',
            'images.*.mimes' => 'فرمت تصویر باید jpeg، png، jpg یا gif باشد',
            'images.*.max' => 'حجم هر تصویر نمی‌تواند بیش از 5 مگابایت باشد',
            'category_values.array' => 'مقادیر دسته‌بندی باید به صورت آرایه ارسال شوند',
            'category_values.*.exists' => 'مقدار دسته‌بندی انتخاب شده نامعتبر است',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'عنوان آگهی',
            'description' => 'توضیحات آگهی',
            'category_id' => 'دسته‌بندی',
            'city_id' => 'شهر',
            'ads_type' => 'نوع آگهی',
            'ads_status' => 'وضعیت آگهی',
            'price' => 'قیمت',
            'contact' => 'اطلاعات تماس',
            'tags' => 'برچسب‌ها',
            'lat' => 'عرض جغرافیایی',
            'lng' => 'طول جغرافیایی',
            'willing_to_trade' => 'آمادگی معاوضه',
            'image' => 'تصویر اصلی',
            'images' => 'تصاویر گالری',
            'category_values' => 'مقادیر دسته‌بندی',
        ];
    }
}
