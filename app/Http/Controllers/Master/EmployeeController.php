<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Exports\Master\EmployeeExport;
use App\Models\Employee;
use App\Models\Authority;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Master\EmployeeRequest;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employee_records = Employee::search($request)
            ->with('department','authority')
            ->orderByDesc('created_at')
            ->paginateResults(10);

        $authority = Authority::all();
        
        return view('pages.master.employee.index', compact('employee_records','authority'));
    }

    public function excel_export(Request $request)
    {
        $employee_records = Employee::search($request)
            ->with('department','authority')
            ->paginateResults(10);

        // Get only the records for the current page
        $currentPageRecords = collect($employee_records->items());

        $exportData = $currentPageRecords->map(function ($employee) {
            return [
                $employee->employee_code,
                $employee->employee_name,
                $employee->department?->department_name,
                $employee->authority?->authority_name,
            ];
        });
        
        $fileName = '社員マスタ一覧' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new EmployeeExport($exportData), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function create(Request $request) 
    {
        $authority = Authority::all();
        $post_type = 'create';
        return view('pages.master.employee.edit', compact('authority', 'post_type'));
    }

    public function store(EmployeeRequest $request)
    {
        DB::beginTransaction(); 

        try {
            $request_data= $request->validated();
            $request_data['password'] = Hash::make($request->password);
            
            $employee = Employee::create($request_data);

            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => '社員の登録が完了しました',
                'employee' => $employee,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Employee registration failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id) {
        $data = Employee::where('id',$id)
            ->with('department')
            ->get()->first();

        $authority = Authority::all();
        $post_type = 'update';
        return view('pages.master.employee.edit', compact('data','authority', 'post_type'));
    }

    public function update(EmployeeRequest $request, $id) {
        try {
            DB::beginTransaction(); 
            $request_data = $request->validated();
            $request_data['password'] = Hash::make($request->password);
            
            $employee = Employee::findOrFail($id);
            $employee->update($request_data);

            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => '従業員の更新が完了しました',
                'employee' => $employee,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Employee update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}