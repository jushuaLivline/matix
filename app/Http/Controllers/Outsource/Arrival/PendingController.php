<?php
namespace App\Http\Controllers\Outsource\Arrival;
use App\Http\Controllers\Controller;
use App\Exports\Outsource\Arrival\PendingExcelExport;

use App\Models\OutsourcedProcessing;
use App\Models\Process;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Constants\Constant;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PendingController extends Controller
{

  // Outsource processing 51
  public function index(Request $request)
  {
    $searchResults = [];

    if(!$request->all()) {
      $request->merge([
        'instruction_date_from' => now()->startOfMonth()->format('Ymd'),
        'instruction_date_to' => now()->endOfMonth()->format('Ymd'),
      ]);
    }
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    // Query the OutsourcedProcessing model
    $searchResults = OutsourcedProcessing::query()
      ->search($request)
      ->paginateResults($paginationThreshold);

      return view('pages.outsource.arrival.pending.index', compact('searchResults'));
  }

  // Outsource processing 51 Export
  public function excel_export(Request $request)
  {
    // Query the OutsourcedProcessing model
    $searchResults =  OutsourcedProcessing::query()
                    ->search($request)
                    ->paginateResults();

    $fileName = '未入荷一覧_' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new PendingExcelExport($searchResults), $fileName, \Maatwebsite\Excel\Excel::XLSX);


  }
}