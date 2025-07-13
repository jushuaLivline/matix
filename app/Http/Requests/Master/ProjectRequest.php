<?php
namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

    public function rules()
    {
        $projectId = $this->route('project'); 

        return [
            'project_number' => [
                'required',
                'max:8',
                Rule::unique('projects', 'project_number')->ignore($projectId),
            ],
            'project_name' => ['required', 'string', 'max:50'],
            'delete_flag' => ['numeric', 'nullable'],
        ];
    }

    protected function prepareForValidation()
    {
        // Set to "1" if no value is given
        $this->merge([
            'delete_flag' => $this->input('delete_flag', '1'),
        ]);
    }


    public function messages ()
    {
        return [
            // project_number messages
            'project_number.required' => 'プロジェクトNoは必須です',
            'project_number.unique' => 'このプロジェクト番号はすでに使用されています',
            'project_number.max' => 'プロジェクト番号を文字数以内で入力してください',

            // project_name messages
            'project_name.required' => 'プロジェクト名は必須です',
            'project_name.max' => 'プロジェクト名は 文字数以内で入力してください',
        ];
    }
}
