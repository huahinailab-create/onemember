<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigureCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Unchecked checkboxes are not submitted; normalise to boolean.
        $this->merge([
            'expiration_enabled'     => $this->boolean('expiration_enabled'),
            'birthday_bonus_enabled' => $this->boolean('birthday_bonus_enabled'),
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
            'spend_amount'           => ['required', 'integer', 'min:1'],
            'points_awarded'         => ['required', 'integer', 'min:1'],
            'expiration_enabled'     => ['boolean'],
            'expiration_duration'    => ['nullable', 'integer', 'min:1', 'required_if:expiration_enabled,true'],
            'expiration_unit'        => ['nullable', 'in:months,years'],
            'birthday_bonus_enabled' => ['boolean'],
            'birthday_bonus_points'  => ['nullable', 'integer', 'min:1', 'required_if:birthday_bonus_enabled,true'],
        ];
    }

    public function messages(): array
    {
        return [
            'expiration_duration.required_if'    => 'Please enter a duration when expiration is enabled.',
            'birthday_bonus_points.required_if'  => 'Please enter the bonus points when birthday bonus is enabled.',
        ];
    }
}
