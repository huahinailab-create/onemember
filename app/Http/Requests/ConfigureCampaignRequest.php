<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfigureCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'birthday_enabled' => $this->boolean('birthday_enabled'),
        ]);
    }

    public function rules(): array
    {
        $campaign = $this->route('campaign');

        if ($campaign->type->value === 'stamps') {
            return [
                'stamps_required'    => ['required', 'integer', 'min:1'],
                'reward_description' => ['required', 'string', 'max:255'],
            ];
        }

        // Points campaign
        return [
            'spend_amount'               => ['required', 'integer', 'min:1'],
            'points_awarded'             => ['required', 'integer', 'min:1'],
            'expiration_type'            => ['required', 'in:never,months,years'],
            'expiration_duration'        => [
                'nullable', 'integer', 'min:1',
                Rule::requiredIf(fn () => in_array($this->input('expiration_type'), ['months', 'years'])),
            ],
            'birthday_enabled'           => ['boolean'],
            'birthday_points'            => ['nullable', 'integer', 'min:1', 'required_if:birthday_enabled,true'],
            'birthday_valid_days_before' => ['nullable', 'integer', 'min:0', 'required_if:birthday_enabled,true'],
            'birthday_valid_days_after'  => ['nullable', 'integer', 'min:0', 'required_if:birthday_enabled,true'],
        ];
    }

    public function messages(): array
    {
        return [
            'expiration_duration.required_if'        => 'Please enter a duration for point expiration.',
            'birthday_points.required_if'            => 'Please enter the bonus points when birthday bonus is enabled.',
            'birthday_valid_days_before.required_if' => 'Please enter valid days before birthday.',
            'birthday_valid_days_after.required_if'  => 'Please enter valid days after birthday.',
        ];
    }
}
