<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryValueRequest extends FormRequest
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
            'value' => 'required|string',
            'category_attribute_id' => 'required|exists:category_attributes,id',
            'type' => 'nullable|integer|in:0,1,2,3',
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
            'value.required' => 'مقدار الزامی است.',
            'category_attribute_id.required' => 'ویژگی الزامی است.',
            'category_attribute_id.exists' => 'ویژگی انتخاب شده معتبر نیست.',
            'type.in' => 'نوع مقدار باید یکی از مقادیر معتبر باشد.',
        ];
    }
}

