<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentRecordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'due_date' => ['required', 'date', 'date_format:Y-m-d'],
            'delivery_no' => ['required', 'string'],
            'delivery_destination_code' => ['required', 'string'],
            'slip_no' => ['required', 'string'],
            'acceptance' => ['string', 'nullable'],
            'drop_ship_code' => ['string', 'nullable'],
            'product_no' => ['required', 'string'],
            'quantity' => ['required', 'numeric'],
            'department_code' => ['string', 'nullable'],
            'remarks' => ['string', 'nullable'],
        ];
    }
}
