<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => ['required', 'string', 'max:255'],
            'contact_person'   => ['nullable', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:30'],
            'address'          => ['nullable', 'string', 'max:500'],
            'currency'         => ['required', 'string', 'size:3'],
            'timezone'         => ['required', 'string', 'timezone:all'],
            // Branding
            'logo'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'brand_color'      => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'business_tagline' => ['nullable', 'string', 'max:100'],
            'receipt_footer'   => ['nullable', 'string', 'max:500'],
            'website'          => ['nullable', 'url', 'max:255'],
            'facebook_url'     => ['nullable', 'url', 'max:255'],
            'instagram_url'    => ['nullable', 'url', 'max:255'],
            'line_url'         => ['nullable', 'url', 'max:255'],
            'remove_logo'      => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'           => 'business name',
            'contact_person' => 'contact person',
            'email'          => 'business email',
            'phone'          => 'mobile number',
            'address'        => 'business address',
            'currency'       => 'currency',
            'timezone'       => 'time zone',
        ];
    }
}
