<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
        $categoryId = $this->route('category')->id;
        
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $categoryId,
            'status' => 'nullable|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'نام دسته‌بندی الزامی است.',
            'slug.unique' => 'این شناسه قبلاً استفاده شده است.',
            'icon.image' => 'فایل باید تصویر باشد.',
            'icon.mimes' => 'فرمت تصویر باید یکی از jpeg, png, jpg, gif, svg باشد.',
            'icon.max' => 'حجم تصویر نباید بیشتر از 2 مگابایت باشد.',
            'parent_id.exists' => 'دسته‌بندی والد انتخاب شده معتبر نیست.',
            'parent_id.not_in' => 'یک دسته‌بندی نمی‌تواند والد خودش باشد.',
        ];
    }
}

