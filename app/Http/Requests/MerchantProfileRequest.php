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
            'name'           => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'address'        => ['nullable', 'string', 'max:500'],
            'currency'       => ['required', 'string', 'size:3'],
            'timezone'       => ['required', 'string', 'timezone:all'],
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
