<?php

namespace App\Http\Requests\Outsource;
use Illuminate\Foundation\Http\FormRequest;

class KanbanRequest extends FormRequest
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
            'order_no' => 'nullable|array',
            'management_no.*' => 'required|exists:kanban_masters,management_no',
            'branch_number' => 'nullable|array',
            'product_code' => 'nullable|array',
            'supplier_process_code' => 'nullable|array',
            'order_classification' => 'nullable|array',
            'instruction_date.*' => 'required|date_format:Ymd',
            'instruction_number' => 'nullable|array',
            'instruction_kanban_quantity' => 'nullable|array',
            'instruction_number.*' => 'required|string|min:1|max:2',
            'lot' => 'nullable|array',
            'arrival_number' => 'nullable|array',
            'arrival_day' => 'nullable|array',
            'incoming_flight_number' => 'nullable|array',
            'arrival_quantity' => 'nullable|array',
            'supplier_code' => 'nullable|array',
            'creator' => 'nullable|array',
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
            'management_no.*' => 'バーコード情報',
            'instruction_date.*' => '指示日',
            'instruction_number.*' => '便',
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