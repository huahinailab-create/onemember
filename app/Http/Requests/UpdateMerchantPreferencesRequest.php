<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMerchantPreferencesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'default_birthday_enabled'    => $this->boolean('default_birthday_enabled'),
            'email_product_updates'        => $this->boolean('email_product_updates'),
            'email_tips'                   => $this->boolean('email_tips'),
            'email_feature_announcements'  => $this->boolean('email_feature_announcements'),
        ]);
    }

    public function rules(): array
    {
        return [
            'currency'    => ['required', 'string', Rule::in([
                'THB', 'USD', 'EUR', 'GBP', 'JPY', 'SGD',
                'MYR', 'IDR', 'PHP', 'VND', 'AUD', 'CAD',
            ])],
            'timezone'    => ['required', 'string', Rule::in(timezone_identifiers_list())],
            'date_format' => ['required', 'string', Rule::in(['DD/MM/YYYY', 'MM/DD/YYYY', 'YYYY-MM-DD'])],
            'default_expiration_type'     => ['required', 'string', Rule::in(['never', 'months', 'years'])],
            'default_expiration_duration' => ['nullable', 'integer', 'min:1', 'max:120',
                Rule::requiredIf(fn () => in_array($this->input('default_expiration_type'), ['months', 'years'])),
            ],
            'default_birthday_enabled'   => ['required', 'boolean'],
            'locale'                     => ['required', 'string', Rule::in(['en', 'th'])],
            'email_product_updates'       => ['required', 'boolean'],
            'email_tips'                  => ['required', 'boolean'],
            'email_feature_announcements' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'default_expiration_type'     => 'points expiration',
            'default_expiration_duration' => 'expiration duration',
            'default_birthday_enabled'    => 'birthday reward default',
        ];
    }
}
