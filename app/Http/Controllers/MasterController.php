<?php

namespace App\Http\Controllers;

use App\Constants\ProductConstant;
use App\Exports\CustomersExport;
use App\Exports\DepartmentsExport;
use App\Exports\KanbanMasterExport;
use App\Exports\LinesExport;
use App\Exports\ProcessesExport;
use App\Exports\ProductsExport;
use App\Models\Calendar;
use App\Models\Code;
use App\Models\Configuration;
use App\Models\Department;
use App\Models\Line;
use App\Models\ManufacturerInfo;
use App\Models\ProductNumber;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Customer;
use App\Models\KanbanMaster;
use App\Models\ProductPrice;
use App\Models\Process;
use App\Models\ProcessUnitPrice;
use App\Services\ConfigurationService;
use App\Services\CustomerService;
use App\Services\DepartmentService;
use App\Services\KanbanService;
use App\Services\LineService;
use App\Services\ProcessesService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Constants\KanbanClassification;

class MasterController extends Controller
{
    public $customerService;
    public $lineService;
    public $kanbanService;
    public $departmentService;
    public $processService;
    public $configurationService;

    public function __construct()
    {
        $this->customerService = new CustomerService;
        $this->lineService = new LineService;
        $this->kanbanService = new KanbanService;
        $this->departmentService = new DepartmentService;
        $this->processService = new ProcessesService;
        $this->configurationService = new ConfigurationService;
    }

    public function products(Request $request)
    {
        $request->session()->forget('last_input');

        $productCategory = [
            0 => '材料',
            1 => '製品',
            2 => '試作品',
            3 => '購入材',
            4 => '仕掛品'
        ];

        $query = ProductNumber::with(['department', 'customer', 'line', 'supplier'])
            ->where(function ($query) use ($request) {
                if ($request->delete_flag != '2') {
                    $query->where('delete_flag', $request->delete_flag);
                }
            })
            ->when($request->part_number, fn($query) => $query->where("part_number", "LIKE", "%{$request->part_number}%"))
            ->when($request->product_name, fn($query) => $query->where("product_name", "LIKE", "%{$request->product_name}%"))
            ->when($request->line_code, fn($query) => $query->where("line_code", $request->line_code))
            ->when($request->department_code, fn($query) => $query->where("department_code", $request->department_code))
            ->when($request->supplier_code, fn($query) => $query->where("supplier_code", $request->supplier_code))
            ->when($request->product_code, fn($query) => $query->where("product_code", "LIKE", "%{$request->product_code}%"))
            ->when($request->customer_code, fn($query) => $query->where("customer_code", $request->customer_code))
            ->when($request->product_category, fn($query) => $query->where("product_category", $request->product_category - 1))
            ->when($request->production_division, fn($query) => $query->where("production_division", $request->production_division - 1));

        // Get the count of products
        $count = $query->count();

        // Paginate results
        $products = $query->paginate(20)->appends($request->except('page'));

        // Prepare view data
        return view('pages.master.products.index', [
            'productCategory' => $productCategory,
            'products' => $products,
            'count' => $count,
            'productionDivision' => ProductConstant::PRODUCTION_DIVISION,
        ]);
    }


    public function productCreate(Request $request)
    {
        if (session()->has('items')) {
            session()->forget('items');
        }
        $last_input = [];
        if ($request->session()->has('last_input')) {
            $last_input = $request->session()->get('last_input');
        }

        $units = Code::selectRaw("code as id, name as name")->where('division', '単位')->get();
        return view('pages.master.products.create', [
            'productCategory' => ProductConstant::CATEGORY,
            'instructionClass' => ProductConstant::INSTRUCTION_CLASS,
            'productionDivision' => ProductConstant::PRODUCTION_DIVISION,
            'units' => $units,
            'last_input' => $last_input
        ]);
    }

    public function productDuplicate(Request $request)
    {
        $last_input = [];
        if ($request->session()->has('last_input')) {
            $last_input = $request->session()->get('last_input');
        }

        return response()->json($last_input);
    }

    public function productStore(Request $request)
    {
        $inputs = $request->except('_token');
        $inputs['delete_flag'] = isset($inputs['delete_flag']) ? 1 : 0;
        $product = ProductNumber::create($inputs);

        if ($request->session()->has('last_input')) {
            $request->session()->forget('last_input');
        }

        $request->session()->put(['last_input' => $inputs]);

        return redirect()->route('master.products.create')->with('success', 'データは正常に登録されました');
    }

    public function productDelete($id)
    {
        $product = ProductNumber::find($id)
                    ->update([
                        'delete_flag' => 1
                    ]);

        return redirect()->route('master.products.index');
    }

