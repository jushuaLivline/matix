<?php

namespace App\Http\Controllers\Purchase;

use App\Exports\Purchase\ActualExport;
use Exception;
use App\Models\Code;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\PurchaseRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Purchase\ActualService;
use Illuminate\Support\Facades\Route;


class ActualController extends Controller
{
    protected $actualService;

    public function __construct(ActualService $actualService)
    {
        $this->actualService = $actualService;
    }

    public function index(Request $request)
    {
        $paginationThreshold = 10;
        
        // Once the search button is clicked, the start and end date will be set to the input value
        if ($request->has('search')) {
            $startDate = $request->filled('start_date') ? $request->start_date : '';
            $endDate = $request->filled('end_date') ? $request->end_date : '';
        } else {
            $startDate = now()->startOfMonth()->format('Ymd');
            $endDate = now()->endOfMonth()->format('Ymd');
        }

        $datas = PurchaseRecord::search($request)->paginateResults($paginationThreshold);

        return view('pages.purchase.actual.index', [
            'datas' => $datas,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    // PURCHASE 62 & 63
    public function create(Request $request) {
        $routeName = Route::currentRouteName();
        $view = ($routeName == 'purchase.purchaseProduction') ? 'pages.purchase.actual.production.create' : 'pages.purchase.actual.item.create';
        
        $codes = Code::where('division', '単位')->get();
        return view($view, compact('codes'));
    }
    
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $this->actualService->store($request);

            DB::commit();
            $message =  ($request->purchase_category == 1) ? "生産品の登録が完了しました。" : "購買品の登録が完了しました。";

            return redirect()->route('purchase.purchaseActual.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '登録中にエラーが発生しました。');
        }
    }

    public function copy_previous_input(Request $request)
    {
        $purchase_category = $request->get('purchase_category');
        $data = $this->actualService->copy_previous_input($purchase_category);

        return redirect()->route('purchase.purchaseActual.index')->with('previous_data', $data);
    }
    public function duplicate($id)
    {
        $data = $this->actualService->edit($id);
        $codes = Code::where('division', operator: '単位')->get();
        return view('pages.purchase.actual.duplicate', compact('codes', 'data'));
    }




    public function edit($id)
    {
        $data = $this->actualService->edit($id);
        $codes = Code::where('division', '単位')->get();
        $routeName = Route::currentRouteName();
        $view = ($routeName == 'purchase.purchaseProduction.edit') ? 'pages.purchase.actual.production.edit' : 'pages.purchase.actual.item.edit';
        
        return view($view, compact('data', 'codes', 'id'));
    }

    
    public function update(Request $request, $id)
    {
        
        try {
            DB::beginTransaction();

            $this->actualService->update($request, $id);

            DB::commit();

            $routeName = Route::currentRouteName();
            $message =  ($routeName == 'purchase.purchaseProduction.update') ? "生産品の更新が完了しました。" : "購買品の更新が完了しました。";


            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error($e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '更新中にエラーが発生しました。');
        }
    }

    public function excel_export(Request $request)
    {
        $datas = [];
        // Check if there are any query parameters and apply filtering if present
        if ($request->query()) {
            // Retrieve only the data from the current page
            $datas = PurchaseRecord::search($request)->paginate($request->per_page ?? 10)->items();
        }

        // Generate the file name with the current date
        $fileName = '購入実績検索・一覧-' . now()->format('Ymd') . '.xlsx';

        // Export the data using the specified export class and file name        
        return Excel::download(new ActualExport($datas), $fileName);  
    }
}
