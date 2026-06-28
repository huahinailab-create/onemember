<?php

namespace App\Http\Requests;

use App\Enums\CampaignStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:150'],
            'type'        => ['required', Rule::in(['points', 'stamps'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'status'      => ['required', Rule::enum(CampaignStatus::class)],
        ];
    }
}
