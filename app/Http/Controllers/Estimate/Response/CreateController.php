<?php

namespace App\Http\Controllers\Estimate\Response;

use App\Exports\Order\ForecastExport;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Estimate;
use App\Models\EstimateReply;
use App\Models\EstimateReplyDetail;
use App\Services\TemporaryUploadService;
use App\Http\Requests\Estimate\Response\CreateRequest;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class CreateController extends Controller
{

  public function create(Request $request, $id)
  {
    if (!request()->all()) {
      $request->merge([
        'id' => $id,
      ]);
    }
    $estimate = Estimate::search($request)->first();
    $employees = Employee::where('authorization_code','000200')->get();

    if (!$estimate) {
      return redirect()->route('estimate.estimateSearch.index');
    }

    return view("pages.estimate.response.create.index", compact(
      'estimate',
      'employees'
    ));
  }
  public function edit(Request $request, $id)
  {
    $request->merge([
      'id' => $id,
    ]);

    $estimate = Estimate::search($request)->first();
    $estimateReply = EstimateReply::where('id', $request->reply_id)->first();
    $employees = Employee::where('authorization_code','000200')->get();;

    if (!$estimate) {
      return redirect()->route('estimate.estimateSearch.index');
    }

    return view("pages.estimate.response.edit.index", compact(
      'estimate',
      'estimateReply',
      'employees'
    ));
  }

  public function update(CreateRequest $request, $id)
  {

    DB::beginTransaction();
    try {
      // Retrieve validated estimate reply data from the request
      $getEstimateReplyData = $request->getEstimateReplyData();
      $getEstimateReplyDetailsData = $request->getEstimateReplyDetailsData();

      $estimateReply = EstimateReply::findOrFail($id);
      $estimateReply->update($getEstimateReplyData);

      foreach ($getEstimateReplyDetailsData as $detailData) {
        EstimateReplyDetail::updateOrCreate(
          ['estimate_id' => $request->estimate_id, 'id' => $detailData['id'] ?? null], // Optionally use 'id' if updating existing records
          $detailData
        );
      }

      DB::commit();
      return redirect()->route('estimate.estimateDetail.show', $request->estimate_id)->with('success', '見積回答の更新が完了しました');

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while storing the data.', [
        'error' => $e->getMessage(),
        'request' => $request->all(),
        'timestamp' => now(),
      ]);

      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  public function store(CreateRequest $request)
  {
    DB::beginTransaction();
    try {
      // Retrieve validated estimate reply data from the request
      $getEstimateReplyData = $request->getEstimateReplyData();
      $getEstimateReplyDetailsData = $request->getEstimateReplyDetailsData();

      // Insert data into the table
      EstimateReply::insert($getEstimateReplyData);
      EstimateReplyDetail::insert($getEstimateReplyDetailsData);

      DB::commit();
      return redirect()->route('estimate.estimateDetail.show', $request->estimate_id)->with('success', '見積回答の作成が完了しました');

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while storing the data.', [
        'error' => $e->getMessage(),
        'request' => $request->all(),
        'timestamp' => now(),
      ]);

      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  public function store_temp_file(CreateRequest $request)
  {

    $file = $request->file('file');
    $filename = now()->format('YmdHis') . '.' . $file->extension();
    $path = $file->storeAs('public/estimate', $filename);

    return response()->json(['name' => $filename, 'path' => $path]);
  }

  public function remove_temp_file(Request $request)
  {
    $filename = $request->input('filename');

    if (!$filename) {
      return response()->json(['success' => false, 'message' => 'Filename は必須です。'], 400);
    }

    $filePath = "public/estimate/" . $filename;

    if (Storage::exists($filePath)) {
      Storage::delete($filePath);
      return response()->json(['success' => true, 'message' => 'ファイルは正常に削除されました。']);
    }

    return response()->json(['success' => false, 'message' => 'ファイルが見つかりません。'], 404);
  }

  public function downloadFile($filename)
  {
    $filePath = "public/estimate/{$filename}";

    if (!Storage::exists($filePath)) {
      return redirect()->back()->with('error', 'ファイルが見つかりません。');
    }

    return Storage::download($filePath);
  }
}
