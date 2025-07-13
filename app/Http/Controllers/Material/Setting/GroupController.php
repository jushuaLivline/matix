<?php

namespace App\Http\Controllers\Material\Setting;

use App\Http\Controllers\Controller;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialArrival;
use App\Models\SupplyMaterialGroup;
use App\Exports\Material\SupplyListExcel;
use App\Http\Requests\Material\Setting\GroupRequest;

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


class GroupController extends Controller
{
  public function __construct()
  {
    $this->supplyMaterialGroup = new SupplyMaterialGroup();
  }

  public function store(GroupRequest $request)
  {
    DB::beginTransaction();
    try {
      $this->supplyMaterialGroup->create($request->validated());

      DB::commit();
      return back()->with('success', 'グループの登録が完了しました');

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

  public function update(GroupRequest $request, $id)
  {
    DB::beginTransaction();
    try {
      $group = $this->supplyMaterialGroup->findOrFail($id);
      $group->update($request->validated());

      DB::commit();
      return back()->with('success', 'グループの更新が完了しました');

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
  public function destroy($id)
  {
    DB::beginTransaction();
    try {
      $group = $this->supplyMaterialGroup->findOrFail($id); // Ensure the record exists
      $group->delete(); // Now delete the record

      DB::commit();
      return back()->with('success', 'グループの削除が完了しました');

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred.', [
        'error' => $e->getMessage(),
        'timestamp' => now(),
      ]);
      return redirect()->back()->with('error', 'Error occurred.');
    }
  }
}
