<?php
namespace App\Http\Controllers\Outsource;

use App\Http\Controllers\Controller;
use App\Exports\Outsource\SupplyExcelExport;
use App\Models\SubcontractSupply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Constants\Constant;
use App\Http\Requests\Outsource\Supply\SupplyRequest;

use App\Models\Customer;

use App\Services\Outsource\Supply\SubcontractSupplyService;

class SupplyController extends Controller
{
    private $subcontractSupplyService;

    public function __construct(SubcontractSupplyService $subcontractSupplyService) {
        $this->subcontractSupplyService = $subcontractSupplyService;
    }

    public function index(Request $request) {
        if(!$request->all()){
            $request->merge([
                'supply_date_from' => Carbon::now()->startOfMonth()->format('Ymd'),
                'supply_date_to' => Carbon::now()->endOfMonth()->format('Ymd'),
                'supply_flight_number_from' => $request->input('supply_flight_number_from'),
                'supply_flight_number_to' => $request->input('supply_flight_number_to'),
                'supplier_process_code' => $request->input('supplier_process_code'),
                'subcontract_supply_no' => $request->input('subcontract_supply_no'),
            ]);
        }

        $paginationThreshold = Constant::PAGINATION_THRESHOLD;
        $subcontract_supply = SubcontractSupply::search($request)->paginateResults($paginationThreshold);
        return view('pages.outsource.supply.index', compact('subcontract_supply'));
    }

    public function edit(Request $request, $id) {
        $subcontract_supply = $this->subcontractSupplyService->edit($id);
        $request_data = $request->all();
    
        return view('pages.outsource.supply.edit', compact(
          'subcontract_supply',
          'request_data'
        ));
    }

    public function update(SupplyRequest $request, $id) {
        // Start a transaction
        DB::beginTransaction();

        try {
            $this->subcontractSupplyService->update($request->validated(), $id);

            // Commit the transaction
            DB::commit();
            $message = "下請け供給が正常に更新されました";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error occurred while updating subcontract supply', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'timestamp' => now(),
            ]);

            // Handle the error, log it or display a custom error message
            return redirect()->back()->with('error', 'Error occurred while updating subcontract supply');
        }
    }

    public function excel_export(Request $request){
        $outsourcedProcesses = SubcontractSupply::search($request)->paginateResults();
        return Excel::download(new SupplyExcelExport($outsourcedProcesses), '下請け供給データ' . now()->format('Ymd') . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }

    public function getSupplierName(Request $request){
        $process_code = $request->input('process_code');
        $process = Customer::where('customer_code', $process_code)->first();

        if ($process) {
            return response()->json([
                'status' => 'success',
                'message' => 'Process found',
                'process_name' => $process->customer_name
            ]);
        } else {
            return response()->json([
                'status' => 'fail',
                'message' => '取引先が見つかりません'
            ], 404);
        }
    }
}