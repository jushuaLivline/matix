<?php

namespace App\Http\Controllers\Order\Kanban;

use App\Http\Controllers\Controller;
use App\Models\UnofficialNotice;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForecastController extends Controller
{
    public function __construct()
  {
    $this->unofficialNotice = new UnofficialNotice();
  }
    public function index(Request $request)
    {
      return redirect()->route('order.kanbanForecast.create');
    }
    
    function create(Request $request)
    {
        $yearMonth = $request->year_month ? Carbon::parse($request->year_month . "01") : null;
        $results = $this->unofficialNotice->getOrderForcast($request)->paginateResults(20);

        $yearMonths = CarbonPeriod::create($yearMonth, '1 month', 3);
        $monthIndexColumn = UnofficialNotice::monthIndexColumn;

        return view("pages.order.kanban.forecast.index", compact(
            'results',
            'yearMonths',
            'monthIndexColumn'
        ));
    }

    function store(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->ids as $id) {
                $unofficialNotice = UnofficialNotice::find($id);

                if (!$unofficialNotice) {
                    continue; // Skip this iteration if no record found
                }

                foreach (UnofficialNotice::monthIndexColumn as $column) {
                    $inputKey = "value_" . $id . "_" . $column;
                    $unofficialNotice->{$column} = $request->{$inputKey} ?? 0;
                    $unofficialNotice->save();
                }
            }
            DB::commit();
        
            return back()->with("success", "かんばん品内示情報の登録が完了しました。");

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction in case of an error

            return response()->json([
                'status' => 'error',
                'message' => '登録中にエラーが発生しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}