<?php
namespace App\Services;

use App\Exports\CustomersExport;
use App\Models\Line;
use Illuminate\Support\Facades\Auth;

class LineService{
    public function createLine($data)
    {
        $data['creator'] = Auth::user()->id;
        return Line::create($data);
    }

    public function updateLine($data, $id)
    {
        $line = Line::find($id);
        $fieldsToUpdate = $data->except(['_token', 'department_name']);

        if (isset($fieldsToUpdate['delete_flag'])) {
            $line->delete_flag = $fieldsToUpdate['delete_flag'] ? 1 : 0;
        } else {
            $line->delete_flag = 0; // Assuming unchecked checkbox means value should be 0
        }

        foreach ($fieldsToUpdate as $field => $value) {
            if ($line->$field != $value) {
                $line->$field = $value;
            }
        }

        $line->updator = Auth::user()->id;

        $updated = $line->save();

        return $updated;
    }

    public function searchQuery($data)
    {
        $line_code = $data->input('line_code');
        // $line_name = $data->input('line_name');
        $department_code = $data->input('department_code');
        $delete_flag = $data->input('delete_flag');

        $query = Line::query();

        if ($line_code) {
            $query->where('line_code', $line_code);
        }

        // if ($line_name) {
        //     $query->where('line_name', 'LIKE', '%'. $line_name .'%');
        // }

        if ($department_code) {
            $query->where('department_code',  $department_code );
        }

        if ($delete_flag) {
            // if ($delete_flag == 'すべて') {
            //     $query->whereIn('delete_flag',  ['0','1']);
            // } else {
                $query->where('delete_flag',  $delete_flag);
            // }
        }

        return $query->select('line_code', 'line_name', 'department_code')->get();
    }

    public function lineErrorMessage ()
    {
        return [
            'line_code.required' => 'The line code is required.',
            'line_code.numeric' => 'The line code must be a numeric value.',
            'line_code.unique' => 'The line code has already been taken.',
            'line_name.required' => 'The line name is required.',
            'line_name_abbreviation.required' => 'The line name abbreviation is required.',
            'department_code.required' => 'The department code is required.',
            'department_code.numeric' => 'The department code must be a numeric value.',
            'department_code.exists_in_departments' => 'The department code does not exist in the Departments Table.',
        ];
    }
}