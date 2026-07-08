<?php

namespace App\Http\Requests;

use App\Services\Media\MediaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMerchantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $merchantId = $this->user()->merchant?->id;

        return [
            'name'          => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', Rule::in([
                'Hair Salon', 'Nail Salon', 'Massage & Spa', 'Restaurant & Café',
                'Hotel', 'Fashion Retail', 'Beauty & Cosmetics', 'Grocery Store',
                'Pet Shop', 'Wholesale', 'Other',
            ])],
            'phone'         => ['nullable', 'string', 'max:30'],
            'website'       => ['nullable', 'url', 'max:255'],
            'email'         => ['required', 'email', 'max:255',
                Rule::unique('merchants', 'email')->ignore($merchantId),
            ],
            'address_line_1' => ['nullable', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'state'          => ['nullable', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'string', 'max:20'],
            'country'        => ['nullable', 'string', 'max:100'],
            'notes'            => ['nullable', 'string', 'max:2000'],
            // Branding
            'logo'             => app(MediaService::class)->validationRules('merchant_logos'),
            'remove_logo'      => ['nullable', 'boolean'],
            'brand_color'      => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'business_tagline' => ['nullable', 'string', 'max:100'],
            'receipt_footer'   => ['nullable', 'string', 'max:500'],
            'facebook_url'     => ['nullable', 'url', 'max:255'],
            'instagram_url'    => ['nullable', 'url', 'max:255'],
            'line_url'         => ['nullable', 'url', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => 'business name',
            'business_type'  => 'business type',
            'email'          => 'business email',
            'address_line_1' => 'address line 1',
            'address_line_2' => 'address line 2',
            'postal_code'    => 'postal code',
        ];
    }
}
