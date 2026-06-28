<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_amount' => ['required', 'numeric', 'min:0.01'],
            'invoice_number'  => ['nullable', 'string', 'max:100'],
            'note'            => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_amount.required' => 'Please enter the purchase amount.',
            'purchase_amount.numeric'  => 'Purchase amount must be a number.',
            'purchase_amount.min'      => 'Purchase amount must be greater than zero.',
        ];
    }
}
