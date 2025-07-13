<?php

namespace App\Http\Requests\Material\Setting;
use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
          case 'material.settingGroup.store':
              return $this->storeRule();
          case 'material.settingGroup.update':
              return $this->updateRule();
          default:
              return [];
      }
    }

    private function storeRule()
    {
      return [
        'part_number' => 'required|string',
        'supply_material_group' => 'required|string',
        'creator' => 'nullable|string',
        'updated_at' => 'nullable|string',
      ];
    }

    private function updateRule()
    {
      return [
        'supply_material_group' => 'required|string',
        'updator' => 'nullable|string',
        'updated_at' => 'nullable|string',
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
            'part_number' => 'グループ'
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