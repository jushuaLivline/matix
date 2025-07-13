<?php

namespace App\Http\Requests\Outsource\Defect;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class MaterialRequest extends FormRequest
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
            'return_date' => 'required|date_format:Ymd',
            'process_code' => 'required|string',
            'product_number' => 'nullable|string',
            'product_code' => 'nullable|string',   // field not exist in db | unset in controller
            'slip_no' => 'nullable|string',
            'reason_code' => 'nullable|numeric',
            'quantity' => 'nullable|numeric',
            'processing_rate' => 'nullable|numeric',
            'updator' => 'nullable',
            'updated_at' => 'nullable|date_format:Y-m-d H:i:s',
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