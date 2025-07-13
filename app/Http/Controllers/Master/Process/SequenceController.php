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

use App\Services\Master\Part\ProcessSequenceService;

class SequenceController extends Controller
{
  protected $processSequenceSettingService;
  public function __construct(ProcessSequenceService $processSequenceSettingService)
  {
      $this->processSequenceSettingService = $processSequenceSettingService;
  }
  
  public function store(Request $request)
  {
    return $this->processSequenceSettingService->store($request);
  }

  public function update(Request $request, $id)
  {
    return $this->processSequenceSettingService->updateSessionRow($request, $id);
  }

  public function destroy(Request $request, $partNumber)
  {
    return $this->processSequenceSettingService->destroy($request, $partNumber);
  }

  public function changeOrderRow(Request $request)
  {
    return $this->processSequenceSettingService->changeOrderRow($request);
  }

  
  public function getProcessOrder(Request $request)
  {
    return $this->processSequenceSettingService->getProcessOrder($request);
  }

  public function saveSession(Request $request)
  {
    return $this->processSequenceSettingService->saveSession($request);
  }

  // DELETE DATA FROM SESSION
  public function deleteSessionRow(Request $request)
  {
    return $this->processSequenceSettingService->deleteSessionRow($request);
  }
  
  
  

}

