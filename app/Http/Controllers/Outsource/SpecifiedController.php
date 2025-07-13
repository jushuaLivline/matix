<?php

namespace App\Http\Controllers\Outsource;

use App\Http\Controllers\Controller;
use App\Services\Outsource\Delivery\SpecifiedService;
use App\Models\KanbanMaster;


use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class SpecifiedController extends Controller
{
  protected $specifiedService;
  public function __construct(SpecifiedService $specifiedService)
  {
    $this->kanban = new KanbanMaster();
    $this->specifiedService = $specifiedService;
  }

  public function index(Request $request)
  {
    return view('pages.outsource.delivery.specified.index');
  }

  public function pdf_export(Request $request)
  {
    if (!$request->all()) {
      // Define default empty values
      $request->merge([
        'part_classification' => '0',
      ]);
    }
    $this->specifiedService->pdf_export($request);
    return redirect()->route('outsource.delivery.specified.index', $request->query());
  }
}
