<?php

namespace App\Services\Purchase;

use App\Helpers\RequestHelper;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseArrival;
use App\Models\PurchaseRecord;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseOrderListExport;
use App\Constants\Constant;
class AcceptanceService
{
  public function update($request, $id)
  {
    $data = [
      'purchase_order_number' => $request->purchase_order_number,
      'requisition_number' => $request->requisition_number,
      'supplier_code' => $request->supplier_code,
      'order_date' => $request->order_date,
      'deadline' => $request->deadline,
      'department_code' => $request->department_code,
      'line_code' => $request->line_code,
      'expense_items' => $request->item_code,
      'machine_number' => $request->machine_code,
      'project_number' => $request->project_code,
      'part_number' => $request->part_number,
      'product_name' => $request->product_name,
      'standard' => $request->standard,
      'quantity' => $request->quantity,
      'unit_code' => $request->unit_code,
      'unit_price' => $request->unit_price,
      'amount_of_money' => $request->amount_of_money,
      'where_used_code' => $request->where_used_code,
      'reason' => $request->reason,
      'remarks' => $request->remarks,
    ];
    PurchaseRequisition::where('id', $id)->update($data);
  }

  public function store($request)
  {
    $idArray = $request->id;

    foreach ($idArray as $key => $id) {
      $purchaseArrival = PurchaseArrival::findOrFail($id);

      if ($purchaseArrival) {
        // Update PurchaseArrival fields
        $purchaseArrival->update([
          'purchase_receipt_date' => $request->purchase_receipt_date_hidden[$key] ?? null,
          'arrival_day' => $request->arrival_day[$key] ?? null,
          'unable_to_resharpen_flag' => $request->unable_to_resharpen_flag[$key] ?? null,
          'slip_no' => $request->slip_no[$key] ?? null,
          'arrival_quantity' => $request->arrival_quantity[$key] ?? null,
          'remarks' => $request->remarks[$key] ?? null,
        ]);

        // Create PurchaseRecord
        PurchaseRecord::create([
          'purchase_record_no' => $purchaseArrival->purchase_record_no,
          'voucher_class' => 1,
          'slip_type' => 1,
          'purchase_category' => 2,
          'arrival_date' => $purchaseArrival->arrival_day,
          'supplier_code' => $purchaseArrival->requisition?->supplier_code,
          'machine_number' => $purchaseArrival->requisition?->machine_number,
          'branch_number' => $purchaseArrival->branch_number,
          'department_code' => $purchaseArrival->requisition?->department_code,
          'line_code' => $purchaseArrival->requisition?->line_code,
          'expense_item' => $purchaseArrival->requisition?->expense_items,
          'subsidy_items' => $purchaseArrival->requisition?->subsidy_items,
          'part_number' => $purchaseArrival->requisition?->part_number,
          'product_name' => $purchaseArrival->requisition?->product_name,
          'standard' => $purchaseArrival->requisition?->standard,
          'where_used_code' => $purchaseArrival->requisition?->where_used_code,
          'quantity' => $purchaseArrival->quantity,
          'unit_code' => $purchaseArrival->requisition?->unit_code,
          'unit_price' => $purchaseArrival->requisition?->unit_price,
          'amount_of_money' => $purchaseArrival->requisition?->amount_of_money,
          'tax_classification' => $purchaseArrival->requisition?->tax_classification,
          'slip_no' => $purchaseArrival->slip_no,
          'project_number' => $purchaseArrival->requisition?->project_number,
          'remarks' => $purchaseArrival->remarks,
        ]);
      }
    }
  }

  public function excel_export($request)
  {
    RequestHelper::processRequest($request);
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    // Fetch the filtered purchase requisition data with pagination
    $items = PurchaseRequisition::getOrderDataList($request);
    $items = $items->paginateResults($paginationThreshold);

    $fileName = '注文データ一覧_' . now()->format('Ymd') . '.xlsx';

    return [
      'items' => $items,
      'fileName' => $fileName
    ];
  }
}
