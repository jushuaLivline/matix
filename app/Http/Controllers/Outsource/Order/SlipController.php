<?php

namespace App\Http\Controllers\Outsource\Order;

use App\Exports\Outsource\Order\SlipExport;
use App\Services\Outsource\Order\SlipService;
use App\Http\Requests\Outsource\Order\SlipRequest;

use App\Http\Controllers\Controller;
use App\Models\SupplyMaterialOrder;
use App\Models\OutsourcedProcessing;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Constants\Constant;

class SlipController extends Controller
{
  protected $slipService;

  public function __construct(SlipService $slipService)
  {
    $this->slipService = $slipService;
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    $outsourcedProcesses = OutsourcedProcessing::search($request)->paginate($paginationThreshold);
    return view('pages.outsource.order.slip.index', compact('outsourcedProcesses'));
  }
  public function PDF_export(Request $request)
  {
    $response = $this->slipService->PDF_export($request);
    if($response->getData(true)['error'])
    {
      return redirect()->route('outsource.order.slip.index', $request->query())->with(['error' => '注文が見つかりませんでした'], 404);
    }
    return redirect()->route('material.order.detail.index');
  }
}
