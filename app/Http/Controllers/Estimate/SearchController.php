<?php

namespace App\Http\Controllers\Estimate;

use App\Exports\Order\ForecastExport;
use App\Http\Controllers\Controller;
use App\Models\UnofficialNotice;
use App\Models\Estimate;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;


class SearchController extends Controller
{

  public function index(Request $request)
  {
    if(!request()->all()){
      $request->merge([
        'estimate_request_date_start' => now()->startOfMonth()->format('Ymd'),
        'estimate_request_date_end' => now()->endOfMonth()->format('Ymd'),
        'reply_due_date_start' => now()->startOfMonth()->format('Ymd'),
        'reply_due_date_end' => now()->endOfMonth()->format('Ymd'),
      ]);
    }
    
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $estimateSearchRecord = Estimate::search($request)->paginateResults($paginationThreshold);

    return view("pages.estimate.search.index", compact (
  'estimateSearchRecord',
    ));
  }
}
