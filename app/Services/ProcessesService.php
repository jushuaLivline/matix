<?php
namespace App\Services;

use App\Models\Department;
use App\Models\Process;
use Illuminate\Support\Facades\Auth;

class ProcessesService{
    public function createProcess($data)
    {
        $data['creator'] = Auth::user()->id;
        return Process::create($data);
    }

    public function updateProcess($data, $id)
    {
        $process = Process::find($id);
        $fieldsToUpdate = $data->except(['_token']);

        if (isset($fieldsToUpdate['delete_flag'])) {
            $process->delete_flag = $fieldsToUpdate['delete_flag'] ? 1 : 0;
        } else {
            $process->delete_flag = 0; // Assuming unchecked checkbox means value should be 0
        }

        foreach ($fieldsToUpdate as $field => $value) {
            if ($process->$field != $value) {
                $process->$field = $value;
            }
        }

        $process->updator = Auth::user()->id;

        $updated = $process->save();

        return $updated;
    }

    public function searchQuery($data)
    {
        $process_code = $data->input('process_code');
        $process_name = $data->input('process_name');
        $inside_and_outside_division = $data->input('inside_and_outside_division');
        $delete_flag = $data->input('delete_flag');

        $query = Process::query();

        if ($process_code) {
            $query->where('process_code', $process_code);
        }

        if ($process_name) {
            $query->where('process_name', $process_name);
        }

        if ($inside_and_outside_division) {
            $query->where('inside_and_outside_division', $inside_and_outside_division);
        }

        if (in_array($delete_flag, ['0', '1'])) {
            $query->where('delete_flag',  $delete_flag);
        }

        return $query->select('process_code',
                              'process_name',
                              'abbreviation_process_name',
                              'inside_and_outside_division',
                              'customer_code',
                              'backorder_days')
                              ->get();
    }

    public function processErrorMessage ()
    {
        return [
            'process_code.required' => 'The process_code field is required.',
            'process_name.required' => 'The process_name field is required.',
            'abbreviation_process_name.required' => 'The abbreviation_process_name field is required.',
            'inside_and_outside_division.required' => 'The inside_and_outside_division field is required.',
        ];
    }
}