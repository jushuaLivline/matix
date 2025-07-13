<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Services\Purchase\PurchaseRequisitionService;
use App\Constants\Constant;
use App\Exports\PurchaseRequisitionSearchExport;
use App\Exports\PurchaseOrderListExport;
use App\Http\Requests\PurchaseRequisition\RequisitionApprovalDetailsRequest;
use App\Models\Code;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseApproval;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Helpers\RequestHelper;

class RequisitionOrderController extends Controller
{
  protected $purchaseRequisitionService;
  public function __construct(PurchaseRequisitionService $purchaseRequisitionService)
  {
    $this->purchaseRequisitionService = $purchaseRequisitionService;
  }

  public function orderDataListExport(Request $request)
  {
    RequestHelper::processRequest($request);
    // Fetch the filtered purchase requisition data with pagination
    $items = PurchaseRequisition::getOrderDataList($request);
    $items = $items->limit(20)->get();

    $fileName = '注文データ一覧_' . now()->format('Ymd') . '.xlsx';
    return Excel::download(new PurchaseOrderListExport($items), $fileName, \Maatwebsite\Excel\Excel::XLSX);
  }
}