<?php

namespace App\Http\Controllers\Outsource;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outsource\KanbanRequest;
use App\Models\KanbanMaster;
use App\Models\OutsourcedProcessing;


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
use Illuminate\Support\Facades\Log;

class KanbanController extends Controller
{
  protected $purchaseOrderFormService;
  public function __construct()
  {
    $this->kanban = new KanbanMaster();
    $this->outsourcedProcessing = new OutsourcedProcessing();
  }

  public function index(Request $request)
  {
    return redirect()->route('outsource.kanbanCreate.create');
  }

  public function create(Request $request)
  {
    $selectedManagementNos = array_map(
      fn($managementNo) => substr($managementNo, 0, 5),
      $request->input('management_no', [])
    );

    $request->merge(['kanban_classification' => 2]);
    $outsourcedProcesses = [];
    $generateOrderNo = [];

    if (!empty($selectedManagementNos)) {
      $outsourcedProcesses = $this->kanban->supplyMaterialKanban($request, $selectedManagementNos);
      $generateOrderNo = $this->outsourcedProcessing->generateOrderNo();
    }

    return view('pages.outsource.kanban.create', compact(
      'outsourcedProcesses',
      'generateOrderNo'
    ));
  }

  public function store(KanbanRequest $request)
  {
    // Start the transaction
    DB::beginTransaction();
    try {
      $latestLotNumber = OutsourcedProcessing::max('lot');
      $lotCount = intval(substr($latestLotNumber, 4, 6));
      $insertData = collect($this->convertArraysToAssociative($request->validated()))
        ->map(function ($data, $index) use ($lotCount) {
          $data['lot'] = now()->format('md') . ($lotCount + 100 + ($index * 100));
          return $data;
        })
        ->toArray();
      $this->outsourcedProcessing->insert($insertData);
      // Commit the transaction if everything is successful
      DB::commit();

      return redirect()->route('outsource.kanbanCreate.create')->with('success', 'かんばん情報の登録が完了しました');

    } catch (\Exception $e) {
      // Rollback the transaction if something goes wrong
      DB::rollBack();
      // Log the error with detailed information
      Log::error('Error occurred while adding supply material  order.', [
        'error' => $e->getMessage(),
        'request' => $request->all(),
        'timestamp' => now(),
      ]);
      // Return error response
      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  public function update($id)
  {
    // Start transaction
    // DB::beginTransaction();

    try {
      // $kanbanMaster = $this->kanban->removeKabanMaster($id);
      // Commit the transaction if everything is successful
      // DB::commit();
      // return response()->json($kanbanMaster, 200);
      return true;

    } catch (\Exception $e) {
      // Rollback the transaction if there is an error
      // DB::rollBack();
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
    $request->merge(['kanban_classification' => $request->query('kanban_classification', 2)]);
    $barcode = substr($barcode, 0, 5);
    $exists = KanbanMaster::existsByBarcode($request, $barcode);
    return response()->json(['exists' => $exists]);
  }
}
