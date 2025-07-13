<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\Department;
use App\Models\SupplyMaterialArrival;

use App\Services\Material\ReturnSummaryService;
use App\Exports\Material\ReturnSummaryExcelExport;


use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use App\Helpers\RequestHelper;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;


class ReturnSummaryController extends Controller
{
  protected $returnSummaryService;

  public function __construct(ReturnSummaryService $returnSummaryService)
  {
    $this->returnSummaryService = $returnSummaryService;
  }

  //Material 29
  function index(Request $request)
  {
    if (!$request->all()) {
      $request->merge([
        'category'=> 'division',
        'return_date_start'=>now()->startOfMonth()->format('Ymd'),
        'return_date_end'=> now()->endOfMonth()->format('Ymd'),
      ]);
    } 
  
    RequestHelper::processRequest($request);
    $results = $this->returnSummaryService->getData($request);

    $totalArrivalQuantity = $results->sum('max_arrival_quantity');
    $totalProductPrice = $results->sum('product_price');
    $totalProcessingPrice = $results->sum('processing_price');
    $totalProcessingRate =  $results->sum('max_processing_rate');
    $grandTotal = $results->sum('total_amount');

    return view("pages.material.return.summary.index", compact(
      'results',
      'totalArrivalQuantity',
      'totalProductPrice',
      'totalProcessingPrice',
      'totalProcessingRate',
      'grandTotal'
    ));
  }

  //Material 29
  function excel_export(Request $request)
  {
    $results = $this->returnSummaryService->excel_export($request);

    // dd($results);
    $fileName = '内示集計_' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new ReturnSummaryExcelExport($results,$request), $fileName, \Maatwebsite\Excel\Excel::XLSX);
  }
}

