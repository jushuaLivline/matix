<?php

namespace App\Services\Master\Part;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Configuration;
use App\Models\Process;
use App\Models\ProcessOrder;
use App\Models\ProcessUnitPrice;
use App\Models\ProductNumber;
use App\Models\ProductPrice;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Services\Master\PartService;
class PartNumberUnitPriceService
{
  protected $partService;

  public function __construct(PartService $partService)
  {
    $this->partService = $partService;
  }

  public function getPartNumberUnitPriceSetting(Request $request)
  {
    $mcup = $this->getMaterialComponentUnitPrice($request->part_number);
    $part_number = $request->part_number ?? 0;

    $productPrices = ProductPrice::where('part_number', $request->part_number)->get();
    $insideProcess = $this->partService->getProcessPrices($part_number, 1);
    $outsideProcess = $this->partService->getProcessPrices($part_number, 2);

    $sessionData = [];
    foreach ($productPrices as $key => $value) {
      $formattedDate = Carbon::parse($value['effective_date'])->format('Ymd');
      $sell_price = number_format($value['sell_price'], 2, '.', '');
      $unit_price = number_format($value['unit_price'], 2, '.', '');
      $sessionData[] = [
        'id' => $value['id'],
        'effective_date' => $formattedDate,
        'sell_price' => $value['sell_price'],
        'unit_price' => $value['unit_price'],
        'processing_unit_price' => ceil(($sell_price  -  $unit_price) * 100) / 100,
        'datepicker' => $this->getDatePickerContent($formattedDate, $key),
        'inside_process' => $insideProcess['processing_unit_price'] ?? 0,
        'outside_process' => $outsideProcess['processing_unit_price'] ?? 0,
        'material_component_unit_price' => $mcup,
      ];
    }

    session(['items' => $sessionData]);

    return session('items');

  }

