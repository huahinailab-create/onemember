<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $merchantId = $this->user()->merchant?->id;
        $memberId   = $this->route('member')?->id;

        return [
            'name'     => ['required', 'string', 'max:150'],
            'phone'    => [
                'required',
                'string',
                'max:30',
                Rule::unique('members', 'phone')
                    ->where('merchant_id', $merchantId)
                    ->whereNull('deleted_at')
                    ->ignore($memberId),
            ],
            'birthday' => ['required', 'date'],
            'nickname' => ['nullable', 'string', 'max:50'],
            'email'    => ['nullable', 'email', 'max:255'],
            'notes'    => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'     => 'Full Name',
            'phone'    => 'Mobile Number',
            'birthday' => 'Date of Birth',
            'nickname' => 'Nickname',
            'email'    => 'Email',
            'notes'    => 'Notes',
        ];
    }
}
