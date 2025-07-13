<?php
namespace App\Services\Outsource\Supply;

use App\Models\SubcontractSupply;
use App\Models\KanbanMaster;
use App\Models\Line;
use Illuminate\Support\Facades\Auth;

class SubcontractSupplyService{
    public function edit($id){
        return SubcontractSupply::where('subcontract_supply_no', $id)->firstOrFail();
    }

    public function update($data, $id) {
        $subcontractSupply = SubcontractSupply::findOrFail($id);

        $subcontractSupply->update([
            'supply_kanban_quantity' => $data['supply_kanban_quantity'],
            'supply_quantity' => $data['supply_quantity'],
        ]);

        KanbanMaster::where('management_no', $subcontractSupply->management_no)
        ->update(['number_of_accomodated' => $data['number_of_accomodated']]);

        return $subcontractSupply;
    }
}