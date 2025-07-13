<?php

namespace App\Http\Controllers\Material;

use App\Exports\Material\OrdersExcelExport;

use App\Http\Controllers\Controller;
use App\Models\SupplyMaterialOrder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Constants\Constant;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginationThreshold = Constant::PAGINATION_THRESHOLD;
        $supplyMaterialOrders = SupplyMaterialOrder::search($request->all())->paginateResults($paginationThreshold);

        return view('pages.material.order.index', compact('supplyMaterialOrders'));
    }

    public function excel_export(Request $request)
    {
        // Get paginated results
        $supplyMaterialOrders = SupplyMaterialOrder::search($request->all())->paginateResults($request->per_page ?? 20);

        // Get only the items on the current page
        $currentPageItems = collect($supplyMaterialOrders->items());

        return Excel::download(new OrdersExcelExport($currentPageItems), '支給材発注データ_' . now()->format('Ymd') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