    public function productHardDelete($id)
    {
        $product = ProductNumber::find($id)
                    ->delete();

        return redirect()->route('master.products.index');
    }

    public function productEdit($id)
    {
        if (session()->has('items')) {
            session()->forget('items');
        }
        
        $today = date('Y-m-d 00:00:00');
        $product = ProductNumber::find($id);
        $product_prices = ProductPrice::where(function($query) use ($today) {
                            $query->where('effective_date', '<=', $today);
                        })
                        ->where('part_number', $product->part_number)
                        ->orderBy('effective_date', 'desc')
                        ->first();
        if ($product_prices != null) {
            $product_prices = $product_prices->toArray();
        }
        $units = Code::selectRaw("code as id, name as name")->where('division', '単位')->get();
        $departments = Department::selectRaw("code as id, name as name")->get();
        $customers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '0')->get();
        $suppliers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '1')->get();
        $lines = Line::selectRaw("line_code as id, line_name as name")->get();
        $departmentMap = [];
        foreach ($departments as $department) {
            $departmentMap[$department->id] = $department;
        }

        $customerMap = [];
        foreach ($customers as $customer) {
            $customerMap[$customer->id] = $customer;
        }

        $supplierMap = [];
        foreach ($suppliers as $supplier) {
            $supplierMap[$supplier->id] = $supplier;
        }

        $lineMap = [];
        foreach ($lines as $line) {
            $lineMap[$line->id] = $line;
        }

        if (isset($departmentMap[$product->department_code])) {
            $product->department = $departmentMap[$product->department_code]->name;
        }
        if (isset($customerMap[$product->customer_code])) {
            $product->customer = $customerMap[$product->customer_code]->name;
        }
        if (isset($supplierMap[$product->supplier_code])) {
            $product->supplier = $supplierMap[$product->supplier_code]->name;
        }
        if (isset($lineMap[$product->line_code])) {
            $product->line = $lineMap[$product->line_code]->name;
        }

        // =========== CONFIGURATIONS ================
        $configurations = Configuration::where('parent_part_number', $product->part_number)->where('delete_flag', '!=', 1)->get();

        // dd($configurations);
        foreach ($configurations as $conf) {
            $prod_name = ProductNumber::where('part_number', $conf->child_part_number)->value('product_name');
            $conf->product_name = $prod_name;
        }
        // =========== END CONFIGURATIONS ============

        $insideProcess = ProcessUnitPrice::select('process_unit_prices.processing_unit_price')
                            ->leftJoin('processes', 'processes.process_code', '=', 'process_unit_prices.process_code')
                            ->where(function($query) use ($today) {
                                $query->where('process_unit_prices.effective_date', '>=', $today)
                                    ->orWhere('process_unit_prices.effective_date', '=', $today);
                            })
                            ->where('process_unit_prices.part_number', $product->part_number)
                            ->where('processes.inside_and_outside_division', 1)
                            ->where('processes.delete_flag', 0)
                            ->orderBy('process_unit_prices.effective_date', 'asc')
                            ->first();
        if ($insideProcess != null) {
            $insideProcess = $insideProcess->toArray();
        }
        $outsideProcess = ProcessUnitPrice::select('process_unit_prices.processing_unit_price')
                            ->leftJoin('processes', 'processes.process_code', '=', 'process_unit_prices.process_code')
                            ->where(function($query) use ($today) {
                                $query->where('process_unit_prices.effective_date', '>=', $today)
                                    ->orWhere('process_unit_prices.effective_date', '=', $today);
                            })
                            ->where('process_unit_prices.part_number', $product->part_number)
                            ->where('processes.inside_and_outside_division', 2)
                            ->where('processes.delete_flag', 0)
                            ->orderBy('process_unit_prices.effective_date', 'asc')
                            ->first();
        if ($outsideProcess != null) {
            $outsideProcess = $outsideProcess->toArray();
        }
        $child_part_numbers = Configuration::where('parent_part_number', $product->part_number)->where('delete_flag', 0)->get();
        $mcup = 0;
        if ($child_part_numbers != null) {
            $child_part_numbers = $child_part_numbers->toArray();
            $child_part_numbers = array_column($child_part_numbers, 'child_part_number');
            $mcu_price = ProductPrice::selectRaw('part_number, effective_date, sell_price, unit_price, sell_price - unit_price as mcu_price')
                            ->whereIn('part_number', $child_part_numbers)
                            ->where(function($query) use ($today) {
                                $query->where('effective_date', '>=', $today)
                                    ->orWhere('effective_date', '=', $today);
                            })
                            ->get();
            if ($mcu_price != null) {
                foreach ($mcu_price as $key => $value) {
                    $mcup += $value->mcu_price;
                }
            }
        }
        return view('pages.master.products.create', [
            'productCategory' => ProductConstant::CATEGORY,
            'instructionClass' => ProductConstant::INSTRUCTION_CLASS,
            'productionDivision' => ProductConstant::PRODUCTION_DIVISION,
            'units' => $units,
            'product' => $product,
            'configurations' => $configurations,
            'productPrices' => $product_prices,
            'insideProcess' => $insideProcess,
            'outsideProcess' => $outsideProcess,
            'materialComponentUnitPrice' => $mcup
        ]);
    }

    public function productUpdate($id, Request $request)
    {
        $inputs = $request->except('_token');
        $inputs['delete_flag'] = isset($inputs['delete_flag']) ? 1 : 0;
        $product = ProductNumber::find($id)->update($inputs);

        return back()->with('success', 'データは正常に登録されました');
    }

    public function exportCSV(Request $request)
    {
        $products = ProductNumber::query()
                        ->when($request->part_number, function($query) use ($request) {
                            $query->where("part_number", "LIKE", "%". $request->part_number . "%");
                        })
                        ->when($request->product_name, function($query) use ($request) {
                            $query->where("product_name", "LIKE", "%". $request->product_name . "%");
                        })
                        ->when($request->line_code, function($query) use ($request) {
                            $query->where("line_code", "LIKE", "%". $request->line_code . "%");
                        })
                        ->when($request->department_code, function($query) use ($request) {
                            $query->where("department_code", "LIKE", "%". $request->department_code . "%");
                        })
                        ->when($request->supplier_code, function($query) use ($request) {
                            $query->where("supplier_code", "LIKE", "%". $request->supplier_code . "%");
                        })
                        ->when($request->product_code, function($query) use ($request) {
                            $query->where("product_code", "LIKE", "%". $request->product_code . "%");
                        })
                        ->when($request->product_category, function($query) use ($request) {
                            $query->where("product_category", $request->product_category);
                        })
                        ->when($request->production_division, function($query) use ($request) {
                            $query->where("production_division", $request->production_division);
                        })
                        ->when($request->delete_flag, function($query) use ($request) {
                            if ($request->delete_flag == 1) {
                                $query->where("delete_flag", 1);
                            }
                        })
                        ->selectRaw('
                            part_number,
                            product_name,
                            line_code,
                            department_code,
                            customer_code,
                            supplier_code,
                            product_category
                            ')
                        ->get();

        $fileName = '品番マスタ一覧.xlsx';
        return Excel::download(new ProductsExport($products), $fileName);

    }

    public function productionSimulation()
    {
        $fileName = 'production_simulation.csv';
        $filePath = public_path('csv/' . $fileName);

        if (file_exists($filePath)) {
            return response()->download($filePath, $fileName);
        } else {
            return response()->json(['error' => 'File not found'], 404);
        }
    }

    // ======================= mst customers ======================= //
    public function customers (Request $request)
    {
        Session::forget('customer_data');

        if ($request->all()) {
            $customer_code = $request->input('customer_code');
            $customer_name = $request->input('customer_name');
            $customer_flag = $request->input('customer_flag');
            $delete_flag = $request->input('delete_flag');

            $query = Customer::query();

            if ($customer_code) {
                $query->where('customer_code', 'LIKE', '%' . $customer_code . '%');
            }

            if ($customer_name) {
                $query->where('customer_name', 'LIKE', '%' . $customer_name . '%');
            }

            if ($customer_flag) {
                if ($customer_flag == "1") {
                    $query->where('customer_flag', $customer_flag);

                }else if ($customer_flag == "2") {
                    $query->where('supplier_tag', 1);

                }
            }

            if (in_array($delete_flag, ['0', '1'])) {
                if ($delete_flag == 'すべて') {
                    $query->whereIn('delete_flag',  ['0','1']);
                } else {
                    $query->where('delete_flag',  $delete_flag);
                }
            }

            $customers = $query->paginate(20);

            $customers->appends([
                'customer_code' => $customer_code,
                'customer_name' => $customer_name,
                'customer_flag' => $customer_flag,
                'delete_flag' => $delete_flag
            ]);
        }

        return view('pages.master.customers.customers', [
            'customers' => $customers ?? [],
            'customer_code' => $request->input('customer_code'),
            'customer_name' => $request->input('customer_name'),
            'customer_flag' => $request->input('customer_flag'),
            'delete_flag' => $request->input('delete_flag')
        ]);
    }

    public function customerSearch (Request $request)
    {
        return redirect()->route('master.index', [
            'customer_code' => $request->input('customer_code'),
            'customer_name' => $request->input('customer_name'),
            'customer_flag' => $request->input('customer_flag'),
            'delete_flag' => $request->input('delete_flag')
        ]);
    }

    public function customerCreateOrUpdate ($id = null)
    {
        if (!empty($id)) {
            $customer = Customer::findOrFail($id);
        }else{
            $customer = new Customer();
        }

        return view('pages.master.customers.customer-edit-or-create', ['customer' => $customer]);
    }

    public function update (Request $request, $id)
    {
        $customer = Customer::find($id);

        $customMessages = $this->customerService->customerErrorMessage();

        $rules = [
            'customer_code' => [
                'required',
                Rule::unique('customers', 'customer_code')->ignore($customer->id),
            ],
            'customer_name' => 'required',
            'supplier_name_abbreviation' => 'required',
            'bill_ratio' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $updated = $this->customerService->updateCustomer($request, $id);

        if ($updated) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to update record
            $request->session()->put('error', '一致するアカウント情報がみつかりませんでした');
        }

        return redirect()->back();
    }

    public function store (Request $request)
    {
        $customMessages = $this->customerService->customerErrorMessage();

        $validator = Validator::make($request->all(), [
            'customer_code' => 'required|unique:customers,customer_code',
            'customer_name' => 'required',
            'supplier_name_abbreviation' => 'required',
            'bill_ratio' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ], $customMessages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $customer = $this->customerService->createCustomer($request->all());

        // Store the data in the session
        $request->session()->put('customer_data', $request->except('_token'));

        if ($customer) {
            // Record created successfully
            $request->session()->put('success', 'レコードが正常に作成されました');
        } else {
            // Failed to create record
            $request->session()->put('error',  "一致するアカウント情報がみつかりませんでした");
        }

        return redirect()->back();
    }

    public function loadSessionForm (Request $request)
    {
        $customerData = $request->session()->get('customer_data');

        return response()->json($customerData);

    }

    public function customerDelete($id)
    {
        $customer = Customer::find($id);
        $customer->delete();

        return redirect()->route('master.index');
    }

    public function dowloadCSV(Request $request)
    {
        $customerResults = $this->customerService->searchQuery($request);

        $fileName = '取引先マスタ一覧.xlsx';
        return Excel::download(new CustomersExport($customerResults), $fileName);
    }

    // ======================= mst Lines ======================== //
    public function lines (Request $request)
    {
        Session::forget('customer_data');

        if ($request->all()) {
            $line_code = $request->input('line_code');
            $department_code = $request->input('department_code');
            // $line_name = $request->input('line_name');
            $delete_flag = $request->input('delete_flag');

            if ($request->department_code != null && $request->department_name == null) {
                $department_model = Department::where('code', $request->department_code)->where('delete_flag', '0')->first();
                $request->merge(['department_name' => $department_model->department_name]);
            } else if ($request->department_code == null)  {
                $request->merge(['department_name' => null]);
            }

            $query = Line::query();

            if ($line_code) {
                $query->where('line_code', $line_code);
            }

            if ($department_code) {
                $query->where('department_code', $department_code);
            }

            if (in_array($delete_flag, ['0', '1'])) {
                if ($delete_flag == 'すべて') {
                    $query->whereIn('delete_flag',  ['0','1']);
                } else {
                    $query->where('delete_flag',  $delete_flag);
                }
            }

            $lines = $query->paginate(20);

            $lines->appends([
                'line_code' => $line_code,
                'department_code' => $department_code,
                'delete_flag' => $delete_flag,
            ]);
        }

        return view('pages.master.lines.line', [
            'lines' => $lines ?? [],
            'line_code' => $request->input('line_code'),
            'department_code' => $request->input('department_code'),
            'delete_flag' => $request->input('delete_flag'),
        ]);
    }

    public function lineSearch (Request $request)
    {
        return redirect()->route('master.lines.index', [
            'line_code' => $request->input('line_code'),
            'department_code' => $request->input('department_code'),
            'delete_flag' => $request->input('delete_flag')
        ]);
    }

    public function linesCreateOrUpdate ($id = null)
    {
        if (!empty($id)) {
            $line = Line::findOrFail($id);
        }else{
            $line = new Line();
        }

        return view('pages.master.lines.line-edit-or-create', ['line' => $line]);
    }

    public function linesStore (Request $request)
    {
        $request->validate([
            'line_code' => 'required|unique:lines,line_code',
            'line_name' => 'required',
            'line_name_abbreviation' => 'required',
            'department_code' => [
                'required',
                'numeric',
                'exists_in_departments',
            ],
        ], $this->lineService->lineErrorMessage());

        $lines = $this->lineService->createLine($request->all());

        // Store the data in the session
        $request->session()->put('line_data', $request->except('_token'));

        if ($lines) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to create record
            $request->session()->put('error',  "一致するアカウント情報がみつかりませんでした");
        }

        return redirect()->back();
    }

    public function linesUpdate (Request $request, $id)
    {
        $line = Line::find($id);

        $request->validate([
            'line_code' => [
                'required',
                Rule::unique('lines', 'line_code')->ignore($line->id),
            ],
            'line_name' => 'required',
            'line_name_abbreviation' => 'required',
            'department_code' => [
                'required',
                'numeric',
                'exists_in_departments',
            ],
        ], $this->lineService->lineErrorMessage());

        $updated = $this->lineService->updateLine($request, $id);

        if ($updated) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to update record
            $request->session()->put('error', '一致するアカウント情報がみつかりませんでした');
        }

        return redirect()->back();
    }

    public function lineDowloadCSV(Request $request)
    {
        $lineResults = $this->lineService->searchQuery($request);

        $fileName = 'ラインマスタ一覧.xlsx';
        return Excel::download(new LinesExport($lineResults), $fileName);
    }

    public function loadSessionFormLine (Request $request)
    {
        $lineData = $request->session()->get('line_data');

        return response()->json($lineData);
    }

    public function lineDelete($id)
    {
        $line = Line::find($id);
        $line->delete();

        return redirect()->route('master.lines.index');
    }

    // ======================= mst kanban_master ======================== //

    public function kanbans (Request $request)
    {
        Session::forget('kanban_data');

        if ($request->all()) {
            $management_no = $request->input('management_no');
            $part_number = $request->input('part_number');
            $kanban_classification = $request->input('kanban_classification');
            $delete_flag = $request->input('delete_flag');

            if ($request->part_number != null && $request->product_name == null) {
                $product_model = ProductNumber::where('part_number', $request->part_number)->where('delete_flag', '0')->first();
                $request->merge(['product_name' => $product_model->product_name]);
            } else if ($request->part_number == null)  {
                $request->merge(['product_name' => null]);
            }
      
            $query = KanbanMaster::query();

            if ($management_no) {
                $query->where('kanban_masters.management_no', $management_no);
            }

            if ($part_number) {
                $query->where('kanban_masters.part_number', $part_number);
            }

            if ($kanban_classification) {
                $query->where('kanban_masters.kanban_classification', $kanban_classification);
            }

            if (in_array($delete_flag, ['0', '1'])) {
                if ($delete_flag == 'すべて') {
                    $query->whereIn('kanban_masters.delete_flag', ['0', '1']);
                } else {
                    $query->where('kanban_masters.delete_flag', $delete_flag);
                }
            }

            // Join ProductNumber table and select edited_part_number column
            $query->leftJoin('product_numbers', 'kanban_masters.part_number', '=', 'product_numbers.part_number')
                ->addSelect('kanban_masters.*', 'product_numbers.edited_part_number as edited_part_number');

            $kanban = $query->paginate(20);

            // Dynamically add kanban_classification_text based on the kanban_classification value
            $kanban->getCollection()->transform(function ($item) {
                // Assign kanban_classification_text based on KanbanClassification constants
                $item->kanban_classification_text = KanbanClassification::KANBAN_CLASSIFICATION[$item->kanban_classification] ?? 'Unknown';
                return $item;
            });
      
            $kanban->appends([
                'management_no' => $management_no,
                'part_number' => $part_number,
                'kanban_classification' => $kanban_classification,
                'delete_flag' => $delete_flag,
            ]);
        }


        return view('pages.master.kanban.kanban', [
            'kanban' => $kanban ?? [],
            'management_no' => $request->input('management_no'),
            'part_number' => $request->input('part_number'),
            'kanban_classification' => $request->input('kanban_classification'),
            'delete_flag' => $request->input('delete_flag'),
        ]);

        // return view('pages.master.kanban.kanban', ['kanban' => $kanban]);
    }

    public function kanbanSearch (Request $request)
    {
        return redirect()->route('master.kanbans.index', [
            'management_no' => $request->input('management_no'),
            'part_number' => $request->input('part_number'),
            'kanban_classification' => $request->input('kanban_classification'),
            'delete_flag' => $request->input('delete_flag'),
        ]);
    }

    public function kanbanCreateOrUpdate ($id = null)
    {
        if (!empty($id)) {
            $kanban = KanbanMaster::findOrFail($id);
        }else{
            $kanban = new KanbanMaster();
        }

        $productCategory = ProductConstant::CATEGORY;

        return view('pages.master.kanban.kanban-edit-or-create', [
            'kanban' => $kanban,
            'productCategory' => $productCategory,
        ]);
    }

    public function kanbanStore (Request $request)
    {
        $request->validate([
            'management_no' => 'required|unique:kanban_masters,management_no',
            'kanban_classification' => 'required',
            'part_number' => 'required',
            'number_of_accomodated' => 'required|numeric',
        ], $this->kanbanService->kanbanErrorMessage());

        $kanban = $this->kanbanService->createKanban($request->all());

        // Store the data in the session
        $request->session()->put('kanban_data', $request->except('_token'));

        if ($kanban) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to create record
            $request->session()->put('error',  "一致するアカウント情報がみつかりませんでした");
        }

        return redirect()->back();
    }

    public function kanbanUpdate (Request $request, $id)
    {
        $kanban = KanbanMaster::find($id);

        $request->validate([
            'management_no' => [
                'required',
                Rule::unique('kanban_masters', 'management_no')->ignore($kanban->id)
            ],
            'kanban_classification' => 'required',
            'part_number' => 'required',
            'number_of_accomodated' => 'required|numeric',
        ], $this->kanbanService->kanbanErrorMessage());

        $updated = $this->kanbanService->updateKanban($request, $id);

        if ($updated) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to update record
            $request->session()->put('error', '一致するアカウント情報がみつかりませんでした');
        }

        return redirect()->back();
    }

    public function kanbanDowloadCSV (Request $request)
    {
        $kanbanResults = $this->kanbanService->searchQuery($request);

        $fileName = 'かんばんマスタ一覧.xlsx';
        return Excel::download(new KanbanMasterExport($kanbanResults), $fileName);
    }

    public function loadSessionFormKanban (Request $request)
    {
        $kanbanData = $request->session()->get('kanban_data');

        // dd($kanbanData);

        return response()->json($kanbanData);
    }

    public function kanbanDelete($id)
    {
        $kanban = KanbanMaster::find($id);
        $kanban->delete();

        return redirect()->route('master.kanbans.index');
    }

    public function searchProductKanban (Request $request)
    {
        $products = [];
        $query = ProductNumber::query();
        if($request->part_number) {
            $query->where("part_number", "LIKE", "%". $request->part_number . "%");
        }

        if($request->product_name) {
            $query->where("product_name", "LIKE", "%". $request->product_name . "%");
        }

        if($request->line_code) {
            $query->where("line_code", "LIKE", "%". $request->line_code . "%");
        }

        if($request->department_code) {
            $query->where("department_code", "LIKE", "%". $request->department_code . "%");
        }

        if($request->supplier_code) {
            $query->where("supplier_code", "LIKE", "%". $request->supplier_code . "%");
        }

        if($request->product_code) {
            $query->where("product_code", "LIKE", "%". $request->product_code . "%");
        }

        if($request->product_category) {
            $query->where("product_category", "LIKE", "%". $request->product_category . "%");
        }

        if($request->delete_flag) {
            if ($request->delete_flag == 1) {
                $query->where("delete_flag", 1);
            }
        }

        $products = $query->paginate(20);

        $suppliers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '1')->get();

        foreach ($products as $key => $product) {
            foreach ($suppliers as $key => $supplier) {
                if ($supplier->id == $product->supplier_code) {
                    $product->supplier = $supplier->name;
                }
            }

        }

        $response = [
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'prev_page_url' => $products->previousPageUrl(),
            'next_page_url' => $products->nextPageUrl(),
        ];

        return response()->json($response);
    }

    // ======================= mst departments ======================== //

    public function departments (Request $request)
    {
        Session::forget('department_data');

        if ($request->all()) {
            $code = $request->input('code');
            $name = $request->input('name');
            $name_abbreviation = $request->input('name_abbreviation');
            $department_name = $request->input('department_name');
            $section_name = $request->input('section_name');
            $group_name = $request->input('group_name');
            $delete_flag = $request->input('delete_flag');

            $query = Department::query();

            if ($code) {
                $query->where('code', $code);
            }

            if ($name) {
                $query->where('name', $name);
            }

            if ($name_abbreviation) {
                $query->where('name_abbreviation', $name_abbreviation);
            }

            if ($department_name) {
                $query->where('department_name', $department_name);
            }

            if ($section_name) {
                $query->where('section_name', $section_name);
            }

            if ($group_name) {
                $query->where('group_name', $group_name);
            }

            if (in_array($delete_flag, ['0', '1'])) {
                if ($delete_flag == 'すべて') {
                    $query->whereIn('delete_flag',  ['0','1']);
                } else {
                    $query->where('delete_flag',  $delete_flag);
                }
            }

            $departments = $query->paginate(20);

            $departments->appends([
                'code' => $code,
                'name' => $name,
                'name_abbreviation' => $name_abbreviation,
                'department_name' => $department_name,
                'section_name' => $section_name,
                'group_name' => $group_name,
                'delete_flag' => $delete_flag
            ]);
        }

        return view('pages.master.departments.departments', [
            'departments' => $departments ?? [],
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'name_abbreviation' => $request->input('name_abbreviation'),
            'department_name' => $request->input('department_name'),
            'section_name' => $request->input('section_name'),
            'group_name' => $request->input('group_name'),
            'delete_flag' => $request->input('delete_flag')
        ]);

    }

    public function departmentSearch (Request $request)
    {
        return redirect()->route('master.departments.index', [
            'code' => $request->input('code'),
            'name' => $request->input('name'),
            'name_abbreviation' => $request->input('name_abbreviation'),
            'department_name' => $request->input('department_name'),
            'section_name' => $request->input('section_name'),
            'group_name' => $request->input('group_name'),
            'delete_flag' => $request->input('delete_flag')
        ]);
    }

    public function departmentCreateOrUpdate ($id = null)
    {
        if (!empty($id)) {
            $deparment = Department::findOrFail($id);
        }else{
            $deparment = new Department();
        }

        return view('pages.master.departments.department-edit-or-create', ['department' => $deparment]);
    }

    public function departmentStore (Request $request)
    {
        $request->validate([
            'code' => 'required|max:6',
            'name' => 'required|max:60',
            'name_abbreviation' => 'required|max:40',
            'department_name' => 'max:40',
            'section_name' => 'max:40',
            'group_name' => 'max:40',
        ], $this->departmentService->departmentErrorMessage());

        $department = $this->departmentService->createDepartment($request->all());

        // Store the data in the session
        $request->session()->put('department_data', $request->except('_token'));

        if ($department) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to create record
            $request->session()->put('error',  "一致するアカウント情報がみつかりませんでした");
        }

        return redirect()->back();
    }

    public function departmentUpdate (Request $request, $id)
    {
        $department = Department::find($id);

        $request->validate([
            'code' => [
                'required',
                Rule::unique('departments', 'code')->ignore($department->id)
            ],
            'name' => 'required|max:60',
            'name_abbreviation' => 'required|max:40',
            'department_name' => 'max:40',
            'section_name' => 'max:40',
            'group_name' => 'max:40',
        ], $this->departmentService->departmentErrorMessage());

        $updated = $this->departmentService->updateDepartment($request, $id);

        if ($updated) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to update record
            $request->session()->put('error', '一致するアカウント情報がみつかりませんでした');
        }

        return redirect()->back();
    }

    public function departmentDowloadCSV (Request $request)
    {
        $departmentResults = $this->departmentService->searchQuery($request);

        $fileName = '部門マスタ一覧.xlsx';
        return Excel::download(new DepartmentsExport($departmentResults), $fileName);
    }

    public function loadSessionFormDepartment (Request $request)
    {
        $departmentData = $request->session()->get('department_data');

        return response()->json($departmentData);
    }

    public function departmentDelete($id)
    {
        $department = Department::find($id);
        $department->delete();

        return redirect()->route('master.departments.index');
    }

    // ======================= mst processes ======================= //
    public function processes (Request $request)
    {
        Session::forget('process_data');

        $process_code = $request->input('process_code');
        $process_name = $request->input('process_name');
        $inside_and_outside_division = $request->input('inside_and_outside_division');
        $delete_flag = $request->input('delete_flag');


        if ($request->all()) {
            // $query = Process::query();
            $query = Process::query()
                            ->when($request->process_code, function($query) use ($request) {
                                $query->where("process_code", "LIKE", "%". $request->process_code . "%");
                            })
                            ->when($request->process_name, function($query) use ($request) {
                                $query->where("process_name", "LIKE", "%". $request->process_name . "%");
                            })
                            ->when($request->inside_and_outside_division, function($query) use ($request) {
                                $query->where("inside_and_outside_division", "=", $request->inside_and_outside_division);
                            })
                            ->when($request->delete_flag, function($query) use ($request) {
                                $query->where("delete_flag", "=", $request->delete_flag);
                            });

            $processes = $query->orderBy('created_at', 'desc')->paginate(20);

            $processes->appends([
                'process_code' => $process_code,
                'process_name' => $process_name,
                'inside_and_outside_division' => $inside_and_outside_division,
                'delete_flag' => $delete_flag
            ]);
        }

        return view('pages.master.processes.processes', [
            'processes' => $processes ?? [],
            'process_code' => $request->input('process_code'),
            'process_name' => $request->input('process_name'),
            'inside_and_outside_division' => $request->input('inside_and_outside_division'),
            'delete_flag' => $request->input('delete_flag')
        ]);
    }

    public function processCreateOrUpdate ($id = null)
    {
        if (!empty($id)) {
            $process = Process::findOrFail($id);
        }else{
            $process = new Process();
        }

        return view('pages.master.processes.processes-edit-or-create', ['process' => $process]);
    }

    public function processSearch (Request $request)
    {
        return redirect()->route('master.processes.index', [
            'process_code' => $request->input('process_code'),
            'process_name' => $request->input('process_name'),
            'inside_and_outside_division' => $request->input('inside_and_outside_division'),
            'delete_flag' => $request->input('delete_flag')
        ]);
    }

    public function processStore (Request $request)
    {
        $request->validate([
            'process_code' => 'required',
            'process_name' => 'required',
            'abbreviation_process_name' => 'required',
            'inside_and_outside_division' => 'required',
        ], $this->processService->processErrorMessage());

        $process = $this->processService->createProcess($request->all());

        // Store the data in the session
        $request->session()->put('process_data', $request->except('_token'));

        if ($process) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to create record
            $request->session()->put('error',  "一致するアカウント情報がみつかりませんでした");
        }

        return redirect()->back();
    }

    public function processUpdate (Request $request, $id)
    {
        $process = Process::find($id);

        $request->validate([
            'process_code' => [
                'required',
                Rule::unique('processes', 'process_code')->ignore($process->id)
            ],
            'process_name' => 'required',
            'abbreviation_process_name' => 'required',
            'inside_and_outside_division' => 'required',
        ], $this->processService->processErrorMessage());

        $updated = $this->processService->updateProcess($request, $id);

        if ($updated) {
            // Record created successfully
            $request->session()->put('success', 'データは正常に登録されました');
        } else {
            // Failed to update record
            $request->session()->put('error', '一致するアカウント情報がみつかりませんでした');
        }

        return redirect()->back();
    }

    public function processDowloadCSV (Request $request)
    {
        $processResults = $this->processService->searchQuery($request);

        $fileName = '工程マスタ一覧.xlsx';
        return Excel::download(new ProcessesExport($processResults), $fileName);
    }

    public function loadSessionFormProcess (Request $request)
    {
        $processData = $request->session()->get('process_data');

        return response()->json($processData);
    }

    public function processDelete($id)
    {
        $process = Process::find($id);
        $process->delete();

        return redirect()->route('master.processes.index');
    }

    // mst_configuration
    public function configurationCreate (Request $request)
    {
        $request->validate([
            'parent_part_number' => 'required',
            'child_part_number' => 'required',
            'number_used' => 'required',
        ]);

        $data = $request->all();

        $child_product_name = $data['child_product_name'] ?? '';

        unset($data['parent_product_name']);
        unset($data['product_name_selected']);
        unset($data['child_product_name']);
        unset($data['config_id']);
        unset($data['orig_child_part_number']);

        $conf = $this->configurationService->createConfiguration($data);

        $conf->child_product_name = $child_product_name;

        return response()->json($conf);

    }

    public function configurationEdit (Request $request)
    {
        $request->validate([
            'parent_part_number' => 'required',
            'child_part_number' => 'required',
            'number_used' => 'required',
            'material_classification' => 'numeric',
        ]);

        $data = $request->all();
        $origConfiguration_id = $data['config_id'];
        $origChildPartNumber = $data['orig_child_part_number'];

        unset($data['_token']);
        unset($data['product_name_selected']);
        unset($data['child_product_name']);
        unset($data['orig_child_part_number']);
        unset($data['config_id']);


        $conf = [];

        // create new and soft delete old if replace
        if ($origChildPartNumber != $data['child_part_number']) {
            // dd('here');
            $conf = $this->configurationService->createConfiguration($data);
            if ($origChildPartNumber != null || $origChildPartNumber != '') {
                // dd('soft');
                $this->configurationService->softDelete($origConfiguration_id);
            }
        } else {
            // dd('asdasd');
            $conf = $this->configurationService->editConfiguration($data, $origConfiguration_id);
        }


        $configurations = Configuration::where('parent_part_number', $data['parent_part_number'])->where('delete_flag', '!=', 1)->get();

        foreach ($configurations as $confi) {
            $prod_name = ProductNumber::where('part_number', $confi->child_part_number)->value('product_name');
            $confi->child_product_name = $prod_name;
        }

        return response()->json($configurations);
    }

    public function configurationSoftDelete (Request $request)
    {
        $data = $request->all();

        $this->configurationService->softDelete($data['config_id']);

        $configurations = Configuration::where('parent_part_number', $data['parent_part_number'])->where('delete_flag', '!=', 1)->get();

        foreach ($configurations as $confi) {
            $prod_name = ProductNumber::where('part_number', $confi->child_part_number)->value('product_name');
            $confi->child_product_name = $prod_name;
        }

        return response()->json($configurations);
    }
}