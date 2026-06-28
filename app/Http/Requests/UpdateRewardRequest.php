<?php

namespace App\Http\Requests;

use App\Enums\RewardStatus;
use App\Enums\RewardType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'unlimited' => $this->boolean('unlimited'),
        ]);

        if ($this->boolean('unlimited')) {
            $this->merge(['quantity_available' => null]);
        }
    }

    public function rules(): array
    {
        $campaign = $this->route('campaign');

        $rules = [
            'name'               => ['required', 'string', 'max:100'],
            'description'        => ['nullable', 'string', 'max:1000'],
            'type'               => ['required', Rule::enum(RewardType::class)],
            'unlimited'          => ['boolean'],
            'quantity_available' => $this->boolean('unlimited')
                                        ? ['nullable']
                                        : ['required', 'integer', 'min:1'],
            'status'             => ['required', Rule::enum(RewardStatus::class)],
            'internal_notes'     => ['nullable', 'string', 'max:1000'],
        ];

        if ($campaign && $campaign->type->value === 'points') {
            $rules['points_required'] = ['required', 'integer', 'min:1'];
        }

        return $rules;
    }
}
