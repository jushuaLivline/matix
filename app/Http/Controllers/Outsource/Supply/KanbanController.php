<?php

namespace App\Http\Controllers\Outsource\Supply;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outsource\Supply\Kanban\Request as KanbanRequest;
use App\Models\KanbanMaster;
use App\Models\Outsource\SubcontractSupply;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KanbanController extends Controller
{
    public function __construct()
    {
        $this->kanban = new KanbanMaster();
        $this->subcontractSupply = new SubcontractSupply();
    }

    public function index(Request $request)
    {
        return redirect()->route('outsource.kanbanSupply.create');
    }

    public function create(Request $request)
    {
        $selectedManagementNos = array_map(
            fn($managementNo) => substr($managementNo, 0, 5),
            $request->input('management_no', [])
        );
        $request->merge(['kanban_classification' => 3]);
        $kanbanMasters = [];
        $subcontractSupplyNo = [];

        if (!empty($selectedManagementNos)) {
            $kanbanMasters = $this->kanban->supplyMaterialKanban($request, $selectedManagementNos);
            $subcontractSupplyNo = $this->subcontractSupply->generateSubcontractSupplyNo();
        }
        
        return view('pages.outsource.supply.kanban.create', compact('kanbanMasters', 'subcontractSupplyNo'));
    }
    public function store(KanbanRequest $request)
    {
        // Start the transaction
        DB::beginTransaction();

        try {
            $insertData = $this->convertArraysToAssociative($request->validated());
            $this->subcontractSupply->insert($insertData);

            // Commit the transaction if everything is successful
            DB::commit();

            return redirect()->route('outsource.kanbanSupply.create')->with('success', 'データは正常に登録されました');
        } catch (Exception $e) {
            // Rollback the transaction if something goes wrong
            DB::rollBack();
            // Return error response
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function convertArraysToAssociative($arrayFields)
    {
      // Ensure all fields are arrays
      $fields = array_map(fn($value) => (array) $value, $arrayFields);
  
      // Transpose the array to group values by index
      $structuredData = array_map(null, ...array_values($fields));
  
      // Convert grouped values into associative arrays
      $insertData = array_map(fn($values) => array_combine(array_keys($fields), $values), $structuredData);
  
      return $insertData;
    }
}
