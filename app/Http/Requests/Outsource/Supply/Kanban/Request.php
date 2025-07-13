<?php

namespace App\Http\Requests\Outsource\Supply\Kanban;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
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
            'subcontract_supply_no' => 'nullable|array',
            'management_no' => 'nullable|array',
            'branch_number' => 'nullable|array',
            'supplier_process_code' => 'nullable|array',
            'product_code' => 'nullable|array',
            'supply_classification' => 'nullable|array',
            'supply_date' => 'nullable|array',
            'supply_flight_no' => 'nullable|array',
            'lot' => 'nullable|array',
            'supply_kanban_quantity' => 'nullable|array',
            'supply_quantity' => 'nullable|array',
            'payment_classification' => 'nullable|array',
            'issuance_date' => 'nullable|array',
            'created_at' => 'nullable|array',
            'creator' => 'nullable|array',
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'required' => ':attributeは必須です。',
            'string' => ':attributeは文字列で入力してください。',
            'numeric' => ':attributeは数値で入力してください。',
            'integer' => ':attributeは整数で入力してください。',
            'date' => ':attributeは日付形式で入力してください。',
            'min' => [
                'numeric' => ':attributeは:min以上で入力してください。',
                'integer' => ':attributeは:min以上で入力してください。'
            ],
            'max' => ':attributeは:max文字以内で入力してください。',
            'in' => ':attributeは指定された値のいずれかである必要があります。'
        ];
    }
}