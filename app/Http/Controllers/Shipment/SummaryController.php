<?php

namespace App\Http\Controllers\Shipment;

use App\Exports\Order\ForecastExport;
use App\Http\Controllers\Controller;
use App\Models\UnofficialNotice;
use App\Models\ShipmentRecord;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;
use App\Services\Shipment\SummaryService;
use App\Exports\Shipment\SummaryExport;


class SummaryController extends Controller
{
  
  protected $summaryService;

  public function __construct(SummaryService $summaryService )
  {
    $this->summaryService = $summaryService;
  }

  public function index(Request $request)
  {
    if(!request()->all()){
      $request->merge([
        'category' => '1',
        'due_date_from' => now()->startOfMonth()->format('Ymd'),
        'due_date_to' => now()->endOfMonth()->format('Ymd'),
      ]);
    }
    
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $shipmentRecord = $this->summaryService->getSummaryRecord($request)->paginateResults($paginationThreshold);

    return view("pages.shipment.summary.index", compact (
  'shipmentRecord',
    ));
  }

  public function destroy($id)
  {

  }
  
  public function excel_export(Request $request)
  {
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $shipmentRecord = $this->summaryService->getSummaryRecord($request)->paginateResults($paginationThreshold);
    
    if($shipmentRecord->isEmpty())
    {
      return redirect()
      ->route('shipment.shipmentSummary.index', $request->query())
      ->with('error', '記録が見つかりませんでした。');
    }

    $fileName = '出荷実績集計_' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new SummaryExport($shipmentRecord), $fileName, \Maatwebsite\Excel\Excel::XLSX);
  }
}
