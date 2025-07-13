<?php

namespace App\Http\Controllers\Material\Order;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\Customer;
use App\Models\SupplyMaterialOrder;

use App\Services\Material\Order\DetailService;


use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class DetailController extends Controller
{
  protected $detailService;

  public function __construct(DetailService $detailService)
  {
    $this->detailService = $detailService;
  }

  //Material 23
  public function index()
  {
    return view('pages.material.order.detail.index');
  }

  //Material 23 export pdf
  public function PDF_export(Request $request)
  {
    $response = $this->detailService->PDF_export($request);
    if($response->getData(true)['error'])
    {
      return redirect()->route('material.order.detail.index')->with('error', $response->getData(true)['error']);
    }
    return redirect()->route('material.order.detail.index');
  }


}

