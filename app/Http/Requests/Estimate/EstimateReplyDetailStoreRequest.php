<?php

namespace App\Http\Requests\Estimate;

use Illuminate\Foundation\Http\FormRequest;

class EstimateReplyDetailStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'reply_estimate_d' => ['required', 'date'],
            'reply_message' => ['required', 'string'],
            'decline_flag' => ['nullable', 'boolean'],
            'delete_flag' => ['nullable', 'boolean']
        ];
    }
}
