<?php

namespace App\Http\Controllers\Order;

use App\Exports\Order\ForecastExport;
use App\Http\Controllers\Controller;
use App\Models\UnofficialNotice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;
use App\Http\Requests\Order\ForecastRequest;


class ForecastController extends Controller
{
  public function index(Request $request)
  {
    if(!request()->all()){
      $request->merge([
        'year_and_month' => now()->format('Ym'),
      ]);
    }
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $unofficialRecords = UnofficialNotice::search($request)->paginateResults($paginationThreshold);

    $dataCustomer = [];
    return view("pages.order.forecast.index", compact (
      'unofficialRecords',
    ));
  }

  public function show(Request $request, $id)
  {
    $request->merge([
      'id' => $id, 
      'get_sum_current_month' => 1
    ]);
    $unofficialRecord = UnofficialNotice::search($request)->first();
    $daysInThreeMonths = 0;
    $startMonth = 0; 

    $startMonth = ($request->year_and_month) 
                      ? Carbon::createFromFormat('Ym', $request->year_and_month)
                      : Carbon::createFromFormat('Ym',$unofficialRecord->year_and_month);
    for ($i = 0; $i < 3; $i++) {
      $month = $startMonth->copy()->addMonths($i);
      $daysInThreeMonths += $month->daysInMonth;
    }

    if(!$unofficialRecord) {
      return redirect()
        ->route('order.forecast.index', $request->query())
        ->with('error', '記録が見つかりませんでした。');
    }

    return view("pages.order.forecast.show", compact('unofficialRecord', 'daysInThreeMonths' )); // Remove extra comma
  }

  public function update(ForecastRequest $request, $id)
  {
    // Initialize database transaction for creating new records
    DB::beginTransaction();
    try{
      
      $unofficialNotice = UnofficialNotice::findOrFail($id);
      $unofficialNotice->update($request->validated());

      DB::commit();
      return redirect()
        ->route('order.forecast.index', $request->query())
        ->with('success', '内示情報の更新が完了しました。');


    } catch (\Exception $e) {
      // Log the error with detailed information
      Log::error('Error occurred while updating record.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }
  
  public function excel_export(Request $request)
  {
    $unofficialNotices = UnofficialNotice::search($request)->get();
    if($unofficialNotices->isEmpty())
    {
      return redirect()
        ->route('order.forecast.index', $request->query())
        ->with('error', '記録が見つかりませんでした。');
    }
    $fileName = '内示情報検索・一覧_' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new ForecastExport($unofficialNotices), $fileName, \Maatwebsite\Excel\Excel::XLSX);
  }
}
