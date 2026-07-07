<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/** BETA-008B — Global Settings / Localization tab. */
class UpdateMerchantLocalizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'country'  => ['required', 'string', Rule::in(array_keys(config('countries.list')))],
            'currency' => ['required', 'string', Rule::in(array_keys(config('localization.currencies')))],
            'accepted_currencies'   => ['nullable', 'array'],
            'accepted_currencies.*' => ['string', Rule::in(array_keys(config('localization.currencies')))],
            'timezone' => ['required', 'string', Rule::in(timezone_identifiers_list())],
            'locale'   => ['required', 'string', Rule::in(array_keys(config('localization.internal_languages')))],
            'customer_languages'    => ['required', 'array', 'min:1'],
            'customer_languages.*'  => ['string', Rule::in(array_keys(config('localization.customer_languages')))],
        ];
    }

    public function attributes(): array
    {
        return [
            'locale'              => 'internal language',
            'customer_languages'  => 'customer-facing languages',
            'accepted_currencies' => 'accepted currencies',
        ];
    }
}
