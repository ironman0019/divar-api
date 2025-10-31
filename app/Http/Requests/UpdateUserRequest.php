<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;
        
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $userId,
            'mobile' => 'required|string|unique:users,mobile,' . $userId . '|regex:/^09[0-9]{9}$/',
            'password' => 'nullable|string|min:6|confirmed',
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
            'password.min' => 'رمز عبور باید حداقل 6 کاراکتر باشد.',
            'password.confirmed' => 'تکرار رمز عبور با رمز عبور مطابقت ندارد.',
            'city_id.exists' => 'شهر انتخاب شده معتبر نیست.',
        ];
    }
}

