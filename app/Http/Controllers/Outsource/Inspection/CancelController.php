<?php

namespace App\Http\Controllers\Outsource\Inspection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outsource\Supply\Kanban\Request as KanbanRequest;
use App\Models\KanbanMaster;
use App\Models\Outsource\SubcontractSupply;
use App\Models\OutsourcedProcessing;

use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class CancelController extends Controller
{
  public function __construct()
  {
    $this->kanban = new KanbanMaster();
    $this->subcontractSupply = new SubcontractSupply();
  }

  public function index(Request $request)
  {
    $arrivalResultLists = [];
    if (!$request->all()) {
      // Define default empty values
      $request->merge([
        'arrival_day_from' => now()->startOfMonth()->format("Ymd"),
        'arrival_day_to' => now()->endOfMonth()->format("Ymd"),
      ]);
    }
   
   
    // Get the results
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $arrivalResultLists = OutsourcedProcessing::query()
      ->search($request)->paginateResults($paginationThreshold);

    return view('pages.outsource.inspection.cancel.index', compact(
      'arrivalResultLists'
    ));
  }

  public function destroy(Request $request, $id)
  {
    $outsourcedDataIds = $request->input('outsourced_data_ids', []);

    if (!empty($outsourcedDataIds) && is_array($outsourcedDataIds)) {
      // Ensure all IDs are numeric values
      $outsourcedDataIds = array_filter($outsourcedDataIds, 'is_numeric');

      if (!empty($outsourcedDataIds)) {
        $updatedRows = OutsourcedProcessing::whereIn('id', $outsourcedDataIds)
          ->delete();

        if ($updatedRows > 0) {
          return redirect()->back()->with('success', "外注加工検収の取り消しが完了いたしました。");
        } else {
          return redirect()->back()->with('error', '更新されたレコードはありません。');
        }
      }
    }

    // If the IDs are invalid or empty
    return redirect()->back()->with('error', '無効なIDまたはIDがありません。');
  }
}
