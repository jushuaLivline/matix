<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CacheShipmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'part_no' => ['required', 'string'],
            'part_name' => ['required', 'string'],
            'quantity' => ['required', 'numeric'],
            'remarks' => ['string', 'nullable'],
        ];
    }
}
