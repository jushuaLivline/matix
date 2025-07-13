<?php

namespace App\Services\Material;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\SupplyMaterialOrder;
use App\Models\UnofficialNotice;
use App\Models\OutsourcedProcessing;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class ProcurementService
{
  public function __construct()
  {
    $this->outsourcedProcessing = new OutsourcedProcessing();
  }

  public function store($request)
  {
    $instructionDates = $request['instruction_date'];
    $instructionNos = $request['instruction_number'];

    foreach ($instructionDates as $index => $instructionDate) {
      $newOrderNumber = now()->format('md') . '0' . ($index + 100);

      if ($instructionNos[$index] !== null) {
        $existingRecord = SupplyMaterialOrder::where('material_number', $request['material_number'])
          ->where('instruction_date', Carbon::parse($instructionDate)->format('Y-m-d'))
          ->first();
        if ($existingRecord) {
          $existingRecord->update([
            'instruction_number' => $instructionNos[$index]
          ]);
        } else {
          $request['instruction_date'] = Carbon::parse($instructionDate)->format('Y-m-d');
          $request['supply_material_order_no'] = $newOrderNumber;
          $request['instruction_number'] = $instructionNos[$index];
          SupplyMaterialOrder::create($request);
        }
      }
    }
    return;
  }

  public function update($request, $id, $instructionNo)
  {
    $days = [];
    for ($i = 1; $i <= 31; $i++) {
      $days['day_' . $i] = isset($instructionNo[$i - 1]) ? $instructionNo[$i - 1] : null;
    }
    $procurement = UnofficialNotice::findOrFail($id);
    $requestData = array_merge($request, $days);

    return $procurement->update($requestData);
  }

  public function destroy($request, $id)
  {
   $yearMonth = Carbon::createFromFormat('Ym',$request->year_month);
    $getRecords = SupplyMaterialOrder::where('material_number', $request->material_number)
    ->whereMonth('instruction_date', $yearMonth->format('m'))
    ->whereYear('instruction_date', $yearMonth->format('Y'))
    ->where('order_classification', 4);

    if ($getRecords->exists()) {
      $getRecords->delete();
    }
    return;
  }

}