<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryAttributeRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'unit' => 'nullable|string|max:50',
            'type' => 'required|integer|in:0,1,2,3',
            'category_id' => 'required|exists:categories,id',
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
            'name.required' => 'نام ویژگی الزامی است.',
            'type.required' => 'نوع ویژگی الزامی است.',
            'type.in' => 'نوع ویژگی باید یکی از مقادیر معتبر باشد.',
            'category_id.required' => 'دسته‌بندی الزامی است.',
            'category_id.exists' => 'دسته‌بندی انتخاب شده معتبر نیست.',
        ];
    }
}

