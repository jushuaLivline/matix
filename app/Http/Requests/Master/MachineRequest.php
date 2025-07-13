<?php

namespace App\Http\Requests\Master;
use Illuminate\Foundation\Http\FormRequest;

class MachineRequest extends FormRequest
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
    public function rules()
    {
        return [
          'sign' => 'required|numeric',
          'machine_number' => 'required|numeric',
          'branch_number' => 'required|numeric',
          'machine_number_name' => 'required|string',
          'project_number' => 'nullable|string',
          'project_name' => 'nullable|string',
          'line_name' => 'nullable|string',
          'machine_division' => 'required|numeric',
          'manager' => 'nullable|string',
          'remarks' => 'nullable|string',
          'delete_flag' => 'nullable',
          'drawing_date' => 'nullable|date_format:Ymd',
          'completion_date' => 'nullable|date_format:Ymd',
          'updated_at' => 'nullable|date_format:Y-m-d H:i:s',
          'created_at' => 'nullable|date_format:Ymd',
          'creator' => 'nullable',
          'updator' => 'nullable',
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