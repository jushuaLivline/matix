<?php
namespace App\Services;

use App\Exports\CustomersExport;
use App\Models\Department;
use App\Models\Line;
use Illuminate\Support\Facades\Auth;

class DepartmentService{
    public function createDepartment($data)
    {
        $data['creator'] = Auth::user()->id;
        return Department::create($data);
    }

    public function updateDepartment($data, $id)
    {
        $department = Department::find($id);
        $fieldsToUpdate = $data->except(['_token']);

        if (isset($fieldsToUpdate['delete_flag'])) {
            $department->delete_flag = $fieldsToUpdate['delete_flag'] ? 1 : 0;
        } else {
            $department->delete_flag = 0; // Assuming unchecked checkbox means value should be 0
        }

        foreach ($fieldsToUpdate as $field => $value) {
            if ($department->$field != $value) {
                $department->$field = $value;
            }
        }

        $department->updator_code = Auth::user()->id;

        $updated = $department->save();

        return $updated;
    }

    public function searchQuery($data)
    {
        $code = $data->input('code');
        $name = $data->input('name');
        $name_abbreviation = $data->input('name_abbreviation');
        $department_name = $data->input('department_name');
        $section_name = $data->input('section_name');
        $group_name = $data->input('group_name');
        $delete_flag = $data->input('delete_flag');

        $query = Department::query();

        if ($code) {
            $query->where('code', $code);
        }

        if ($name) {
            $query->where('name', $name);
        }

        if ($name_abbreviation) {
            $query->where('name_abbreviation', $name_abbreviation);
        }
        
        if ($department_name) {
            $query->where('department_name', $department_name);
        }

        if ($section_name) {
            $query->where('section_name', $section_name);
        }

        if ($group_name) {
            $query->where('group_name', $group_name);
        }

        if (in_array($delete_flag, ['0', '1'])) {
            if ($delete_flag == 'すべて') {
                $query->whereIn('delete_flag',  ['0','1']);
            } else {
                $query->where('delete_flag',  $delete_flag);
            }
        }

        return $query->select('code',
                              'name',
                              'name_abbreviation',
                              'department_name',
                              'section_name',
                              'group_name',
                              'delete_flag')->get();
    }

    public function departmentErrorMessage ()
    {
        return [
            'code.required' => 'The code field is required.',
            'code.max' => 'The code must not exceed 6 characters.',
            'name.required' => 'The name field is required.',
            'name.max' => 'The name must not exceed 60 characters.',
            'name_abbreviation.required' => 'The name abbreviation field is required.',
            'name_abbreviation.max' => 'The name abbreviation must not exceed 40 characters.',
            'department_name.max' => 'The department name must not exceed 40 characters.',
            'section_name.max' => 'The section name must not exceed 40 characters.',
            'group_name.max' => 'The group name must not exceed 40 characters.',
        ];
    }
}