<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;

use App\Exports\Master\ProductNumberExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Code;
use App\Models\ProductNumber;
use App\Models\Configuration;
use App\Models\Process;
use App\Models\ProcessOrder;
use App\Models\ProcessUnitPrice;
use App\Models\ProductPrice;

use App\Constants\Constant;
use App\Http\Requests\Master\PartNumberRequest;
use App\Services\Master\PartService;



class PartController extends Controller
{
  protected $partService;

  public function __construct(PartService $partService)
  {
    $this->partService = $partService;
  }
  public function index(Request $request)
  {
    $excludeParams = ['page', 'line_name', 'department_name', 'customer_name', 'supplier_name'];

    //This is to remove null parameters
    $queryParams = collect($request->except($excludeParams))
      ->filter(function ($value) {
        return $value !== null && $value !== ''; // Allow '0' values to pass through
      })
      ->all();

    $query = ProductNumber::with('supplier', 'customer', 'department', 'line');
    if (!empty($queryParams)) {
      $query->where($queryParams);
    }

    $query->orderByDesc('created_at');
    $part_number_records = $query->paginateResults(10);

    return view('pages.master.part.index', compact('part_number_records'));
  }

  public function excel_export(Request $request)
  {
    $excludeParams = ['page', 'line_name', 'department_name', 'customer_name', 'supplier_name'];
    $queryParams = collect($request->except($excludeParams))->filter()->all();

    $query = ProductNumber::with('supplier', 'customer', 'department', 'line');

    if (!empty($queryParams)) {
        $query->where($queryParams);
    }
    
    // Paginate results to get only the current page
    $product_number_records = $query->paginateResults(10); 

    // Get only the records for the current page
    $currentPageRecords = collect($product_number_records->items());

    $exportData = $currentPageRecords->map(function ($product_number) {
        return [
            $product_number->part_number,
            $product_number->product_name,
            $product_number->line?->line_name,
            $product_number->department?->department_name,
            $product_number->customer?->supplier_name_abbreviation,
            $product_number->supplier?->supplier_name_abbreviation,
            $product_number->product_category,
        ];
    });

    $fileName = '品番マスタ一覧' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new ProductNumberExport($exportData), $fileName, \Maatwebsite\Excel\Excel::XLSX);
  }

  public function create(Request $request)
  {
    $codes = Code::where('division', '単位')->get();
    return view('pages.master.part.edit', compact('codes'));
  }

  public function store(PartNumberRequest $request)
  {
    try {
      DB::beginTransaction();

      $product = ProductNumber::create($request->validated());

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => '製品が正常に作成されました。',
        'product' => $product,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();

      Log::error('Product creation failed: ' . $e->getMessage());

      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function edit(Request $request, $id)
  {

    $data = $this->partService->getProductData($id);
    $part_number = $data->part_number ?? 0;
      $codes = $this->partService->getCodes();
      $product_prices = $this->partService->getLatestProductPrice($part_number);
      $insideProcess = $this->partService->getProcessPrices($part_number,1);
      $outsideProcess = $this->partService->getProcessPrices($part_number,2);
      $configurations = $this->partService->getConfigurations($part_number);

      return view('pages.master.part.edit', compact(
      'data',
      'codes',
      'configurations',
      'product_prices',
      'insideProcess',
      'outsideProcess'
    ));
  }


  public function update(PartNumberRequest $request, $id)
  {
    try {
      DB::beginTransaction();

      $product = ProductNumber::findOrFail($id);

      $product->update($request->validated());

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => '製品が正常に更新されました',
        'product' => $product,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Product creation failed: ' . $e->getMessage());

      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function destroy(PartNumberRequest $request, $id)
  {
    try {
      DB::beginTransaction();

      $product = ProductNumber::findOrFail($id);
      $product->update($request->validated());

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => '製品が正常に削除されました',
        'product' => $product,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Product creation failed: ' . $e->getMessage());

      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(),
      ], 500);
    }
  }
}

