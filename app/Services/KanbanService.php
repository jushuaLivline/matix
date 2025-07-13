<?php
namespace App\Services;

use App\Exports\CustomersExport;
use App\Models\KanbanMaster;
use Illuminate\Support\Facades\Auth;

class KanbanService{
    public function createKanban($data)
    {
        $data['creator'] = Auth::user()->id;

        unset($data['product_name']);
        unset($data['process_name']);
        unset($data['next_process_name']);

        return KanbanMaster::create($data);
    }

    public function updateKanban($data, $id)
    {
        $kanban = KanbanMaster::find($id);
        $fieldsToUpdate = $data->except(['_token', 'product_name', 'process_name', 'next_process_name']);

        if (isset($fieldsToUpdate['delete_flag'])) {
            $kanban->delete_flag = $fieldsToUpdate['delete_flag'] ? 1 : 0;
        } else {
            $kanban->delete_flag = 0; // Assuming unchecked checkbox means value should be 0
        }

        foreach ($fieldsToUpdate as $field => $value) {
            if ($kanban->$field != $value) {
                $kanban->$field = $value;
            }
        }

        $kanban->updator = Auth::user()->id;

        $updated = $kanban->save();

        return $updated;
    }

    public function searchQuery($data)
    {
        $management_no = $data->input('management_no');
        $part_number = $data->input('part_number');
        $kanban_classification = $data->input('kanban_classification');
        $delete_flag = $data->input('delete_flag');

        $query = KanbanMaster::query();

        if ($management_no) {
            $query->where('management_no', $management_no);
        }

        if ($part_number) {
            $query->where('part_number', $part_number);
        }

        if ($kanban_classification) {
            $query->where('kanban_classification', $kanban_classification);
        }

        if (in_array($delete_flag, ['0', '1'])) {
            if ($delete_flag == 'すべて') {
                $query->whereIn('delete_flag',  ['0','1']);
            } else {
                $query->where('delete_flag',  $delete_flag);
            }
        }

        return $query->select('management_no', 'part_number', 'kanban_classification')->get();
    }

    public function kanbanErrorMessage ()
    {
        return [
            'management_no.required' => 'The management number field is required.',
            'management_no.numeric' => 'The management number must be a numeric value.',
            'management_no.unique' => 'The management number has already been taken.',
            'kanban_classification.required' => 'The kanban classification field is required.',
            'part_number.required' => 'The part number field is required.',
            'number_of_accomodated.required' => 'The number of accommodated field is required.',
            'number_of_accomodated.numeric' => 'The number of accommodated must be a numeric value.',
        ];
    }
}