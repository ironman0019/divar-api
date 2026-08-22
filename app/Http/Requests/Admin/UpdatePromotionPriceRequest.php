<?php

namespace App\Http\Requests\Admin;

use App\Models\Advertisement\PromotionPrice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromotionPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $promotionPrice = $this->route('promotion_price') ?? $this->route('promotionPrice');

        return [
            'type' => ['required', Rule::in([PromotionPrice::TYPE_LADDER, PromotionPrice::TYPE_SPECIAL])],
            'duration_days' => [
                'required',
                'integer',
                'min:1',
                'max:365',
                Rule::unique('promotion_prices')
                    ->where(fn ($query) => $query->where('type', $this->input('type')))
                    ->ignore($promotionPrice),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع تبلیغ الزامی است.',
            'duration_days.required' => 'مدت زمان الزامی است.',
            'duration_days.unique' => 'این ترکیب نوع و مدت قبلاً ثبت شده است.',
            'price.required' => 'قیمت الزامی است.',
        ];
    }
}
