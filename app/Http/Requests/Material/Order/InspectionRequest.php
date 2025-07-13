<?php

namespace App\Http\Requests\Material\Order;
use Illuminate\Foundation\Http\FormRequest;

class InspectionRequest extends FormRequest
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

    public function rules(): array
    {
        $action = $this->route()->getName();
        switch ($action) {
            case 'material.order.inspections.storeData':
            case 'material.order.inspections.update':
                return $this->storeRules();
            default:
                return [];
        }
        
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function storeRules()
    {
        return [
          'supply_material_receipt_no' => 'nullable|string',
          'serial_number' => 'nullable|string',
          'delivery_no' => 'required|integer',
          'voucher_class' => 'nullable|string',
          'arrival_day' => 'required|date_format:Ymd',
          'flight_no' => 'required|integer|min:1',
          'supplier_code' => 'nullable|string',
          'product_number' => 'nullable|string',
          'material_manufacturer_code' => 'nullable|string',
          'material_no' => 'required|string|exists:product_numbers,part_number',
          'arrival_quantity' => 'required|integer',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'arrival_day' => '納入日',
            'flight_no' => '便No.',
            'delivery_no' => '納入番号',
            'material_no' => '材料品番',
            'arrival_quantity' => '納入数',
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
            'date_format' => ':attributeは正しい形式で入力してください。', 
            'min' => [
                'numeric' => ':attributeは:min以上で入力してください。',
                'integer' => ':attributeは:min以上で入力してください。'
            ],
            'exists' => ':attributeが存在しません。',
            'max' => ':attributeは:max文字以内で入力してください。',
            'in' => ':attributeは指定された値のいずれかである必要があります。'
        ];
    }
}