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
use App\Http\Requests\Master\PartNumberRequest;

use App\Services\Master\Part\ProcessUnitPriceService;

class UnitPriceController extends Controller
{
  protected $processUnitPriceService;

  public function __construct(ProcessUnitPriceService $processUnitPriceService)
  {
    $this->processUnitPriceService = $processUnitPriceService;
  }
  public function store(Request $request)
  {
    return $this->processUnitPriceService->store($request);
  }
  public function update(Request $request, $id)
  {
    return $this->processUnitPriceService->updateSessionRow($request, $id);
  }
  public function destroy(Request $request, $partNumber)
  {
    return $this->processUnitPriceService->destroy($request, $partNumber);
  }

  public function deleteSessionRow(Request $request)
  {
    return $this->processUnitPriceService->deleteSessionRow($request);
  }
  public function getProcessSetting(Request $request)
  {
    return $this->processUnitPriceService->getProcessSetting($request);
  }

  public function saveSession(Request $request)
  {
    return $this->processUnitPriceService->saveSession($request);
  }

}

