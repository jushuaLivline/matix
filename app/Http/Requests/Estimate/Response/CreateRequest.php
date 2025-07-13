<?php

namespace App\Http\Requests\Estimate\Response;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class CreateRequest extends FormRequest
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
      case 'estimate.estimateResponse.store':
      case 'estimate.estimateResponse.update':
        return $this->storeRules();
      case 'estimate.estimateResponseStoreTempFile.store_temp_file':
        return $this->temporaryFileRules();
      default:
        return [];
    }
  }

  private function storeRules()
  {
    return [
      'uploaded_files' => 'array|min:1', // File is not required
      'estimate_id' => 'required|integer',
      'estimate_reply_date' => 'required|date_format:Ymd',
      'uploaded_files.*' => 'string', // Assuming you store file paths as strings
      'monthly_standard_amount' => 'nullable|array',
      'employee_code' => 'nullable|string',
      'reply_content' => 'nullable|string',
      'decline_flag' => 'nullable|integer',
      'created_user' => 'nullable|integer',
      'creator' => 'nullable|string',
      'updator' => 'nullable|string',
      'created_at' => 'nullable|date_format:Y-m-d H:i:s',
      'updated_at' => 'nullable|date_format:Y-m-d H:i:s',
      'file' => 'nullable|file|max:10240|mimes:xlsx,xls,docx,doc,pptx,ppt,pdf,jpg,gif,png',
    ];
  }

  private function temporaryFileRules()
  {
    return [
      'file' => 'file|max:10240|mimes:xlsx,xls,docx,doc,pptx,ppt,pdf,jpg,gif,png', // File is not required
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
      'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
        'file' => ':attributeのサイズは10MB以下である必要があります。' // Custom message for file size limit
      ],
      'in' => ':attributeは指定された値のいずれかである必要があります。',
      'mimes' => [
        'file' => ':attributeは無効なファイル形式です。許可されている形式: xlsx,xls,docx,doc,pptx,ppt,pdf,jpg,gif,png' // Custom message for file size limit
      ],
    ];
  }



  public function getEstimateReplyData()
  {
    $action = $this->route()->getName(); // Get the current route name
    $is_update = ($action == 'estimate.estimateResponse.update') ? 1 : 0;
    $data = [
      'decline_flag' => $this->input('decline_flag', 0),
      'reply_content' => $this->input('reply_content'),
      'employee_code' => $this->input('employee_code'),
      'estimate_reply_date' => \Carbon\Carbon::createFromFormat('Ymd', $this->input('estimate_reply_date'))->format('Y-m-d'),
    ];

    if ($is_update) {
      $data['updated_at'] = now()->format('Y-m-d H:i:s');
      $data['updator'] = $this->input('creator');
    } else {
      $data['estimate_id'] = $this->input('estimate_id');
      $data['created_at'] = now()->format('Y-m-d H:i:s');
      $data['creator'] = $this->input('creator');
    }

    return $data;
  }

  public function getEstimateReplyDetailsData()
  {
    $action = $this->route()->getName(); // Get the current route name
    $is_update = ($action == 'estimate.estimateResponse.update') ? 1 : 0;
    $monthlyAmounts = $this->input('monthly_standard_amount', []);
    $uploadedFiles = $this->input('uploaded_files', []);
    $estimateReplyDetailIds = $this->input('estimate_reply_detail_ids', []);


    return collect($monthlyAmounts)->map(function ($amount, $index) use ($uploadedFiles, $is_update, $estimateReplyDetailIds) {
      $data = [
        'monthly_standard_amount' => $amount,
        'estimate_id' => $this->input('estimate_id'),
      ];

      if (!empty($uploadedFiles[$index])) {
        $data['attachment_file'] = $uploadedFiles[$index];
      }

      if ($is_update && !empty($estimateReplyDetailIds[$index])) {
        $data['id'] = $estimateReplyDetailIds[$index];
        $data['updated_at'] = now()->format('Y-m-d H:i:s');
        $data['updated_user'] = $this->input('creator');
      } else {
        $data['created_at'] = now()->format('Y-m-d H:i:s');
        $data['created_user'] = $this->input('creator');
      }

      return $data;

    })->toArray();
  }
}