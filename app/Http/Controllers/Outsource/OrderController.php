<?php

namespace App\Http\Controllers\Outsource;

use App\Exports\Outsource\OrderExcelExport;

use App\Http\Controllers\Controller;
use App\Models\SupplyMaterialOrder;
use App\Models\OutsourcedProcessing;
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
      $filters = [
      'supplier_code' => $request->input('supplier_code'),
      'product_code' => $request->input('product_code'),
      'document_issue_date_from' => $request->input('document_issue_date_from'),
      'document_issue_date_to' => $request->input('document_issue_date_to'),
      'instruction_date_from' => $request->input('instruction_date_from'),
      'instruction_date_to' => $request->input('instruction_date_to'),
      'order_number_start' => $request->input('order_number_start'),
      'order_number_end' => $request->input('order_number_end'),
    ];

    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $outsourcedProcesses = OutsourcedProcessing::search($request)->paginateResults($paginationThreshold);

    return view('pages.outsource.order.index', compact('outsourcedProcesses'));
  }

  public function excel_export(Request $request)
  {
    $outsourcedProcesses = OutsourcedProcessing::search($request)->get();
    return Excel::download(new OrderExcelExport($outsourcedProcesses), '外注加工発注データ_' . now()->format('Ymd') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
  }
}
