<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\Material\SupplyArrival;
use App\Models\Configuration;


use App\Services\Material\ReturnService;
use App\Http\Requests\Material\ReturnRequest;
use App\Exports\Material\ReturnExcelExport;

use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ReturnController extends Controller
{
  protected $returnService;
  public function __construct(ReturnService $returnService)
  {
    $this->supplyMaterialArrival = new SupplyArrival();
    $this->productNumber = new ProductNumber();
    $this->configuration = new Configuration();
    $this->returnService = $returnService;
  }
  
  public function index(Request $request)
  {
    if (!$request->all()) {
      $request->merge([
        'voucher_class' => 1,
        'arrival_day_from' => Carbon::now()->startOfMonth()->format('Ymd'),
        'arrival_day_to' => Carbon::now()->endOfMonth()->format('Ymd'),
      ]);
    }
    $supplyArrivals = SupplyArrival::filterReturnRecords($request);
    $supplyArrivals = $supplyArrivals->PaginateResults(20);
    return view('pages.material.return.index', compact('supplyArrivals'));
  }
  public function create(Request $request)
  {
    $productNumber = $request->part_number;
    
    // Fetch data using model methods
    $product = $this->productNumber->getByPartNumber($productNumber);
    $materials = $this->configuration->where('parent_part_number', $productNumber)->paginate();
    $supplyMaterialArrival = $this->supplyMaterialArrival->with('product', 'configuration')->where('id',$request->arrivalId)->first();
    $requestData = $request->all();
    
    // Fetch child part numbers & merge material classification
    $productMaterials = $this->productNumber->getWithMaterialClassification($materials);

    return view('pages.material.return.create', compact(
        'productMaterials',
        'requestData',
        'supplyMaterialArrival',
    ));
  }

  public function store(ReturnRequest $request)
  {
    // Initialize database transaction for creating new records
    DB::beginTransaction();

    try {
      // Create a new record in mst_supply_material_orders table
      $this->returnService->store($request->validated());

      // Commit transaction on success
      DB::commit();
      return redirect()->route('material.returnCreate.create')->with('success', '返品情報の登録が完了しました');

    } catch (\Exception $e) {
      // Log the error with detailed information
      Log::error('Error occurred while submitting the form', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function update(ReturnRequest $request, $id)
  {
    // Initialize database transaction for creating new records
    DB::beginTransaction();
    try {
        $this->returnService->update($request->validated(), $id);
        // Commit transaction on success
        DB::commit();
        return redirect()->route('material.returnCreate.index')->with('success', '返品実績情報の更新が完了しました');

    } catch (\Exception $e) {
      // Log the error with detailed information
      Log::error('Error occurred while submitting the form', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }
  
  public function excel_export(Request $request)
  {
    if (!$request->all()) {
      $request->merge([
        'voucher_class' => 1,
        'arrival_day_from' => Carbon::now()->startOfMonth()->format('Ymd'),
        'arrival_day_to' => Carbon::now()->endOfMonth()->format('Ymd'),
      ]);
    }
    $supplyArrivals = SupplyArrival::filterReturnRecords($request)->paginateResults();

    $fileName = '支給材返品実績_' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new ReturnExcelExport($supplyArrivals), $fileName, \Maatwebsite\Excel\Excel::XLSX);

  }
}
