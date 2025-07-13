<?php

namespace App\Http\Controllers;

use App\Exports\ListCustomerExport;
use App\Exports\ListDepartmentExport;
use App\Exports\ListLineExport;
use App\Exports\PurchaseBreakdownExport;
use App\Exports\PurchaseDataExport;
use App\Models\Code;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Item;
use App\Models\Line;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CostPriceController extends Controller
{
    public function index(Request $request)
    {   
        // Initialize totals for different cost categories
        $totals = [
            'totalPurchasedMaterialCost' => 0,
            'totalEquipmentProcessingCost' => 0,
            'totalOutsourcingCost' => 0,
            'totalEquipmentOutsourcingCost' => 0,
            'totalOutsourcedDesignCost' => 0,
            'totalOutsourcingConstructionCost' => 0,
            'totalCuttingToolsCost' => 0,
            'totalEntertainmentFee' => 0,
            'totalRepairCost' => 0,
            'totalSuppliesExpense' => 0,
            'totalOfficeSupplies' => 0,
            'totalConferenceCost' => 0,
            'totalMiscExpenses' => 0,
            'totalCosts' => 0,
        ];

        $lists = collect();
        $count = 0;

        if ($request->all()) {
            // Extract year and month from the input
            $yearMonth = $request->input('year_month');
            $year = substr($yearMonth, 0, 4);
            $month = substr($yearMonth, 4, 2);

            // Construct the base query with necessary joins and select statements
            $query = Purchase::query()
                ->with(['department:id,code,name', 'line:id,line_code,line_name', 'customer:id,customer_code,customer_name'])
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->selectRaw('
                    department_code,
                    line_code,
                    customer_code,
                    SUM(CASE WHEN expense_item = 011 THEN amount_of_money ELSE 0 END) as purchased_material_cost,
                    SUM(CASE WHEN expense_item = 012 THEN amount_of_money ELSE 0 END) as equipment_processing_cost,
                    SUM(CASE WHEN expense_item = 021 THEN amount_of_money ELSE 0 END) as outsourcing_cost,
                    SUM(CASE WHEN expense_item = 022 THEN amount_of_money ELSE 0 END) as equipment_outsourcing_cost,
                    SUM(CASE WHEN expense_item = 023 THEN amount_of_money ELSE 0 END) as outsourced_design_cost,
                    SUM(CASE WHEN expense_item = 024 THEN amount_of_money ELSE 0 END) as outsourcing_construction_cost,
                    SUM(CASE WHEN expense_item = 031 THEN amount_of_money ELSE 0 END) as cutting_tools_cost,
                    SUM(CASE WHEN expense_item = 331 THEN amount_of_money ELSE 0 END) as entertainment_fee,
                    SUM(CASE WHEN expense_item = 341 THEN amount_of_money ELSE 0 END) as repair_cost,
                    SUM(CASE WHEN expense_item = 361 THEN amount_of_money ELSE 0 END) as supplies_expense,
                    SUM(CASE WHEN expense_item = 371 THEN amount_of_money ELSE 0 END) as office_supplies,
                    SUM(CASE WHEN expense_item = 381 THEN amount_of_money ELSE 0 END) as conference_cost,
                    SUM(CASE WHEN expense_item = 391 THEN amount_of_money ELSE 0 END) as misc_expenses
                ');

            // Apply department code filter if present
            if ($request->filled('department_code')) {
                $query->where('department_code', $request->department_code);
            }

            // Apply breakdown groupings and order by logic
            switch ($request->input('breakdown')) {
                case '課別':
                    $query->selectRaw("CONCAT(SUBSTRING(department_code, 1, 4), '00') AS grouped_department_code")
                        ->groupBy('grouped_department_code')
                        ->orderByRaw("grouped_department_code");
                    break;
                case '組別':
                    $query->groupBy('department_code')
                        ->orderBy("department_code");
                    break;
                case 'ライン別':
                    $query->groupBy('line_code')
                        ->orderByRaw("line_code IS NULL, line_code");
                    break;
                case '得意先別':
                    $query->groupBy('customer_code')
                        ->orderByRaw("customer_code IS NULL, customer_code");
                    break;
                default:
                    $query->groupBy('department_code')
                        ->orderByRaw("department_code IS NULL, department_code");
                    break;
            }            

            // Create a unique cache key based on request parameters to cache the results forever
            $cacheKey = "purchases_{$yearMonth}_{$request->breakdown}_" . md5(json_encode($request->all()));

            // Cache data indefinitely until an update or insert occurs
            $lists = Cache::rememberForever($cacheKey, function () use ($query, $request) {
                return $query->simplePaginate(20)->appends($request->except('page'));
            });

            // Calculate the totals for each category from the fetched data
            foreach ($lists as $item) {
                $totals['totalPurchasedMaterialCost'] += $item->purchased_material_cost;
                $totals['totalEquipmentProcessingCost'] += $item->equipment_processing_cost;
                $totals['totalOutsourcingCost'] += $item->outsourcing_cost;
                $totals['totalEquipmentOutsourcingCost'] += $item->equipment_outsourcing_cost;
                $totals['totalOutsourcedDesignCost'] += $item->outsourced_design_cost;
                $totals['totalOutsourcingConstructionCost'] += $item->outsourcing_construction_cost;
                $totals['totalCuttingToolsCost'] += $item->cutting_tools_cost;
                $totals['totalEntertainmentFee'] += $item->entertainment_fee;
                $totals['totalRepairCost'] += $item->repair_cost;
                $totals['totalSuppliesExpense'] += $item->supplies_expense;
                $totals['totalOfficeSupplies'] += $item->office_supplies;
                $totals['totalConferenceCost'] += $item->conference_cost;
                $totals['totalMiscExpenses'] += $item->misc_expenses;
            }
        }

        // Return the results to the view
        return view('pages.cost.list', [
            'lists' => $lists,
            'totals' => $totals,
        ]);
    }

    public function listSearch(Request $request)
    {
        return redirect()->route('cost.index', [
            'year_month' => $request->year_month,
            'breakdown' => $request->breakdown,
            'department_code' => $request->department_code,
            'department_name' => $request->department_name
        ]);
    }

    public function listExportCSV(Request $request)
    {
        if ($request->all()) {
            $yearMonth = $request->input('year_month');
            $year = substr($yearMonth, 0, 4);
            $month = substr($yearMonth, 4, 2);

            // Base query with sum aggregations and conditions
            $query = Purchase::selectRaw('
                    department_code,
                    line_code,
                    customer_code,
                    SUM(COALESCE(CASE WHEN expense_item = 011 THEN amount_of_money ELSE 0 END, 0)) as purchased_material_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 012 THEN amount_of_money ELSE 0 END, 0)) as equipment_processing_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 021 THEN amount_of_money ELSE 0 END, 0)) as outsourcing_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 022 THEN amount_of_money ELSE 0 END, 0)) as equipment_outsourcing_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 023 THEN amount_of_money ELSE 0 END, 0)) as outsourced_design_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 024 THEN amount_of_money ELSE 0 END, 0)) as outsourcing_construction_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 031 THEN amount_of_money ELSE 0 END, 0)) as cutting_tools_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 331 THEN amount_of_money ELSE 0 END, 0)) as entertainment_fee,
                    SUM(COALESCE(CASE WHEN expense_item = 341 THEN amount_of_money ELSE 0 END, 0)) as repair_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 361 THEN amount_of_money ELSE 0 END, 0)) as supplies_expense,
                    SUM(COALESCE(CASE WHEN expense_item = 371 THEN amount_of_money ELSE 0 END, 0)) as office_supplies,
                    SUM(COALESCE(CASE WHEN expense_item = 381 THEN amount_of_money ELSE 0 END, 0)) as conference_cost,
                    SUM(COALESCE(CASE WHEN expense_item = 391 THEN amount_of_money ELSE 0 END, 0)) as misc_expenses
                ')
                ->whereYear('date', $year)
                ->whereMonth('date', $month);

            // Apply grouping and ordering based on the breakdown type
            switch ($request->input('breakdown')) {
                case '課別':
                    $query->selectRaw("CONCAT(LEFT(department_code, 4), '00') AS grouped_department_code")
                        ->groupBy('grouped_department_code')
                        ->orderByRaw("grouped_department_code IS NULL, grouped_department_code");
                    break;
                case '組別':
                    $query->groupBy('department_code')
                        ->orderByRaw("department_code IS NULL, department_code");
                    break;
                case 'ライン別':
                    $query->groupBy('line_code')
                        ->orderByRaw("line_code IS NULL, line_code");
                    break;
                case '得意先別':
                    $query->groupBy('customer_code')
                        ->orderByRaw("customer_code IS NULL, customer_code");
                    break;
                default:
                    $query->groupBy('department_code')
                        ->orderByRaw("department_code IS NULL, department_code");
                    break;
            }

            // Fetch the query results
            $lists = $query->get();

            // Map departments, customers, and lines
            $departments = Department::selectRaw("code as id, name as name")->get();
            $customers = Customer::selectRaw("customer_code as id, customer_name as name")->get();
            $lines = Line::selectRaw("line_code as id, line_name as name")->get();

            // Prepare mappings for lookup
            $departmentMap = $departments->keyBy('id');
            $customerMap = $customers->keyBy('id');
            $lineMap = $lines->keyBy('id');

            $data = [];
            foreach ($lists as $list) {
                $list->customer = $customerMap[$list->customer_code]->name ?? '';
                $list->department = $departmentMap[$list->department_code]->name ?? '';
                $list->line = $lineMap[$list->line_code]->name ?? '';
                $list->total = $list->purchased_material_cost +
                    $list->equipment_processing_cost +
                    $list->outsourcing_cost +
                    $list->equipment_outsourcing_cost +
                    $list->outsourced_design_cost +
                    $list->outsourcing_construction_cost +
                    $list->cutting_tools_cost +
                    $list->entertainment_fee +
                    $list->repair_cost +
                    $list->supplies_expense +
                    $list->office_supplies +
                    $list->conference_cost +
                    $list->misc_expenses;

                // Build data for export based on breakdown type
                if ($request->breakdown == '課別' || $request->breakdown == '組別') {
                    $data[] = [
                        $list->grouped_department_code ?? $list->department_code ?? '',
                        $list->department,
                        $list->purchased_material_cost ?? 0,
                        $list->equipment_processing_cost ?? 0,
                        $list->outsourcing_cost ?? 0,
                        $list->equipment_outsourcing_cost ?? 0,
                        $list->outsourced_design_cost ?? 0,
                        $list->outsourcing_construction_cost ?? 0,
                        $list->cutting_tools_cost ?? 0,
                        $list->entertainment_fee ?? 0,
                        $list->repair_cost ?? 0,
                        $list->supplies_expense ?? 0,
                        $list->office_supplies ?? 0,
                        $list->conference_cost ?? 0,
                        $list->misc_expenses ?? 0,
                        $list->total ?? 0
                    ];
                } else if ($request->breakdown == 'ライン別') {
                    $data[] = [
                        $list->department_code ?? '',
                        $list->department,
                        $list->line_code ?? '',
                        $list->line,
                        $list->purchased_material_cost ?? 0,
                        $list->equipment_processing_cost ?? 0,
                        $list->outsourcing_cost ?? 0,
                        $list->equipment_outsourcing_cost ?? 0,
                        $list->outsourced_design_cost ?? 0,
                        $list->outsourcing_construction_cost ?? 0,
                        $list->cutting_tools_cost ?? 0,
                        $list->entertainment_fee ?? 0,
                        $list->repair_cost ?? 0,
                        $list->supplies_expense ?? 0,
                        $list->office_supplies ?? 0,
                        $list->conference_cost ?? 0,
                        $list->misc_expenses ?? 0,
                        $list->total ?? 0
                    ];
                } else {
                    $data[] = [
                        $list->customer_code ?? '',
                        $list->customer,
                        $list->purchased_material_cost ?? 0,
                        $list->equipment_processing_cost ?? 0,
                        $list->outsourcing_cost ?? 0,
                        $list->equipment_outsourcing_cost ?? 0,
                        $list->outsourced_design_cost ?? 0,
                        $list->outsourcing_construction_cost ?? 0,
                        $list->cutting_tools_cost ?? 0,
                        $list->entertainment_fee ?? 0,
                        $list->repair_cost ?? 0,
                        $list->supplies_expense ?? 0,
                        $list->office_supplies ?? 0,
                        $list->conference_cost ?? 0,
                        $list->misc_expenses ?? 0,
                        $list->total ?? 0
                    ];
                }
            }
            
            $fileName = '原価表.xlsx';
            if ($request->breakdown == '課別' || $request->breakdown == '組別') {
                $export = new ListDepartmentExport(collect($data));
            } else if ($request->breakdown == 'ライン別') {
                $export = new ListLineExport(collect($data));
            } else {
                $export = new ListCustomerExport(collect($data));
            }
            return Excel::download($export, $fileName);
        }
    }

    public function purchaseBreakdown(Request $request)
    {
        $breakdownDatas = collect();
        $count = 0;
        $currentSum = 0;

        if ($request->filled('year_month')) {
            $yearMonth = $request->input('year_month');
            $year = substr($yearMonth, 0, 4);
            $month = substr($yearMonth, 4, 2);

            // Build the base query with scopes and eager loading to avoid N+1 issues
            $query = Purchase::with(['item'])
                ->filterByYearMonth($year, $month)
                ->withAggregatedAmounts();

            $count = $query->count();
            $breakdownDatas = $query->paginate(20);

            $currentSum = $breakdownDatas->sum('amount');

            $breakdownDatas->appends(['year_month' => $yearMonth]);
        }

        return view('pages.cost.purchase-breakdown', [
            'breakdown_datas' => $breakdownDatas,
            'count' => $count,
            'sum' => $currentSum,
        ]);
    }

    public function purchaseBreakdownSearch(Request $request)
    {
        return redirect()->route('cost.purchaseBreakdown', [
            'year_month' => $request->year_month,
        ]);
    }

    public function purchaseBreakdownExportCSV(Request $request)
    {
        if ($request->all()) {
            $yearMonth = $request->input('year_month');
            $year = substr($yearMonth, 0, 4);
            $month = substr($yearMonth, 4, 2);
            $breakdown_datas = Purchase::selectRaw('
                    mst_purchases.expense_item,
                    mst_items.item_name,
                    mst_items.acount,
                    mst_items.acount_name,
                    mst_items.supplementary_subjects,
                    mst_items.auxiliary_course_name,
                    SUM(COALESCE(mst_purchases.amount_of_money, 0)) as amount
                ')
                ->leftJoin('items', 'items.expense_item', '=', 'purchases.expense_item')
                ->whereYear('purchases.date', $year)
                ->whereMonth('purchases.date', $month)
                ->groupBy('purchases.expense_item')
                ->get();

            $fileName = '費目別・仕入内訳.xlsx';
            return Excel::download(new PurchaseBreakdownExport($breakdown_datas), $fileName);
        }
    }

    public function purchaseData(Request $request)
    {
        $data = [];
        $count = 0;
        if ($request->all()) {
            if ($request->line_code != null && $request->line_name == null) {
                $line_model = Line::where('line_code', $request->line_code)->where('delete_flag', '0')->first();
                $request->merge(['line_name' => $line_model->line_name]);
            } else if ($request->line_code == null) {
                $request->merge(['line_name' => null]);
            }
            if ($request->department_code != null && $request->department_name == null) {
                $department_model = Department::where('code', $request->department_code)->where('delete_flag', '0')->first();
                $request->merge(['department_name' => $department_model->name]);
            } else if ($request->department_code == null)  {
                $request->merge(['department_name' => null]);
            }
            if ($request->customer_code != null && $request->customer_name == null) {
                $customer_model = Customer::where('customer_code', $request->customer_code)->where('supplier_tag', '0')->where('delete_flag', '0')->first();
                $request->merge(['customer_name' => $customer_model->customer_name]);
            } else if ($request->customer_code == null)  {
                $request->merge(['customer_name' => null]);
            }
            if ($request->supplier_code != null && $request->supplier_name == null) {
                $supplier_model = Customer::where('customer_code', $request->supplier_code)->where('supplier_tag', '1')->where('delete_flag', '0')->first();
                $request->merge(['supplier_name' => $supplier_model->customer_name]);
            } else if ($request->supplier_code == null)  {
                $request->merge(['supplier_name' => null]);
            }
            if ($request->expense_item != null && $request->item_name == null) {
                $item_model = Item::where('expense_item', $request->expense_item)->where('delete_flag', '0')->first();
                $request->merge(['item_name' => $item_model->name]);
            } else if ($request->expense_item == null)  {
                $request->merge(['item_name' => null]);
            }
            $query = Purchase::query()
                            ->when($request->year_month, function($query) use ($request) {
                                $yearMonth = $request->input('year_month');
                                $year = substr($yearMonth, 0, 4);
                                $month = substr($yearMonth, 4, 2);
                                $query->whereYear('date', $year)
                                        ->whereMonth('date', $month);
                            })
                            ->when($request->expense_item, function($query) use ($request) {
                                $query->where("expense_item", "LIKE", "%". $request->expense_item . "%");
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
                            ->when($request->customer_code, function($query) use ($request) {
                                $query->where("customer_code", "LIKE", "%". $request->customer_code . "%");
                            });

            $count = (clone $query)->count();

            $data = $query
                            ->paginate(20);
            $departments = Department::selectRaw("code as id, name as name")->get();
            $customers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '0')->get();
            $suppliers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '1')->get();
            $lines = Line::selectRaw("line_code as id, line_name as name")->get();
            $items = Item::selectRaw("expense_item as id, item_name as name")->get();
            $codes = Code::selectRaw("code as id, name as name")->where('division', '単位')->get();

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

            $itemMap = [];
            foreach ($items as $item) {
                $itemMap[$item->id] = $item;
            }

            $codeMap = [];
            foreach ($codes as $code) {
                $codeMap[$code->id] = $code;
            }

            foreach ($data as $key => $purchase_data) {
                $purchase_data->customer = '';
                $purchase_data->department = '';
                $purchase_data->supplier = '';
                $purchase_data->line = '';
                $purchase_data->item = '';
                $purchase_data->unit = '';

                if (isset($departmentMap[$purchase_data->department_code])) {
                    $purchase_data->department = $departmentMap[$purchase_data->department_code]->name;
                }
                if (isset($customerMap[$purchase_data->customer_code])) {
                    $purchase_data->customer = $customerMap[$purchase_data->customer_code]->name;
                }
                if (isset($supplierMap[$purchase_data->supplier_code])) {
                    $purchase_data->supplier = $supplierMap[$purchase_data->supplier_code]->name;
                }
                if (isset($lineMap[$purchase_data->line_code])) {
                    $purchase_data->line = $lineMap[$purchase_data->line_code]->name;
                }
                if (isset($itemMap[$purchase_data->expense_item])) {
                    $purchase_data->item = $itemMap[$purchase_data->expense_item]->name;
                }
                if (isset($codeMap[$purchase_data->unit_code])) {
                    $purchase_data->unit = $codeMap[$purchase_data->unit_code]->name;
                }
                $purchase_data->date = date('Y/m/d', strtotime($purchase_data->date));
            }
            $parameters = [
                'year_month' => $request->year_month,
                'line_code' => $request->line_code,
                'line_name' => $request->line_name,
                'department_code' => $request->department_code,
                'department_name' => $request->department_name,
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $request->supplier_name,
                'customer_code' => $request->customer_code,
                'customer_name' => $request->customer_name,
                'expense_item' => $request->expense_item,
                'item_name' => $request->item_name,
            ];
            // Filter out empty or null values
            $filteredParameters = array_filter($parameters, function ($value) {
                return $value !== null && $value !== '';
            });
            $data->appends($filteredParameters);
        }

        return view('pages.cost.purchase-data', [
            'data' => $data,
            'count' => $count,
        ]);
    }

    public function purchaseDataSearch(Request $request)
    {
        return redirect()->route('cost.purchaseData', [
            'year_month' => $request->year_month,
            'line_code' => $request->line_code,
            'line_name' => $request->line_name,
            'department_code' => $request->department_code,
            'department_name' => $request->department_name,
            'supplier_code' => $request->supplier_code,
            'supplier_name' => $request->supplier_name,
            'customer_code' => $request->customer_code,
            'customer_name' => $request->customer_name,
            'expense_item' => $request->expense_item,
            'item_name' => $request->item_name,
        ]);
    }

    public function purchaseDataExportCSV(Request $request)
    {
        if ($request->all()) {
            $query = Purchase::query()
                            ->when($request->year_month, function($query) use ($request) {
                                $yearMonth = $request->input('year_month');
                                $year = substr($yearMonth, 0, 4);
                                $month = substr($yearMonth, 4, 2);
                                $query->whereYear('date', $year)
                                        ->whereMonth('date', $month);
                            })
                            ->when($request->expense_item, function($query) use ($request) {
                                $query->where("expense_item", "LIKE", "%". $request->expense_item . "%");
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
                            ->when($request->customer_code, function($query) use ($request) {
                                $query->where("customer_code", "LIKE", "%". $request->customer_code . "%");
                            })
                            ->selectRaw(
                                '
                                    department_code,
                                    line_code,
                                    customer_code,
                                    supplier_code,
                                    part_number,
                                    product_name,
                                    quantity,
                                    unit_code,
                                    unit_price,
                                    amount_of_money as amount,
                                    slip_number as slip_no,
                                    date
                                '
                            )->get();
            $departments = Department::selectRaw("code as id, name as name")->get();
            $customers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '0')->get();
            $suppliers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '1')->get();
            $lines = Line::selectRaw("line_code as id, line_name as name")->get();
            $items = Item::selectRaw("expense_item as id, item_name as name")->get();

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

            $itemMap = [];
            foreach ($items as $item) {
                $itemMap[$item->id] = $item;
            }
            $data = [];
            foreach ($query as $key => $purchase_data) {
                $purchase_data->customer = '';
                $purchase_data->department = '';
                $purchase_data->supplier = '';
                $purchase_data->line = '';
                $purchase_data->item = '';

                if (isset($departmentMap[$purchase_data->department_code])) {
                    $purchase_data->department = $departmentMap[$purchase_data->department_code]->name;
                }
                if (isset($customerMap[$purchase_data->customer_code])) {
                    $purchase_data->customer = $customerMap[$purchase_data->customer_code]->name;
                }
                if (isset($supplierMap[$purchase_data->supplier_code])) {
                    $purchase_data->supplier = $supplierMap[$purchase_data->supplier_code]->name;
                }
                if (isset($lineMap[$purchase_data->line_code])) {
                    $purchase_data->line = $lineMap[$purchase_data->line_code]->name;
                }
                if (isset($itemMap[$purchase_data->expense_item])) {
                    $purchase_data->item = $itemMap[$purchase_data->expense_item]->name;
                }
                $purchase_data->date = date('Y/m/d', strtotime($purchase_data->date));

                $data[] = [
                    $purchase_data->department_code ?? '',
                    $purchase_data->department,
                    $purchase_data->line_code ?? '',
                    $purchase_data->line,
                    $purchase_data->part_number ?? '',
                    $purchase_data->product_name ?? '',
                    $purchase_data->quantity ?? 0,
                    $purchase_data->unit_code ?? '',
                    $purchase_data->unit_price ?? 0,
                    $purchase_data->amount ?? 0,
                    $purchase_data->slip_no ?? '',
                    $purchase_data->supplier,
                    $purchase_data->customer,
                    $purchase_data->date ?? '',
                ];
            }
            $fileName = '費目別・仕入データリスト.xlsx';
            return Excel::download(new PurchaseDataExport(collect($data)), $fileName);
        }
    }
}
