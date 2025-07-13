<?php

namespace App\Http\Requests\Order;
use Illuminate\Foundation\Http\FormRequest;

class ForecastRequest extends FormRequest
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
        'day_1' => 'nullable|numeric',
        'day_1' => 'nullable|numeric',
        'day_2' => 'nullable|numeric',
        'day_3' => 'nullable|numeric',
        'day_4' => 'nullable|numeric',
        'day_5' => 'nullable|numeric',
        'day_6' => 'nullable|numeric',
        'day_7' => 'nullable|numeric',
        'day_8' => 'nullable|numeric',
        'day_9' => 'nullable|numeric',
        'day_10' => 'nullable|numeric',
        'day_11' => 'nullable|numeric',
        'day_12' => 'nullable|numeric',
        'day_13' => 'nullable|numeric',
        'day_14' => 'nullable|numeric',
        'day_15' => 'nullable|numeric',
        'day_16' => 'nullable|numeric',
        'day_17' => 'nullable|numeric',
        'day_18' => 'nullable|numeric',
        'day_19' => 'nullable|numeric',
        'day_20' => 'nullable|numeric',
        'day_21' => 'nullable|numeric',
        'day_22' => 'nullable|numeric',
        'day_23' => 'nullable|numeric',
        'day_24' => 'nullable|numeric',
        'day_25' => 'nullable|numeric',
        'day_26' => 'nullable|numeric',
        'day_27' => 'nullable|numeric',
        'day_28' => 'nullable|numeric',
        'day_29' => 'nullable|numeric',
        'day_30' => 'nullable|numeric',
        'day_31' => 'nullable|numeric',
        'current_month' => 'nullable|numeric',
        'next_month' => 'nullable|numeric',
        'two_months_later' => 'nullable|numeric',
        'updated_at' => 'nullable|date',
        'updator' => 'nullable|string',
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
