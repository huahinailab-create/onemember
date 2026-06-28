<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOnboardingBusinessInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', Rule::in([
                'Hair Salon', 'Nail Salon', 'Massage & Spa', 'Restaurant & Café',
                'Hotel', 'Fashion Retail', 'Beauty & Cosmetics', 'Grocery Store',
                'Pet Shop', 'Wholesale', 'Other',
            ])],
            'phone'   => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }
}
