<?php

namespace App\Http\Controllers\Outsource;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outsource\Inspection\Request as KanbanRequest;
use App\Models\KanbanMaster;
use App\Models\OutsourcedProcessing;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InspectionController extends Controller
{
    public function __construct()
    {
        $this->kanban = new KanbanMaster();
        $this->outsourcedProcessing = new OutsourcedProcessing();
    }
    public function index(Request $request)
    {
        return redirect()->route('outsource.inspectionCreate.create');
    }

    public function create(Request $request)
    {
        $selectedManagementNos = array_map(
            fn($managementNo) => substr($managementNo, 0, 5),
            $request->input('management_no', [])
        );
        $request->merge(['kanban_classification' => 2]);
        $kanbanMasters = [];
        $orderNo = [];

        if (!empty($selectedManagementNos)) {
            $kanbanMasters = $this->kanban->supplyMaterialKanban($request, $selectedManagementNos);
            $orderNo = OutsourcedProcessing::generateOrderNo();
        }

        return view('pages.outsource.inspection.create', compact('kanbanMasters', 'orderNo'));
    }

    public function store(KanbanRequest $request)
    {
        // Start the transaction
        DB::beginTransaction();

        try {
            $insertData = $this->convertArraysToAssociative($request->validated());
            $this->outsourcedProcessing->insert($insertData);

            // Commit the transaction if everything is successful
            DB::commit();

            return redirect()->route('outsource.inspectionCreate.create')->with('success', 'データは正常に登録されました');
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
