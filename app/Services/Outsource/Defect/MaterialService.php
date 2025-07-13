<?php

namespace App\Services\Outsource\Defect;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Helpers\RequestHelper;
use App\Models\Code;
use App\Models\OutsourceMaterialFailure;
use App\Models\ProductNumber;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class MaterialService
{

  public function updateDefectRecord($request, $id)
  {
    $validatedData = $request;
    $validatedData['return_date'] = Carbon::parse($validatedData['return_date'])->format('Y-m-d');
    $validatedData['product_number'] = $validatedData['product_code'];
    unset($validatedData['product_code']);

    // Find the defect record
    $materialFailure = OutsourceMaterialFailure::findOrFail($id);
    $materialFailure->update($validatedData);

    return $materialFailure;
  }

  public function edit($request, $id)
  {
    $request->merge([
      'outsource_material_failures.id' => $id
    ]);

    $materialFailureRecord = OutsourceMaterialFailure::search($request)->first();

    return $materialFailureRecord;
  }
}
