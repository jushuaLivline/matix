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

class ProcessUnitPriceService
{

  public function getProcessSetting(Request $request)
  {
    // Retrieve unit price records based on the request criteria
    $unit_prices = ProcessUnitPrice::search($request)->get();

    // Reset session if unit prices exist
    if (session()->has('process_setting')) {
      session()->forget('process_setting');
    }

    // Retrieve or initialize session data
    $sessionData = session('process_setting', []);

    foreach ($unit_prices as $key => $price) {
      $formattedDate = Carbon::parse($price->effective_date)->format('Ymd');

      $sessionData[$request->process_code][] = [
        'process_unit_price_id' => $price->id,
        'effective_date' => $formattedDate,
        'processing_unit_price' => $price->processing_unit_price ?? 0,
        'datepicker' => $this->getDatePickerContent($formattedDate, $key),
        'creator' => $price->creator,
        'updator' => $price->updator,
        'updated_at' => $price->updated_at,
      ];
    }

    // Store session data
    session(['process_setting' => $sessionData]);

    return $sessionData;
  }

  public function saveSession(Request $request)
  {
    $data = $request->data;
    $processCode = $request->process_code;

    // Retrieve or initialize session data
    $sessionData = session('process_setting', []);
    $sessionData[$processCode] = $sessionData[$processCode] ?? [];

    // Determine the new key index
    $key = count($sessionData[$processCode]);

    // Format date using Carbon
    $effectiveDate = Carbon::parse($data['effective_date_setting'])->format('Ymd');

    // Append new data
    $sessionData[$processCode][] = [
      'effective_date' => $effectiveDate,
      'processing_unit_price' => $data['processing_unit_price'] ?? 0,
      'datepicker' => $this->getDatePickerContent($effectiveDate, $key),
      'creator' => $request->user()->id,
    ];

    // Store session data
    session(['process_setting' => $sessionData]);

    return $sessionData;
  }

  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      if (session()->has('process_setting') && array_key_exists($request->process_code, session('process_setting'))) {
        $processSetting = ProcessUnitPrice::search($request)->get();
        if (!empty($processSetting)) {
          ProcessUnitPrice::search($request)->delete();
        }

        $processCode = $request->process_code;
        $sessionData = session("process_setting.$processCode", []);

        $unitPriceRecords = collect($sessionData)->map(function ($item) use ($request) {
          return [
            'part_number' => $request->part_number,
            'process_code' => $request->process_code,
            'effective_date' => Carbon::parse($item['effective_date'])->format('Y-m-d H:i:s'),
            'processing_unit_price' => $item['processing_unit_price'] ?? 0,
            'creator' => $item['creator'] ?? null,
            'updator' => $item['updator'] ?? null,
            'updated_at' => $item['updated_at'] ?? null,
          ];
        })->toArray();

        // Insert new records
        ProcessUnitPrice::insert($unitPriceRecords);
        DB::commit();

        session()->forget("process_setting.$processCode");
        return redirect()->back()->with('success', '工程単価設定の登録が完了しました');
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
    DB::beginTransaction();
  
    try {
      // Check if records exist before deleting
      $processRecords = ProcessUnitPrice::search($request)->get();
      if ($processRecords->isEmpty()) {
        return redirect()->back()->with('error', '削除するデータがありません');
      }

      // Delete records
      ProcessUnitPrice::search($request)->delete();
      DB::commit();

      // Remove from session
      $sessionData = session('process_setting', []);
      if (isset($sessionData[$request->process_code])) {
        unset($sessionData[$request->process_code]);
      }
      session(['process_setting' => $sessionData]);

      return redirect()->route('master.part.edit', $request->product_id)->with('success', '工程単価設定の削除が完了しました');
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
      $process_code = $request->input('process_code');
      $index = $request->input('index');

      // Validate required inputs
      if (!isset($process_code, $index)) {
        return response()->json(['error' => 'Invalid process code or index'], 400);
      }

      $effective_date = Carbon::parse($request->input('effective_date'))->format('Ymd');
      $processing_unit_price = $request->input('processing_unit_price', 0);

      // Retrieve session data
      $sessionData = session('process_setting', []);

      // Ensure process code exists in session
      if (!isset($sessionData[$process_code][$index])) {
        return response()->json(['error' => 'Data not found in session'], 404);
      }

      // Update the record in the database
      if($id && $id > 0){
        $updateProcessUnitPrice = ProcessUnitPrice::findOrFail($id);
        $updateProcessUnitPrice->update([
          'process_code' => $process_code,
          'effective_date' => Carbon::parse($effective_date)->format('Y-m-d H:i:s'),
          'processing_unit_price' => $processing_unit_price,
          'updator' => $request->user()->id,
          'updated_at' => now()->format('Y-m-d H:i:s')
        ]);
      }


      // Update session data
      $sessionData[$process_code][$index] = [
        'process_unit_price_id' => $id,
        'process_code' => $process_code,
        'effective_date' => $effective_date,
        'processing_unit_price' => $processing_unit_price,
        'datepicker' => $this->getDatePickerContent($effective_date, $index),
        'updator' => $request->user()->id,
        'updated_at' => now()->format('Y-m-d H:i:s')
      ];

      session(['process_setting' => $sessionData]);

      DB::commit();
      return session('process_setting');

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
      $ProcessUnitPrice = ProcessUnitPrice::findOrFail($request->process_unit_price_id);
      if ($ProcessUnitPrice) {
        $ProcessUnitPrice->delete();
      }

      $sessionData = session('process_setting', []);
      unset($sessionData[$request->process_code][$request->index]);
      $sessionData[$request->process_code] = array_values($sessionData[$request->process_code]);

      session(['process_setting' => $sessionData]);

      DB::commit();
      return $sessionData;
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


  public function getDatePickerContent($effective_date = null, $key = 0)
  {
    return view('partials._date_picker', [
      'inputName' => 'effective_date_setting_unit_price_' . $key,
      'dateFormat' => 'YYYYMMDD',
      'value' => $effective_date,
      'inputClass' => 'w-100 input-required',
    ])->render();
  }
}
