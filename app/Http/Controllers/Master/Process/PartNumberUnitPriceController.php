<?php

namespace App\Http\Controllers\Master\Process;

use App\Http\Controllers\Controller;

use App\Exports\Master\ProductNumberExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

use App\Models\Code;
use App\Models\ProductNumber;
use App\Models\Configuration;
use App\Models\Process;
use App\Models\ProcessOrder;
use App\Models\ProcessUnitPrice;
use App\Models\ProductPrice;

use App\Constants\Constant;
use App\Services\Master\Part\PartNumberUnitPriceService;

class PartNumberUnitPriceController extends Controller
{
  protected $partNumberUnitPriceService;

  public function __construct(PartNumberUnitPriceService $partNumberUnitPriceService)
  {
    $this->partNumberUnitPriceService = $partNumberUnitPriceService;
  }
  public function store(Request $request)
  {
    return $this->partNumberUnitPriceService->store($request);
  }
  
  public function update(Request $request, $id)
  {
    return $this->partNumberUnitPriceService->updateSessionRow($request, $id);
  }
  
  public function destroy(Request $request, $partNumber)
  {
    return $this->partNumberUnitPriceService->destroy($request, $partNumber);
  }

  public function deleteSessionRow(Request $request)
  {
    return $this->partNumberUnitPriceService->deleteSessionRow($request);
  }
  public function getPartNumberUnitPriceSetting(Request $request)
  {
    return $this->partNumberUnitPriceService->getPartNumberUnitPriceSetting($request);
  }

  public function saveSession(Request $request)
  {
    return $this->partNumberUnitPriceService->saveSession($request);
  }

}

