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

class ProcessSequenceService
{

  public function getProcessOrder(Request $request)
  {

    // Get the record from mst_process_orders table
    $processes = ProcessOrder::search($request)->orderBy('process_order')->get();
    // Destroy session
    if (session()->has('process_order')) {
      session()->forget('process_order');
    }

    $io_division = [
      '1' => '社内',
    ];

    // Prepare data for session
    $sessionData = session('process_order', []);
    foreach ($processes as $key => $value) {
      $sessionData[] = [
        'process_order_id' => $value->id,
        'process_order' => $value->process_order,
        'process_code' => $value->process_code ?? '',
        'process_name' => $value?->process?->process_name ?? '',
        'process_details' => $value->process_details ?? '',
        'packing' => $value->packing ?? '',
        'inside_and_outside_division' => $io_division[$value?->process?->inside_and_outside_division] ?? '社外',
        'processing_unit_price' => $value?->processUnitPrice->processing_unit_price ?? 0,
        'process_modal' => $this->getProcessModalContent($value->process_order),
        'creator' => $value->creator ?? null,
        'updator' => $value->updator ?? null,
        'updated_at' => $value->updated_at ?? null,
      ];
    }
    //Store data in session and order by process_order
    session(['process_order' => $sessionData]);
    $sortedSessionData = session('process_order');
    usort($sortedSessionData, function ($a, $b) {
      return $a['process_order'] - $b['process_order'];
    });

    return $sortedSessionData;
  }

  public function saveSession(Request $request)
  {
    $data = $request->data;
    $request->merge(['process_code' => $data['process_code']]);

    // Retrieve process and unit price
    $process = Process::where('process_code', $data['process_code'])->first();
    $unitPrice = ProcessUnitPrice::filterByPartAndProcess($request)->first();

    // Get session data
    $sessionData = session('process_order', []);

    // Determine next process order number
    $process_order = count($sessionData) > 0 ? end($sessionData)['process_order'] + 1 : 1;

    // Determine inside/outside division
    $inside_and_outside_division = $process?->inside_and_outside_division == 1 ? '社内' : '社外';

    // Add new process order to session
    $sessionData[] = [
      'process_order' => $process_order,
      'process_code' => $data['process_code'] ?? '',
      'process_name' => $process?->process_name ?? '',
      'process_details' => $data['process_details'] ?? '',
      'packing' => $data['packing'] ?? '',
      'inside_and_outside_division' => $inside_and_outside_division,
      'processing_unit_price' => $unitPrice?->processing_unit_price ?? 0,
      'process_modal' => $this->getProcessModalContent($process_order),
      'creator' => $request->user()->id,
    ];

    // Store updated session data
    session(['process_order' => $sessionData]);

    // Sort session data by process_order
    usort($sessionData, fn($a, $b) => $a['process_order'] <=> $b['process_order']);

    return $sessionData;
  }


