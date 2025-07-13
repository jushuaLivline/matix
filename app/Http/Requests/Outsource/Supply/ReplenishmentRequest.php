<?php

namespace App\Http\Requests\Outsource\Supply;
use Illuminate\Foundation\Http\FormRequest;

class ReplenishmentRequest extends FormRequest
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
          case 'outsource.supplyReplenishment.store':
            return $this->storeRule();
          case 'outsource.supplyReplenishment.storeData':
          case 'outsource.supplyReplenishment.update':
            return $this->updateRule();
          default:
            return [];
        }
        
    }
    public function storeRule ()
    {
        return [
            'supplier_code' => 'required|string', // Validate supplier_code as a required string
            'session_data' => 'required|array', // Ensure session_data is an array
            'session_data.*.id' => 'required|string', // Validate each item's 'id'
            'session_data.*.product_code' => 'required|string',
            'session_data.*.supply_classification' => 'required|string',
            'session_data.*.supplier_process_code' => 'nullable|string',
            'session_data.*.supply_date' => 'required|date_format:Ymd', // Validate YYYYMMDD format
            'session_data.*.supply_quantity' => 'required|numeric',
            'session_data.*.management_no' => 'nullable|numeric',
            'session_data.*.payment_classification' => 'required|string',
            'session_data.*.supply_flight_no' => 'required|string',
            'session_data.*.product_name' => 'nullable|string', // Nullable but must be a string if present
            'session_data.*.creator' => 'required|string',
            'session_data.*.created_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
    public function updateRule ()
    {
        return [
            'supplier_code' => 'required|numeric|exists:customers,customer_code', // Validate supplier_code as a required string
            'product_code' => 'required|string|exists:product_numbers,part_number', // Validate supplier_code as a required string
            'supply_classification' => 'nullable|string',
            'supply_date' => 'required|date_format:Ymd', // Validate YYYYMMDD format
            'supply_quantity' => 'required|numeric',
            'payment_classification' => 'required|string',
            'supply_flight_no' => 'required|numeric',
            'product_name' => 'nullable|string', // Nullable but must be a string if present
            'creator' => 'nullable|string',
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
            'supplier_code' => '支給先',
            'product_code' => '製品品番',
            'product_name' => '品名',
            'supply_date' => '支給日',
            'supply_quantity' => '数量',
            'payment_classification' => '有償/無償',
            'supply_flight_no' => '便',
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
            'numeric' => ':attributeは数値で入 力してください。',
            'integer' => ':attributeは整数で入力してください。',
            'date' => ':attributeは日付形式で入力してください。',
            'date_format' => '正しい形式で入力してください。', 
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