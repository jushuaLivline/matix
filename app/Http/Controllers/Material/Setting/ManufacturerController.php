<?php

namespace App\Http\Controllers\Material\Setting;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialArrival;
use App\Models\SupplyMaterialGroup;
use App\Models\ManufacturerInfo;
use App\Http\Requests\Material\Setting\ManufacturerRequest;

use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;  
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Helpers\RequestHelper;
use Maatwebsite\Excel\Facades\Excel;


class ManufacturerController extends Controller
{
  public function __construct()
  {
    $this->manufacturerInfo = new ManufacturerInfo();
  }

  public function store(ManufacturerRequest $request)
  {
    DB::beginTransaction();
    try {

      $this->manufacturerInfo->create($request->validated());
      DB::commit();
      return back()->with('success', '担当者・連絡先情報の設定が完了しました。');

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'Error occurred.');
    }
  }

  public function update(ManufacturerRequest $request, $id)
  {
    DB::beginTransaction();
    try {
      
      $group = $this->manufacturerInfo->findOrFail($id);
      $group->update($request->validated());

      DB::commit();
      return back()->with('success', '担当者・連絡先情報の更新が完了しました。');

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'Error occurred.');
    }
  }
}
