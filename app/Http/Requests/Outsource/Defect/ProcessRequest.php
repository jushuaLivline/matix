<?php

namespace App\Http\Requests\Outsource\Defect;
use Illuminate\Foundation\Http\FormRequest;

class ProcessRequest extends FormRequest
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
          case 'outsource.defect.process.store':
            return $this->storeRule();
          case 'outsource.defect.process.storeData':
          case 'outsource.defect.process.updateSession':
            return $this->updateRule();
          default:
            return [];
        }
       
    }
    public function storeRule()
    {
        return [
            'session_data.*.registration_no' => 'nullable|numeric', // Validate each item's 'id'
            'session_data.*.serial_number' => 'nullable|numeric',
            'session_data.*.process_code' => 'required|string|exists:processes,process_code',
            'session_data.*.product_code' => 'required|string|exists:product_numbers,part_number',
            'session_data.*.disposal_date' => 'required|date_format:Ymd',
            'session_data.*.part_number' => 'nullable|string',
            'session_data.*.quantity' => 'required|numeric',
            'session_data.*.slip_no' => 'required|numeric',
            'session_data.*.creator' => 'required',
            'session_data.*.created_at' => 'nullable|date_format:Y-m-d H:i:s',
        ];
    }
    public function updateRule()
    {
        return [
            'process_code' => 'required|exists:processes,process_code',
            'disposal_date' => 'required|date_format:Ymd',
            'product_code' => 'required|exists:product_numbers,part_number',
            'quantity' => 'required|numeric',
            'slip_no' => 'required|string|max:20',
            'creator' => 'nullable',
            'process_name' => 'nullable',
            'product_name' => 'nullable',
            'processing_unit_price' => 'nullable',
            'subTotal' => 'nullable',
            'creator' => 'nullable',
            'created_at' => 'nullable|date_format:Y-m-d H:i:s',
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
            'process_code' => '工程',
            'process_code' => '支給先',
            'product_code' => '製品品番',
            'quantity' => '数量',
            'disposal_date' => '廃却日',
            'slip_no' => '伝票No',
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