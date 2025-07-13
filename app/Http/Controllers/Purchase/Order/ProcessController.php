<?php

namespace App\Http\Controllers\Purchase\Order;

use App\Http\Controllers\Controller;
use App\Models\PurchaseArrival;
use App\Models\PurchaseRequisition;
use App\Models\Code;
use App\Models\Customer;
use App\Models\PurchaseRecord;
use App\Services\Purchase\Order\ProcessService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProcessController extends Controller
{
  protected $processService;

  public function __construct(ProcessService $processService)
  {
    $this->processService = $processService;
  }
  
  public function index(Request $request)
  {
    $items = $this->processService->index($request);
    $request = $request->query();
    return view('pages.purchases.order.process.index', compact('items', 'request'));
  }
}
