<?php

namespace App\Http\Controllers\Material\Kanban;

use App\Http\Controllers\Controller as AppController;
use App\Http\Requests\Material\Kanban\Request as KanbanRequest;
use App\Models\Material\SupplyOrder;
use App\Models\KanbanMaster;


use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class Controller extends AppController
{
  protected $purchaseOrderFormService, $supplyOrder;
  public function __construct()
  {
    $this->supplyOrder = new SupplyOrder();
    $this->kanban = new KanbanMaster();
  }

  public function index(Request $request)
  {
    return redirect()->route('material.kanbanCreate.create');
  }
  //Material 19
  public function create(Request $request)
  {
    $selectedManagementNos = array_map(
      fn($managementNo) => substr($managementNo, 0, 5),
      $request->input('management_no', [])
    );
    $request->merge(['kanban_classification' => 1]);
    $kanbanMasters = [];
    $supplyMaterialOrderNo = [];

    if (!empty($selectedManagementNos)) {
      $kanbanMasters = $this->kanban->supplyMaterialKanban($request,$selectedManagementNos);
      $supplyMaterialOrderNo = $this->supplyOrder->generateSupplyMaterialOrderNo();
    }

    return view('pages.material.kanban.create', compact('kanbanMasters', 'supplyMaterialOrderNo'));
  }

  //Material 19 POST Form
  public function store(KanbanRequest $request)
  {
    // Start the transaction
    DB::beginTransaction();

    try {

      $insertData = $this->convertArraysToAssociative($request->validated());
      $this->supplyOrder->insert($insertData);
      // Commit the transaction if everything is successful
      DB::commit();

      return redirect()->route('material.kanbanCreate.create')->with('success', 'データは正常に登録されました');

    } catch (\Exception $e) {
      // Rollback the transaction if something goes wrong
      DB::rollBack();
      // Return error response
      return redirect()->back()->with('error', $e->getMessage());
    }
  }
  //Material 19 DELETE Form
  public function update($id)
  {
    // Start transaction
    DB::beginTransaction();

    try {
      $kanbanMaster = $this->kanban->removeKabanMaster($id);

      // Commit the transaction if everything is successful
      DB::commit();

      return response()->json($kanbanMaster, 200);

    } catch (\Exception $e) {
      // Rollback the transaction if there is an error
      DB::rollBack();
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

  public function ajaxCheckKanbanManagementNo(Request $request)
  {
    $barcode = $request->query('barcode'); // Get the barcode from the request

    if (!$barcode) {
      return response()->json(['error' => '管理No.が入力されていません。'], 400);
    }
    $request->merge(['kanban_classification' => 1]);
    $barcode = substr($barcode, 0, 5);
    $exists = KanbanMaster::existsByBarcode($request, $barcode);

    return response()->json(['exists' => $exists]);
  }

}