  public function updateSessionRow(Request $request, $id)
  {

    DB::beginTransaction();
    try {
      $index = $request->input('index');
      $process_code = $request->input('process_code');
      $process_details = $request->input('process_details');
      $packing = $request->input('packing') ?? '';

      // Retrieve process and unit price
      $process = Process::where('process_code', $process_code)->first();
      $unitPrice = ProcessUnitPrice::filterByPartAndProcess($request)->first();

      // Get existing session data
      $sessionData = session('process_order', []);

      if (!isset($sessionData[$index])) {
        return response()->json(['error' => 'Invalid index.'], 400);
      }

      // Update process order ID
      if($id && $id > 0){
        $updateProcessOrder = ProcessOrder::findOrFail($id);
        $updateProcessOrder->fill([
          'process_code'     => $process_code,
          'process_details'  => $process_details,
          'packing'          => $packing,
          'updator'          => $request->user()->id,
          'updated_at'       => now()->format('Y-m-d H:i:s'), // Carbon instance, no need to format
        ])->save();
      }


      // Determine inside/outside division
      $inside_and_outside_division = $process?->inside_and_outside_division == 1 ? '社内' : '社外';

      // Update session data
      $sessionData[$index] = [
        'process_order_id' => $id,
        'process_order' => $sessionData[$index]['process_order'],
        'process_code' => $process_code ?? '',
        'process_name' => $process->process_name ?? '',
        'process_details' => $process_details,
        'packing' => $packing,
        'inside_and_outside_division' => $inside_and_outside_division,
        'processing_unit_price' => $unitPrice->processing_unit_price ?? 0,
        'process_modal' => $this->getProcessModalContent($sessionData[$index]['process_order']),
        'updator' => $request->user()->id,
        'updated_at' => now()->format('Y-m-d H:i:s'),
      ];

      // Update session
      session(['process_order' => $sessionData]);

      // Sort session data by process_order
      usort($sessionData, fn($a, $b) => $a['process_order'] <=> $b['process_order']);

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


  // DELETE DATA FROM SESSION
  public function deleteSessionRow(Request $request)
  {
    DB::beginTransaction();
    try {
      // Find and delete the process order
      $ProcessOrder = ProcessOrder::findOrFail($request->process_order_id);
      if ($ProcessOrder) {
        $ProcessOrder->delete();
      }

      // Retrieve session data
      $sessionData = session('process_order', []);

      if (isset($sessionData[$request->index])) {
        array_splice($sessionData, $request->index, 1); // Remove the specific index

        // Reindex the process orders
        foreach ($sessionData as $key => &$value) {
          $value['process_order'] = $key + 1;
        }
        unset($value);

        // Update session data
        session(['process_order' => $sessionData]);
      }

      DB::commit();
      return $sessionData;

    } catch (\Exception $e) {
      DB::rollBack();

      // Log the error with structured data
      Log::error('Error deleting process order', [
        'error' => $e->getMessage(),
        'process_order_id' => $request->process_order_id,
        'timestamp' => now(),
      ]);

      return redirect()->back()->with('error', 'Error occurred while deleting the record.');
    }
  }

  // DELETE DATA FROM DB
  public function destroy(Request $request, $partNumber)
  {
    DB::beginTransaction();
    try {

      $processOrder = ProcessOrder::where('part_number', $request->part_number)->get();
      if (!empty($processOrder)) {
        ProcessOrder::where('part_number', $request->part_number)->delete();
        ProcessUnitPrice::where('part_number', $request->part_number)->delete();
      }
      // Commit the transaction
      DB::commit();

      // Clear the session data after insertion
      session()->forget('process_order');
      return redirect()->route('master.part.edit', ['id' => $request->product_id])->with('success', '工程順序設定の削除が完了しました');

    } catch (\Exception $e) {
      // Rollback the transaction if something went wrong
      DB::rollBack();

      // Log the error with detailed information
      Log::error('Error occurred while deleting the recored', [
        'error' => $e->getMessage(),
        'timestamp' => now(),
      ]);

      // Handle the error, log it or display a custom error message
      return redirect()->back()->with('error', 'Error occurred while deleting the recored.');
    }
  }
  // Save all session records to the database
  public function store(Request $request)
  {
    // Delete existing process orders for the given part_number
    ProcessOrder::where('part_number', $request->part_number)->delete();

    // Check if session has process_order data
    if ($processOrders = session('process_order')) {
      $data = array_map(function ($item) use ($request) {
        return [
          'part_number' => $request->part_number,
          'process_order' => $item['process_order'],
          'process_code' => $item['process_code'],
          'process_details' => $item['process_details'],
          'packing' => $item['packing'],
          'creator' => $item['creator'] ?? null,
          'updator' => $item['updator']?? null,
          'updated_at' => $item['updated_at']?? null,
        ];
      }, $processOrders);

      // Bulk insert to optimize performance
      ProcessOrder::insert($data);

      // Clear session data
      session()->forget('process_order');
    }

    return redirect()->route('master.part.edit', $request->product_id)
      ->with('success', '工程順序設定の登録が完了しました');
  }


  public function changeOrderRow(Request $request)
  {
    $sessionData = session('process_order', []);

    $index = $request->input('index'); // Index of the second element
    $direction = $request->input('direction'); // Direction: 'up' or 'down'
    $processOrderId = $request->input('processOrderId') ?? '';

    if (($direction === 'up' && $index > 0) || ($direction === 'down' && $index < count($sessionData) - 1)) {
      // Swap elements
      $temp = $sessionData[$index];
      $sessionData[$index] = $sessionData[$index + ($direction === 'up' ? -1 : 1)];
      $sessionData[$index + ($direction === 'up' ? -1 : 1)] = $temp;

      // Update process_order values
      $sessionData = array_values($sessionData);
      for ($i = 0; $i < count($sessionData); $i++) {
        $sessionData[$i]['process_order'] = $i + 1;
      }
    }

    session(['process_order' => $sessionData]);
    usort($sessionData, function ($a, $b) {
      return $a['process_order'] - $b['process_order'];
    });

    return $sessionData;
  }

  public function getProcessModalContent($key = 0)
  {
    return view('partials.modals.masters._search', [
      'modalId' => 'searchProcessModal-' . $key,
      'searchLabel' => '工程コード',
      'resultValueElementId' => 'process_code_' . $key,
      'resultNameElementId' => 'process_name_' . $key,
      'model' => 'Process'
    ])->render();
  }
}
