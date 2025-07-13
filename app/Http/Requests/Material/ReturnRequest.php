<?php

namespace App\Http\Requests\Material;
use Illuminate\Foundation\Http\FormRequest;

class ReturnRequest extends FormRequest
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
      $action = $this->route()->getName(); // Get the current route name
      switch ($action) {
        case 'material.returnCreate.store':
          return $this->createRule();
        case 'material.returnCreate.update':
          return $this->updateRule();
        default:
          return [];
      }
        
    }

    private function createRule()
    {
      return [
        'arrival_quantity' => 'nullable|array',
        'processing_rate' => 'nullable|array',
        'delivery_no' => 'nullable|array',
        'arrival_day' => 'nullable|string',
        'voucher_class' => 'nullable|array',
        'flight_no' => 'nullable|array',
        'supplier_code' => 'nullable|array',
        'department_code' => 'nullable|array',
        'line_code' => 'nullable|array',
        'product_number' => 'nullable|array',
        'material_manufacturer_code' => 'nullable|array',
        'material_no' => 'nullable|array',
        'creator' => 'nullable|array',
      ];
    }

    private function updateRule()
    {
      return [
          'arrival_day' => 'nullable|string',
          'delivery_no' => 'nullable|string',
          'arrival_quantity' => 'nullable|integer',
          'processing_rate' => 'nullable|string',
          'updator' => 'nullable',
          'updated_at' => 'nullable|string'
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
            'arrival_day' => '返却日',
            'arrival_quantity' => '数量',
            'delivery_no' => '伝票No.',
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
