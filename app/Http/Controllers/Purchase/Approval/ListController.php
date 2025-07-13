<?php

namespace App\Http\Controllers\Purchase\Approval;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PurchaseRequisition;

use App\Http\Requests\Purchase\Approval\ListRequest;
use App\Services\Purchase\Approval\ListService;
use App\Exports\Purchase\Approval\ListExport;
use Illuminate\Support\Facades\Log;

class ListController extends Controller
{
    protected $listService;
    
    public function __construct(ListService $listService) {
        $this->listService = $listService;
    }

    public function index(ListRequest $request)
    {
        if (!$request->has('purpose') && (!$request->has('request_date_from') || !$request->has('request_date_to'))) {
            $purpose = 1;
            $request_date_from = Carbon::now()->startOfMonth()->format('Ymd');
            $request_date_to = Carbon::now()->endOfMonth()->format('Ymd');
      
            return redirect()->route('purchase.approval.list.index', compact('purpose', 'request_date_from', 'request_date_to'));
          }

        $filters = $request->validated();
        $filters = $request->user()->employee_code ? array_merge($filters, ['current_user' => $request->user()->employee_code]) : $filters;

        $purposes = Constant::PURCHASE_REQUISITION_SEARCH_PURPOSE;
        $datas = PurchaseRequisition::search($filters);

        $cacheKey = 'purchase_requisition_search_' . md5(json_encode($filters));

        // Cache will be available only in 10 minutes
        Cache::put($cacheKey, $filters, 600);

        return view('pages.purchase.approval.list.index', compact('datas','purposes','cacheKey'));
    }

    public function requisition_approval(Request $request)
    {
        $response = $this->listService->purchaseRequisitionApprovalProcess($request);

        if ($response['status'] === 'success') {
            return back()->with('success', $response['message']);
        } else {
            return back()->with('error', $response['message']);
        }
    }

    public function excel_export(Request $request)
    {
        $cacheKey = $request->input('cache_key');
        $filters = Cache::get($cacheKey, []);

        if (empty($filters)) {
            return back()->with('error', '検索セッションが期限切れです。再度検索を実行してください。');
        }

        $purchaseRequisitions = PurchaseRequisition::search($filters, true);

        $perPage = $request->per_page ?? 10;
        $page = $request->page ?? 1;
        $datas = $purchaseRequisitions->slice(($page - 1) * $perPage, $perPage)->values();

        $fileName = '購買依頼一覧' . now()->format('Ymd') . '.xlsx';

        Cache::forget($cacheKey);

        return Excel::download(
            new ListExport($datas),
            $fileName
        );
    }
    
}