<?php
namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

    public function rules()
    {
        $employee_code = $this->route('employee'); 

        return [
            'employee_code' => [
                'required',
                'string',
                Rule::unique('employees', 'employee_code')->ignore($employee_code),
            ],
            'employee_name' => ['required', 'string'],
            'department_code' => [
                'nullable',
                'string',
                'exists:departments,code' // if not null, it must exist
            ],
            'password' => ['required', 'string'],
            'mail_address' => [
                'required', 
                'email', 
                Rule::unique('employees', 'mail_address')->ignore($employee_code),
            ],
            'authorization_code' => ['required', 'string'],
            'delete_flag' => ['numeric', 'nullable'],
            'purchasing_approval_request_email_notification_flag' => ['numeric', 'nullable'],
        ];
    }

    protected function prepareForValidation()
    {
        // Set 'delete_flag' to 1 if it is null
        if (is_null($this->delete_flag)) {
            $this->merge(['delete_flag' => 1]);
        }
    }

    public function messages()
    {
        return [
            'employee_code.required' => '社員コードは必須です',
            'employee_code.unique' => 'この従業員コードは既に存在します',
            'employee_name.required' => '社員名は必須です',

            'department_code.required' => '部門は必須です',
            'department_code.exists' => '部門が存在しません',

            'password.required' => 'パスワードは必須です',
            'mail_address.required' => 'メールアドレスは必須です',
            'mail_address.email' => '正しい形式で入力してください',
            'mail_address.unique' => 'このメールアドレスは既に使用されています。',
            'authorization_code.required' => '認証コードが必要です',
        ];
    }
}
