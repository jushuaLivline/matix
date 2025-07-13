<?php

namespace App\Http\Controllers\Purchase;

use Exception;
use Carbon\Carbon;
use App\Models\Code;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PurchaseRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Exports\PurchaseHistoryExport;
use App\Services\Purchase\HistoryService;
use App\Http\Requests\Purchase\HistoryRequest;


class HistoryController extends Controller
{
  protected $historyService;

  public function __construct(HistoryService $historyService) 
  {
    $this->historyService = $historyService;
  }

  public function index(Request $request)
  {
      $paginationThreshold = 10;
      
      // Once the search button is clicked, the start and end date will be set to the input value
      if ($request->has('search')) {
          $startDate = $request->filled('start_date') ? $request->start_date : '';
          $endDate = $request->filled('end_date') ? $request->end_date : '';
      } 
      else {
          $startDate = now()->startOfMonth()->format('Ymd');
          $endDate = now()->endOfMonth()->format('Ymd');
      }
      
      $datas = PurchaseRecord::search($request)->paginateResults($paginationThreshold);
      
      return view('pages.purchases.history.index', [
          'datas' => $datas,
          'start_date' => $startDate,
          'end_date' => $endDate,
      ]);
  }

  public function create(Request $request)
  {
    $codes = Code::where('division', '単位')->get();
    $purchase_category = $request->get('purchase_category');
    $is_previous_input = $this->historyService->check_previous_input($purchase_category);
    return view('pages.purchases.history.create', compact('codes', 'is_previous_input'));
  }

  public function store(Request $request)
  {
      try {
          DB::beginTransaction();
          
          $this->historyService->store($request);
          
          DB::commit();
          
          $purchase_category = $request->purchase_category;
          $message = $purchase_category == 2 ? "購買品の登録が完了しました。" : "生産品の登録が完了しました。";
          return redirect()->route('purchase.history.create', ['purchase_category' => $purchase_category])->with('success', $message);
          
      } catch (\Exception $e) {
          DB::rollBack();
          
          Log::error($e->getMessage());
          return redirect()->back()
              ->withInput()
              ->with('error', '登録中にエラーが発生しました。');
      }
  }

  public function copy_previous_input(Request $request){
     $purchase_category = $request->get('purchase_category');
     $data = $this->historyService->copy_previous_input($purchase_category);
     
     return redirect()->route('purchase.history.create', ['purchase_category' => $purchase_category])->with('previous_data', $data);
  }
  public function duplicate($id)
  {
      $data = $this->historyService->edit($id);
      $codes = Code::where('division', '単位')->get();
      return view('pages.purchases.history.duplicate', compact('codes','data'));
  }

  public function edit($id){
    $data = $this->historyService->edit($id);
    $codes = Code::where('division', '単位')->get();
    return view('pages.purchases.history.edit', compact('data', 'codes', 'id'));
  }

  public function update(Request $request, $id)
  {
     try {
         DB::beginTransaction();
         
         $this->historyService->update($request, $id);
         
         DB::commit();
  
         $purchase_category = $request->get('purchase_category');
         $message = $purchase_category == 2 ? "購買品の更新が完了しました。" : "生産品の更新が完了しました。";
         
         return redirect()->route('purchase.history.edit', [
             'history' => $id,
             'purchase_category' => $purchase_category
         ])->with('success', $message);
  
     } catch (\Exception $e) {
         DB::rollBack();
         
         Log::error($e->getMessage());
         return redirect()->back()
             ->withInput()
             ->with('error', '更新中にエラーが発生しました。');
     }
  }

  public function destroy($id)
  {
    try {
      DB::beginTransaction();
      
      $this->historyService->destroy($id);
      
      DB::commit();
      
      return redirect()->route('purchase.history.index')->with('success', '購入実績の削除が完了しました。');
      
    } catch (Exception $e) {
      DB::rollBack();
      
      Log::error($e->getMessage());
      return redirect()->back()
          ->with('error', '削除中にエラーが発生しました。');
    }
  }

  public function excel_export(Request $request){
        $datas = [];
        // Check if there are any query parameters and apply filtering if present
        if ($request->query()) {
            // Retrieve only the data from the current page
            $datas = PurchaseRecord::search($request)->paginate($request->per_page ?? 10)->items();
        }
        // Generate the file name with the current date
        $fileName = '購入実績検索・一覧-'.now()->format('Ymd').'.xlsx';
        // Export the data using the specified export class and file name
        return Excel::download(
            export: new PurchaseHistoryExport( datas: $datas), 
            fileName: $fileName);
  }
}
