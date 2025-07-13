<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchShipmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'due_date_start' => ['date', 'date_format:Ymd'],
            'due_date_end' => ['date', 'date_format:Ymd'],
        ];
    }
}
