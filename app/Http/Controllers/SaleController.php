<?php

namespace App\Http\Controllers;

use App\Exports\SalePlanExport;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Line;
use App\Models\ProductNumber;
use App\Models\SalesActual;
use App\Services\SalesService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct(
        protected SalesService $salesService,
    ) { }

    public function salePlanSearch(Request $request)
    {
        $data = $request->all();
        $data = $this->salesService->salesPlan($data);
        return view('pages.sales.sale_plan_search', $data);
    }

    public function salePerformanceSearch(Request $request)
    {
        $datas = [];
        if (count($request->query()) > 0) {
            if (($request->type ?? 1) == 1) {
                // $datas = Customer::where('delete_flag', '0')->whereHas('saleActuals', function ($query) {
                //     $query->select('id');
                //     $query->limit(1);
                // })->paginate(20);
                $datas = DB::table(getTableName(Customer::class))
                    ->join(getTableName(SalesActual::class), function ($join) {
                        $join->on('customers.customer_code', '=', 'sales_actuals.customer_code');
                        $join->on('sales_actuals.year_month', DB::raw(request()->year_month));
                    })
                    ->select('customers.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                    ->where('customers.delete_flag', 0)
                    ->groupBy('customers.customer_code')
                    ->orderBy('customers.customer_code', 'ASC')
                    ->paginate(20);
                $sums = DB::table(getTableName(SalesActual::class))
                    ->leftJoin(getTableName(Customer::class), function ($join) {
                        $join->on('customers.customer_code', '=', 'sales_actuals.customer_code');
                    })
                    ->select(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                    ->where('customers.delete_flag', 0)->
                    first();
                $sum_a = $sums->price_a;
                $sum_b = $sums->price_b;
                $sum_c = $sums->price_c;
                $sum_e = $sums->price_e;
                $count = DB::table(getTableName(SalesActual::class))
                    ->leftJoin(getTableName(Customer::class), function ($join) {
                        $join->on('customers.customer_code', '=', 'sales_actuals.customer_code');
                    })
                    ->where('customers.delete_flag', 0)
                    ->where('year_month', request()->year_month)
                    ->count();
            } elseif (($request->type ?? 1) == 2 || ($request->type ?? 1) == 3) {
                if (($request->type ?? 1) == 2) {
                    $datas = DB::table(getTableName(Department::class))
                        ->join(getTableName(SalesActual::class), function ($join) {
                            $join->on('sales_actuals.year_month', DB::raw(request()->year_month));
                            $join->on(DB::raw('substr(mst_sales_actuals.department_code,1,4)'), '=', DB::raw('substr(mst_departments.code,1,4)'));
                        })
                        ->select('departments.*')
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                        ->where('departments.delete_flag', '0')
                        ->groupBy(DB::raw('substr(mst_departments.code,1,4)'))
                        ->orderBy('departments.code', 'ASC')
                        ->paginate(20);
                    $sums = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Department::class), function ($join) {
                            $join->on('departments.code', '=', 'sales_actuals.department_code');
                        })
                        ->select(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                        ->where('departments.delete_flag', '0')
                        ->first();
                    $sum_a = $sums->price_a;
                    $sum_b = $sums->price_b;
                    $sum_c = $sums->price_c;
                    $sum_e = $sums->price_e;
                    $count = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Department::class), function ($join) {
                            $join->on('departments.code', '=', 'sales_actuals.department_code');
                            $join->where('departments.delete_flag', '0');
                        })
                        ->where('departments.delete_flag', '0')
                        ->where('year_month', request()->year_month)
                        ->count();
                } else {
                    // $datas = Department::whereHas('salePlans', function())->paginate(20);
                    $datas = DB::table(getTableName(Department::class))
                    ->join(getTableName(SalesActual::class), function ($join) {
                        $join->on('departments.code', '=', 'sales_actuals.department_code');
                        $join->on('sales_actuals.year_month', DB::raw(request()->year_month));
                    })
                    ->select('departments.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 1 AND mst_sales_plans.department_code = mst_departments.code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_a'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 3 AND mst_sales_plans.department_code = mst_departments.code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_b'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 2 AND mst_sales_plans.department_code = mst_departments.code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_c'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 4 AND mst_sales_plans.department_code = mst_departments.code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_e'))
                    ->where('departments.delete_flag', '0')
                    ->groupBy('departments.code')
                    ->orderBy('departments.code', 'ASC')
                    ->paginate(20);
                    $sums = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Department::class), function ($join) {
                            $join->on('departments.code', '=', 'sales_actuals.department_code');
                        })
                        ->select(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                        ->where('departments.delete_flag', '0')
                        ->first();
                    $sum_a = $sums->price_a;
                    $sum_b = $sums->price_b;
                    $sum_c = $sums->price_c;
                    $sum_e = $sums->price_e;
                    $count = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Department::class), function ($join) {
                            $join->on('departments.code', '=', 'sales_actuals.department_code');
                        })
                        ->where('year_month', request()->year_month)
                        ->where('departments.delete_flag', '0')
                        ->count();
                }
            } elseif (($request->type ?? 1) == 4) {
                $datas = DB::table(getTableName(Line::class))
                    ->join(getTableName(SalesActual::class), function ($join) {
                        $join->on('lines.line_code', '=', 'sales_actuals.line_code');
                        $join->on('sales_actuals.year_month', DB::raw(request()->year_month));
                    })
                    ->select('lines.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 1 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_a'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 3 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_b'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 2 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_c'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 4 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_e'))
                    ->where('lines.delete_flag', '0')
                    ->groupBy('lines.line_code')
                    ->orderBy('lines.line_code', 'ASC')
                    ->paginate(20);
                    $sums = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Line::class), function ($join) {
                            $join->on('lines.line_code', '=', 'sales_actuals.line_code');
                        })
                        ->select(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                        ->where('lines.delete_flag', '0')
                        ->first();
                    $sum_a = $sums->price_a;
                    $sum_b = $sums->price_b;
                    $sum_c = $sums->price_c;
                    $sum_e = $sums->price_e;
                    $count = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Line::class), function ($join) {
                            $join->on('lines.line_code', '=', 'sales_actuals.line_code');
                        })
                        ->where('year_month', request()->year_month)
                        ->where('lines.delete_flag', '0')
                        ->count();
            } elseif (($request->type ?? 1) == 5) {
                $datas = DB::table(getTableName(Line::class))
                    ->leftJoin(getTableName(SalesActual::class), function ($join) {
                        $join->on('lines.line_code', '=', 'sales_actuals.line_code');
                        $join->on('sales_actuals.year_month', DB::raw(request()->year_month));
                    })
                    ->select('lines.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 1 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_a'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 3 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_b'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 2 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_c'))
                    // ->addSelect(DB::raw('(SELECT SUM(mst_sales_plans.amount) FROM mst_sales_plans where mst_sales_plans.amount_category = 4 AND mst_sales_plans.line_code = mst_lines.line_code AND mst_sales_plans.year_month = "'.request()->year_month.'") as price_e'))
                    ->where('lines.delete_flag', '0')
                    ->groupBy('lines.line_code')
                    ->orderBy('lines.line_code', 'ASC')
                    ->paginate(20);
                    $sums = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Line::class), function ($join) {
                            $join->on('lines.line_code', '=', 'sales_actuals.line_code');
                        })
                        ->select(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
                        ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
                        ->where('lines.delete_flag', '0')
                        ->first();
                    $sum_a = $sums->price_a;
                    $sum_b = $sums->price_b;
                    $sum_c = $sums->price_c;
                    $sum_e = $sums->price_e;
                    $count = DB::table(getTableName(SalesActual::class))
                        ->leftJoin(getTableName(Line::class), function ($join) {
                            $join->on('lines.line_code', '=', 'sales_actuals.line_code');
                        })
                        ->where('year_month', request()->year_month)
                        ->where('lines.delete_flag', '0')
                        ->count();
            }
        }
        // $sums = DB::table(getTableName(SalesActual::class))
        //             ->select(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 1 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_a'))
        //             ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 3 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_b'))
        //             ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 2 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_c'))
        //             ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_actuals.amount_category = 4 AND mst_sales_actuals.year_month = "'.request()->year_month.'" THEN mst_sales_actuals.amount_of_money ELSE 0 END) as price_e'))
        //             ->first();
        // $sum_a = $sums->price_a;
        // $sum_b = $sums->price_b;
        // $sum_c = $sums->price_c;
        // $sum_e = $sums->price_e;
        // $count = DB::table(getTableName(SalePlan::class))->where('year_month', request()->year_month)->count();
        // $datas = SalePlan::all();
        return view('pages.sales.sale_performance_table',[
            'datas' => $datas,
            'sum_a' => $sum_a ?? 0,
            'sum_b' => $sum_b ?? 0,
            'sum_c' => $sum_c ?? 0,
            'sum_e' => $sum_e ?? 0,
            'count' => $count ?? 0,
        ]);
    }

    public function issuanceOfStatementOfOrderAmount(Request $request)
    {
        $datas = [];

        // Check if any filter is provided
        if ($request->filled('supplier_code') || $request->filled('year_month')) {
            $datas = ProductNumber::with('salePlans')->whereHas('salePlans', function ($query) use ($request) {
                $query->when($request->filled('supplier_code'), function ($query) use ($request) {
                    $query->where('supplier_code', $request->supplier_code);
                })->when($request->filled('year_month'), function ($query) use ($request) {
                    $query->where('year_month', $request->year_month);
                });
            })->get();
        }

        return view('pages.sales.issuance_of_statement_of_order_amount', [
            'datas' => $datas,
        ]);
    }

    public function export(Request $request)
    {
        $data = $request->all();
        $data = $this->salesService->salesPlan($data, false);
        $type = $request->input('type');
        $export = new SalePlanExport($data, $type);
        return Excel::download($export, '発注金額明細表.xlsx');
    }
}
