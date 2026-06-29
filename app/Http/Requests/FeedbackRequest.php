<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth middleware on the route is the gate
    }

    public function rules(): array
    {
        return [
            'category'    => ['required', 'string', 'in:bug,feature,question,general'],
            'subject'     => ['required', 'string', 'max:200'],
            'message'     => ['required', 'string', 'min:10', 'max:5000'],
            'current_url' => ['nullable', 'string', 'max:2000'],
            'browser'     => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category'    => 'Category',
            'subject'     => 'Subject',
            'message'     => 'Message',
            'current_url' => 'Page URL',
            'browser'     => 'Browser',
        ];
    }
}
