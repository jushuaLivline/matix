<?php

namespace App\Http\Controllers\Purchase;

use Exception;
use App\Models\Code;
use Illuminate\Http\Request;
use App\Helpers\RequestHelper;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\Purchase\OrderService;
use App\Exports\Purchase\Order\ListExport;
use App\Http\Requests\Purchase\OrderRequest;
use App\Constants\Constant;

class OrderController extends Controller
{
  protected $orderService;

  public function __construct(OrderService $orderService)
  {
    $this->orderService = $orderService;
  }

  public function index(Request $request)
  {
    if (!$request->query()) {
      // Define default empty values
      $items = [];
      $amount_of_money = 0;
      $defaultQuery = [
        'status' => 'all',
        'acceptance' => 'all',
        'order_date_start' => now()->startOfMonth()->format('Ymd'),
        'order_date_end' => now()->endOfMonth()->format('Ymd')
      ];
      $request->merge($defaultQuery);
    }
    $requestData = $request->all();

    // Define pagination threshold
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;

    // Process and sanitize the request data
    RequestHelper::processRequest($request);

    // Fetch the filtered purchase requisition data with pagination
    // $items = PurchaseRequisition::getOrderDataList($request);

    $items = PurchaseRequisition::getDataList($request);
    $items = $items->paginateResults($paginationThreshold);

    // Calculate the total sum of 'amount_of_money'
    $amount_of_money = $items->sum('amount_of_money');

    return view('pages.purchases.order.index', compact('items', 'amount_of_money', 'requestData'));
  }

  public function edit($id)
  {
    $units = Code::selectRaw('
            division,
            code,
            name    
        ')
      ->whereDivision('単位')
      ->get();

    $item = PurchaseRequisition::with('department')->findOrFail($id);
    return view('pages.purchases.order.edit', compact('units', 'item'));
  }

  public function update(OrderRequest $request, $id)
  {
    try {
      DB::beginTransaction();
      $this->orderService->update($request->validated(), $id);
      $message = '発注情報の更新が完了しました';

      DB::commit();
      return redirect()->route('purchase.order.index', [
        'order_date_end' => now()->endOfMonth()->format('Ymd'),
        'status' => 'all',
        'acceptance' => 'all',
      ])->with('success', $message);

    } catch (Exception $e) {
      DB::rollBack();
      Log::error('処理でエラーが発生しました: ' . $e->getMessage());

      return redirect()->back()
        ->withInput()
        ->with('error', '処理に失敗しました。' . $e->getMessage());
    }
  }

  public function cancel($id)
  {
    DB::beginTransaction();
    try {
      $this->orderService->cancel($id);
      DB::commit();

      $message = '取消処理が完了しました';
      return redirect()->route('purchase.order.index', [
        'order_date_end' => now()->endOfMonth()->format('Ymd'),
        'status' => 'all',
        'acceptance' => 'all',
      ])->with('success', $message);
    } catch (Exception $e) {
      DB::rollBack();
      Log::error('処理でエラーが発生しました: ' . $e->getMessage());

      return redirect()->back()
        ->withInput()
        ->with('error', '処理に失敗しました。' . $e->getMessage());
    }
  }

  public function excel_export(Request $request)
  {
    $res = $this->orderService->excel_export($request);
    return Excel::download(new ListExport($res['items']), $res['fileName'], \Maatwebsite\Excel\Excel::XLSX);
  }
}
