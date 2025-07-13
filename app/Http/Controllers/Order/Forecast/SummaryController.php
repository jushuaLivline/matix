<?php

namespace App\Http\Controllers\Order\Forecast;

use App\Services\Order\Forecast\SummaryService;

use App\Exports\Order\Forecast\SummaryExport;
use App\Http\Controllers\Controller;
use App\Models\UnofficialNotice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;

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
        'year_and_month' => now()->format('Ym'),
        'category' => 'department'
      ]);
    }

    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $unofficialRecords = $this->summaryService->getSummaryRecord($request)->paginateResults($paginationThreshold);

    return view("pages.order.forecast.summary.index", compact (
      'unofficialRecords',
    ));
  }

  public function excel_export(Request $request)
  {
    if(!request()->all()) {
      $request->merge([
        'year_and_month' => now()->format('Ym'),
        'category' => 'department'
      ]);
    }

    $unofficialNotices = $this->summaryService->getSummaryRecord($request)->get();
    if($unofficialNotices->isEmpty())
    {
      return redirect()
        ->route('order.forecastSummary.index', $request->query())
        ->with('error', '記録が見つかりませんでした。');
    }
    $fileName = '内示集計_' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new SummaryExport($unofficialNotices), $fileName, \Maatwebsite\Excel\Excel::XLSX);
  }
}
