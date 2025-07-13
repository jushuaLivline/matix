<?php

namespace App\Http\Requests\PurchaseOrderForm;

use Illuminate\Foundation\Http\FormRequest;

class OrderFormReissueRequest extends FormRequest
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
            'order_date_from' => 'nullable|date',
            'order_date_to' => 'nullable|date',
            'purchase_order_number' => 'nullable|string',
            'supplier_code' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'order_date_from.date' => '正しい形式で入力してください',
            'order_date_to.date' => '正しい形式で入力してください',
        ];
    }
}
