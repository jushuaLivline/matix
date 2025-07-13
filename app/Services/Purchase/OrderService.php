<?php

namespace App\Services\Purchase;

use App\Helpers\RequestHelper;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchaseOrderListExport;
use App\Constants\Constant;

class OrderService
{
    public function update($data, $id)
    {
        $order = PurchaseRequisition::findOrFail($id);
        $data['updator'] = Auth::user()->employee_code;
        $order->update($data);
        return $order;
    }

    public function cancel($id){
        $order = PurchaseRequisition::findOrFail($id);
        $order->update([
            'order_date' => null,
            'updator' => Auth::user()->employee_code
        ]);
        return $order;
    }

    public function excel_export($request){
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
