<?php

namespace App\Http\Requests\Estimate\Request;
use Illuminate\Foundation\Http\FormRequest;

class RequestCreate extends FormRequest
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
      $action = $this->route()->getName();
  
      switch ($action) {
        case 'estimate.requestCreate.store':
          return $this->storeRules();
        case 'estimate.requestCreateStoreFile':
          return $this->storeFileRules();
        default:
          return [];
      }
    }

    public function storeRules()
    {
        return [
            'customer_code' => 'nullable',
            'customer_contact_person' => 'nullable',
            'estimate_request_date' => 'nullable',
            'reply_due_date' => 'nullable',
            'base_product_code' => 'nullable',
            'product_code' => 'nullable',
            'part_name' => 'nullable',
            'model_type' => 'nullable',
            'monthly_standard_amount' => 'nullable',
            'sop' => 'nullable',
            'request_content' => 'nullable',
            'attachment_file' => 'nullable',
            'delete_flag' => 'nullable',
            'created_at' => 'nullable',
            'creator' => 'nullable',
        ];
    }

    private function storeFileRules()
    {
        return [
            'file' => 'required|file|max:10240|mimes:xlsx,xls,docx,doc,pptx,ppt,pdf,jpg,gif,png',
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