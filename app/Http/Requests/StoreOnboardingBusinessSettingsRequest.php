<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOnboardingBusinessSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'locale'      => ['sometimes', 'string', Rule::in(['en', 'th'])],
            'country'     => ['required', 'string', Rule::in(array_keys(config('countries.list')))],
            'terms'       => ['accepted'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Default locale to the current app locale when not submitted
        if (! $this->has('locale') || ! $this->input('locale')) {
            $this->merge(['locale' => app()->getLocale() ?: 'th']);
        }
    }

    public function messages(): array
    {
        return [
            'locale.in' => __('validation.locale_invalid'),
        ];
    }
}
