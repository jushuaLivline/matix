<?php

namespace App\Http\Controllers\Order;

use App\Services\Order\ConfirmedService;

use App\Http\Controllers\Controller;
use App\Exports\OrderSearchExport;
use App\Models\FirmOrder;
use Illuminate\Http\Request;
use App\Constants\Constant;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class ConfirmedOrderController extends Controller
{

    public function __construct(ConfirmedService $confirmedService)
    {
        $this->confirmedService = $confirmedService;
    }
    public function index(Request $request)
    {
        // Fetch firm orders and shipment records
        list($firmOrders, $shipmentRecords) = $this->confirmedService->index($request);

        // Prepare data for merging Firm Orders and Shipment Records
        $data = $this->confirmedService->prepareData($firmOrders, $shipmentRecords);

        // Paginate the result set
        $perPage = 20;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $items = collect($data)->slice(($currentPage - 1) * $perPage, $perPage);
        $total = count($data);

        $result = new LengthAwarePaginator($items, $total, $perPage, $currentPage, [
            'path' => route('order.confirmed.index'),
            'url' => route('order.confirmed.index'),
        ]);

        // Collect unique and sorted delivery numbers
        $deliveryNos = $this->confirmedService->getUniqueSortedDeliveryNos($firmOrders, $shipmentRecords);

        return view("pages.order.confirmed.index", compact(
            'result' ,
            'deliveryNos'
        ));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = array_filter([
            'created_at' => $request->input('created_at') ?? now()->format('Ymd'),
            'delivery_no' => $request->input('delivery_no'),
            'delivery_destination_code' => $request->input('delivery_destination_code'),
            'delivery_destination_name' => $request->input('delivery_destination_name'),
            'plant' => $request->input('plant'),
            'acceptance' => $request->input('acceptance'),
            'classification' => $request->input('classification'),
            'part_number' => $request->input('part_number'),
            'product_name' => $request->input('product_name'),
        ]);

        //$paginationThreshold = Constant::PAGINATION_THRESHOLD;
        $result = FirmOrder::CreateSearch($data)
            ->paginateResults(10);

        return view('pages.order.confirmed.create', compact('data', 'result'));
    }

    public function delete(Request $request)
    {
        $id = $request->input('id');
        $deleted = FirmOrder::where('id', $id)->delete();

        if ($deleted) {
            return response()->json(['message' => '確定受注情報の削除が完了しました', 'status' => 'success']);
        } else {
            return response()->json(['message' => 'データの削除中にエラーが発生しました', 'status' => 'error']);
        }
    }

    public function bulkRegister(Request $request)
    {
        try {
            $orders = $request->input('orders');

            DB::beginTransaction();

            foreach ($orders as $order) {
                FirmOrder::where('id', $order['id'])->update([
                    'number_of_accommodated' => $order['number_of_accommodated'],
                    'kanban_number' => $order['kanban_number'],
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => '確定受注情報の登録が完了しました'
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction in case of an error

            return response()->json([
                'status' => 'error',
                'message' => '登録中にエラーが発生しました。',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function excel_export(Request $request)
    {
        list($firmOrders, $shipmentRecords) = $this->confirmedService->index($request);
        $data = $this->confirmedService->prepareData($firmOrders, $shipmentRecords);
        $deliveryNos = $this->confirmedService->getUniqueSortedDeliveryNos($firmOrders, $shipmentRecords);
        $category = $request->category ?? 0;


        $fileName = '確定受注検索・一覧_'.now()->format('Ymd').'.xlsx';
        // Download the Excel file using the export class
        return Excel::download(new OrderSearchExport(collect($data), $deliveryNos, $category), $fileName);
    }
}
