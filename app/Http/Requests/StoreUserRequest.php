<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile|regex:/^09[0-9]{9}$/',
            'password' => 'required|string|min:6|confirmed',
            'city_id' => 'nullable|exists:cities,id',
            'is_active' => 'nullable|boolean',
            'is_admin' => 'nullable|boolean',
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
            'mobile.required' => 'شماره موبایل الزامی است.',
            'mobile.unique' => 'این شماره موبایل قبلاً ثبت شده است.',
            'mobile.regex' => 'فرمت شماره موبایل صحیح نیست.',
            'email.email' => 'فرمت ایمیل صحیح نیست.',
            'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
            'password.required' => 'رمز عبور الزامی است.',
            'password.min' => 'رمز عبور باید حداقل 6 کاراکتر باشد.',
            'password.confirmed' => 'تکرار رمز عبور با رمز عبور مطابقت ندارد.',
            'city_id.exists' => 'شهر انتخاب شده معتبر نیست.',
        ];
    }
}