  public function saveSession(Request $request)
  {

    $today = date('Y-m-d 00:00:00');
    $part_number = $request->part_number ?? 0;

    $insideProcess = $this->partService->getProcessPrices($part_number, 1);
    $outsideProcess = $this->partService->getProcessPrices($part_number, 2);

    $child_part_numbers = Configuration::where('parent_part_number', $part_number)->where('delete_flag', 0)->get();
    $mcup = 0;
    if ($child_part_numbers != null) {
      $child_part_numbers = $child_part_numbers->toArray();
      $child_part_numbers = array_column($child_part_numbers, 'child_part_number');
      $mcu_price = ProductPrice::selectRaw('part_number, effective_date, sell_price, unit_price, sell_price - unit_price as mcu_price')
        ->whereIn('part_number', $child_part_numbers)
        ->where(function ($query) use ($today) {
          $query->where('effective_date', '>=', $today)
            ->orWhere('effective_date', '=', $today);
        })
        ->get();
      if ($mcu_price != null) {
        foreach ($mcu_price as $key => $value) {
          $mcup += $value->mcu_price;
        }
      }
    }
    $data = $request->data;
    $sessionData = session('items', []);
    $key = 0;
    if (!empty($sessionData)) {
      $key = array_key_last($sessionData) + 1;
    }
    $data = [
      'effective_date' => $data['effective_date'],
      'sell_price' => $data['sell_price'],
      'unit_price' => $data['unit_price'],
      'processing_unit_price' => $data['sell_price'] - $data['unit_price'],
      'datepicker' => $this->getDatePickerContent($data['effective_date'], $key),
      'inside_process' => $insideProcess['processing_unit_price'] ?? 0,
      'outside_process' => $outsideProcess['processing_unit_price'] ?? 0,
      'material_component_unit_price' => $mcup,
      'creator' => $request->user()->id,
    ];

    $sessionData[] = $data;
    session(['items' => $sessionData]);

    return session('items');
  }

  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      if (session()->has('items')) {
        $processSetting = ProductPrice::search($request)->get();
        if (!empty($processSetting)) {
          ProductPrice::search($request)->delete();
        }
        $sessionData = session("items", []);
        $productPriceRecored = collect($sessionData)->map(function ($item) use ($request) {
          return [
            'part_number' => $request->part_number,
            'effective_date' => Carbon::parse($item['effective_date'])->format('Y-m-d H:i:s'),
            'sell_price' => $item['sell_price'],
            'unit_price' => $item['unit_price'],
            'creator' => $item['creator'] ?? null,
            'updator' => $item['updator'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,
          ];
        })->toArray();

        // Insert new records
        ProductPrice::insert($productPriceRecored);
        DB::commit();

        session()->forget("items");
        return redirect()->route('master.part.edit', $request->product_id)->with('success', '品番単価設定の登録が完了しました');
      }
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while storing the data.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function destroy(Request $request, $partNumber)
  {
    try {
      // Check if records exist before deleting
      $processRecords = ProductPrice::search($request)->get();
      if ($processRecords->isEmpty()) {
        return redirect()->back()->with('error', '削除するデータがありません');
      }

      // Delete records
      ProductPrice::search($request)->delete();
      DB::commit();

      // Remove from session
      $sessionData = session('process_setting', []);
      if (isset($sessionData[$request->process_code])) {
        unset($sessionData[$request->process_code]);
      }
      session(['process_setting' => $sessionData]);

      return redirect()->route('master.part.edit', $request->product_id)->with('success', '品番単価設定の削除が完了しました');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while deleting the data.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function updateSessionRow(Request $request, $id)
  {

    DB::beginTransaction();
    try {
      $insideProcess = $this->partService->getProcessPrices($request->part_number, 1);
      $outsideProcess = $this->partService->getProcessPrices($request->part_number, 2);
      $mcup = $this->getMaterialComponentUnitPrice($request->part_number);

      $index = $request->input('index');
      $effective_date = Carbon::parse($request->input('effective_date'))->format('Ymd');
      $sell_price = number_format($request->input('sell_price', 0), 2, '.', '');
      $unit_price = number_format($request->input('unit_price', 0), 2, '.', '');


      // Get existing session data
      $sessionData = session('items', []);

      // Validate required inputs
      if (!isset($sessionData[$index])) {
        return response()->json(['error' => 'Invalid index.'], 400);
      }

      // update the record in the database
      if($id && $id > 0){
        $productPrice = ProductPrice::findOrFail($id);
        $productPrice->fill([
          'effective_date' => Carbon::parse($effective_date),
          'sell_price'     => $sell_price,
          'unit_price'     => $unit_price,
          'updator'        => $request->user()->id,
          'updated_at'     => now(),
          ])->save();
      }
      
      
      // Update session data
      $sessionData[$index] = [
        'id' => $id,
        'effective_date' => $effective_date,
        'sell_price' => $sell_price,
        'unit_price' => $unit_price,
        'processing_unit_price' => ceil(($sell_price - $unit_price) * 100) / 100,
        'datepicker' => $this->getDatePickerContent($effective_date, $index),
        'inside_process' => $insideProcess['processing_unit_price'] ?? 0,
        'outside_process' => $outsideProcess['processing_unit_price'] ?? 0,
        'material_component_unit_price' => $mcup,
        'updator' => $request->user()->id,
        'updated_at' => now()->format('Y-m-d H:i:s')
      ];

      session(['items' => $sessionData]);

      DB::commit();
      return session('items');

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while updating the data.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function deleteSessionRow(Request $request)
  {
    DB::beginTransaction();
    try {

      $productPrice = ProductPrice::find($request->product_prices_id);
      if ($productPrice) {
        $productPrice->delete();
      }

      $sessionData = session('items', []);

      if (isset($sessionData[$request->index])) {
        array_splice($sessionData, $request->index, length: 1); // Remove the specific index

        // Reindex the process orders
        foreach ($sessionData as $key => &$value) {
          $value['items'] = $key + 1;
        }
        unset($value);
        // Update session data
        session(['items' => $sessionData]);
      }

      DB::commit();
      return $sessionData;

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while removing the data.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function getDatePickerContent($effective_date = null, $key = 0)
  {
    return view('partials._date_picker', [
      'inputName' => 'effective_date_setting_' . $key,
      'dateFormat' => 'YYYYMMDD',
      'value' => $effective_date,
      'inputClass' => 'w-100c input-required',
    ])->render();
  }

  private function getMaterialComponentUnitPrice($part_number)
  {
    $today = date('Y-m-d 00:00:00');
    $child_part_numbers = Configuration::where('parent_part_number', $part_number)->where('delete_flag', 0)->get();
    $mcup = 0;
    if ($child_part_numbers != null) {
      $child_part_numbers = $child_part_numbers->toArray();
      $child_part_numbers = array_column($child_part_numbers, 'child_part_number');
      $mcu_price = ProductPrice::selectRaw('part_number, effective_date, sell_price, unit_price, sell_price - unit_price as mcu_price')
        ->whereIn('part_number', $child_part_numbers)
        ->where(function ($query) use ($today) {
          $query->where('effective_date', '>=', $today)
            ->orWhere('effective_date', '=', $today);
        })
        ->get();
      if ($mcu_price != null) {
        foreach ($mcu_price as $key => $value) {
          $mcup += $value->mcu_price;
        }
      }
    }
    return $mcup;
  }
}
