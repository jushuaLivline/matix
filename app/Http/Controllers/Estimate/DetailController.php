<?php

namespace App\Http\Controllers\Estimate;

use App\Exports\Order\ForecastExport;
use App\Http\Controllers\Controller;
use App\Models\UnofficialNotice;
use App\Models\Estimate;
use App\Models\EstimateReply;
use App\Models\EstimateReplyDetail;
use App\Services\TemporaryUploadService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Constants\Constant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class DetailController extends Controller
{

  // ESTIMATE-185
  public function show(Request $request, $id)
  {
    if (!request()->all()) {
      $request->merge([
        'id' => $id,
      ]);
    }
    $estimate = Estimate::search($request)->first();   
     
    return view("pages.estimate.detail.show", compact("estimate"));
  }
}
