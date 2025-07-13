<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;

use App\Exports\Master\SupplierExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CustomerService;
use App\Http\Requests\Master\SupplierRequest;

class SupplierController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService )
    {
      $this->customerService = $customerService;
    }

    public function index(Request $request)
    {
        $queryParams = $request->except('page');

        if (isset($queryParams['customer_flag']) && $queryParams['customer_flag'] === 'all') {
            unset($queryParams['customer_flag']);
        }
    
        if (isset($queryParams['delete_flag']) && $queryParams['delete_flag'] === 'all') {
            unset($queryParams['delete_flag']);
        }
        $customer_records = Customer::where($queryParams)->orderByDesc('created_at')->paginateResults(10);
        //Log::info('Records Fetched:', ['data' => $queryParams]);

        return view('pages.master.supplier.index',compact('customer_records'));
    }

    public function excel_export(Request $request)
    {
        // Exclude 'page' from query parameters
        $queryParams = $request->except('page');

        if (isset($queryParams['customer_flag']) && $queryParams['customer_flag'] === 'all') {
            unset($queryParams['customer_flag']);
        }

        if (isset($queryParams['delete_flag']) && $queryParams['delete_flag'] === 'all') {
            unset($queryParams['delete_flag']);
        }

        $customer_records = Customer::where($queryParams)->paginateResults(10);
        $currentPageRecords = collect($customer_records->items());

        $exportData = [];

        for ($i = 0; $i < count($currentPageRecords); $i++) {
            $customer = $currentPageRecords[$i];

            // Format post_code
            $post = preg_replace('/\D/', '', $customer->post_code);
            if (strlen($post) >= 4) {
                $formattedPostCode = substr($post, 0, 3) . '-' . substr($post, 3, 4);
            } else {
                $formattedPostCode = $post;
            }

            // Format telephone_number
            $tel = preg_replace('/\D/', '', $customer->telephone_number);
            if (strlen($tel) > 0) {
                // Alternate formats based on index
                if (preg_match('/^(090|080|070)/', $tel)) {
                    $formattedTelephone = substr($tel, 0, 3) . '-' . substr($tel, 3, 3) . '-' . substr($tel, 6, 5);
                } else {
                    $formattedTelephone = substr($tel, 0, 2) . '-' . substr($tel, 2, 4) . '-' . substr($tel, 6, 5);
                }
            } else {
                $formattedTelephone = $tel;
            }

            if (strlen($tel) <= 6) {
                $formattedTelephone = rtrim($formattedTelephone, '-');
            }

            $exportData[] = [
                $customer->customer_code,
                $customer->customer_name,
                $formattedPostCode,
                $customer->address_1,
                $formattedTelephone,
                $customer->customer_flag == 1 ? 'o' : 'x', 
                $customer->supplier_tag == 1 ? 'o' : 'x', 
            ];
        }

        // Set the filename for the download
        $fileName = '取引先マスタ一覧' . now()->format('Ymd') . '.xlsx';

        // Return the Excel file download
        return Excel::download(new SupplierExport($exportData), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }


    public function create(Request $request) 
    {
        return view('pages.master.supplier.edit');
    }

    public function store(SupplierRequest $request)
    {
        try {
            DB::beginTransaction(); 

            $customer = $this->customerService->createCustomer($request->validated());

            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => '取引先マスタが正常に作成されました。',
                'supplier' => $customer,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Supplier creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function edit($id) {
        $data = Customer::where('id',$id)->get()->first();

        return view('pages.master.supplier.edit', compact('data'));
    }

    public function update(SupplierRequest $request, $id) {
        try {
            DB::beginTransaction(); 
            
            $supplier = Customer::findOrFail($id);

            $supplier->update($request->validated());

            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => '取引先マスタが正常に更新されました',
                'supplier' => $supplier,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Supplier creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}