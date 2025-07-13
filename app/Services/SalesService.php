<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Department;
use App\Models\Line;
use App\Models\SalePlan;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SalesService
{
    public function salesPlan(array $data, $paginate = true)
    {
        $yearMonth = Arr::get($data, 'year_month');
        $type = Arr::get($data, 'type', 1);
        if (!$yearMonth) $type = null;

        $data = match ($type) {
            '1' => (function () use ($yearMonth, $paginate) {
                $sales = DB::table(getTableName(Customer::class))
                    ->join(getTableName(SalePlan::class), function ($join) use ($yearMonth) {
                        $join->on('customers.customer_code', '=', 'sales_plans.customer_code');
                        $join->on('sales_plans.year_month', DB::raw($yearMonth));
                    })
                    ->select('customers.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('customers.delete_flag', '0')
                    ->groupBy('customers.customer_code');
                $sales = $paginate ? $sales->orderBy('customers.customer_code', 'ASC')->paginate(20) : $sales->get();
                $sum = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Customer::class), function ($join) {
                        $join->on('customers.customer_code', '=', 'sales_plans.customer_code');
                        $join->where('customers.delete_flag', '0');
                    })
                    ->select(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->first();
                $count = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Customer::class), function ($join) {
                        $join->on('customers.customer_code', '=', 'sales_plans.customer_code');
                    })
                    ->where('year_month', $yearMonth)
                    ->where('customers.delete_flag', '0')
                    ->count();
                return [
                    'sales' => $sales,
                    'sum_a' => $sum->price_a ?? 0,
                    'sum_b' => $sum->price_b ?? 0,
                    'sum_c' => $sum->price_c ?? 0,
                    'sum_e' => $sum->price_e ?? 0,
                    'count' => $count ?? 0,
                ];
            })(),
            '2' => (function () use ($yearMonth, $paginate) {
                $sales = DB::table(getTableName(Department::class))
                    ->join(getTableName(SalePlan::class), function ($join) use ($yearMonth) {
                        $join->on('sales_plans.year_month', DB::raw($yearMonth));
                        $join->on(DB::raw('substr(mst_sales_plans.department_code,1,4)'), '=', DB::raw('substr(mst_departments.code,1,4)'));
                    })
                    ->select('departments.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('departments.delete_flag', '0')
                    ->groupBy(DB::raw('substr(mst_departments.code,1,4)'));
                $sales = $paginate ? $sales->orderBy('mst_sales_plans.department_code', 'ASC')->paginate(20) : $sales->get();
                $sum = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Department::class), function ($join) {
                        $join->on('departments.code', '=', 'sales_plans.department_code');
                    })
                    ->select(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('departments.delete_flag', '0')
                    ->first();
                $count = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Department::class), function ($join) {
                        $join->on('departments.code', '=', 'sales_plans.department_code');
                    })
                    ->where('year_month', $yearMonth)
                    ->where('departments.delete_flag', '0')
                    ->count();
                return [
                    'sales' => $sales,
                    'sum_a' => $sum->price_a ?? 0,
                    'sum_b' => $sum->price_b ?? 0,
                    'sum_c' => $sum->price_c ?? 0,
                    'sum_e' => $sum->price_e ?? 0,
                    'count' => $count ?? 0,
                ];
            })(),
            '3' => (function () use ($yearMonth, $paginate) {
                $sales = DB::table(getTableName(Department::class))
                    ->join(getTableName(SalePlan::class), function ($join) use ($yearMonth) {
                        $join->on('departments.code', '=', 'sales_plans.department_code');
                        $join->on('sales_plans.year_month', DB::raw($yearMonth));
                    })
                    ->select('departments.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('departments.delete_flag', '0')
                    ->groupBy('departments.code');
                $sales = $paginate ? $sales->orderBy('departments.code', 'ASC')->paginate(20) : $sales->get();
                $sum = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Department::class), function ($join) {
                        $join->on('departments.code', '=', 'sales_plans.department_code');
                    })
                    ->select(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('departments.delete_flag', '0')
                    ->first();
                $count = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Department::class), function ($join) {
                        $join->on('departments.code', '=', 'sales_plans.department_code');
                    })
                    ->where('year_month', $yearMonth)
                    ->where('departments.delete_flag', '0')
                    ->count();
                return [
                    'sales' => $sales,
                    'sum_a' => $sum->price_a ?? 0,
                    'sum_b' => $sum->price_b ?? 0,
                    'sum_c' => $sum->price_c ?? 0,
                    'sum_e' => $sum->price_e ?? 0,
                    'count' => $count ?? 0,
                ];
            })(),
            '4' => (function () use ($yearMonth, $paginate) {
                $sales = DB::table(getTableName(Line::class))
                    ->join(getTableName(SalePlan::class), function ($join) use ($yearMonth) {
                        $join->on('lines.line_code', '=', 'sales_plans.line_code');
                        $join->on('sales_plans.year_month', DB::raw($yearMonth));
                    })
                    ->select('lines.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('lines.delete_flag', '0')
                    ->groupBy('lines.line_code');
                $sales = $paginate ? $sales->orderBy('lines.line_code', 'ASC')->paginate(20) : $sales->get();
                $sum = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Line::class), function ($join) {
                        $join->on('lines.line_code', '=', 'sales_plans.line_code');
                    })
                    ->select(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('lines.delete_flag', '0')
                    ->first();
                $count = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Line::class), function ($join) {
                        $join->on('lines.line_code', '=', 'sales_plans.line_code');
                    })
                    ->where('year_month', $yearMonth)
                    ->where('lines.delete_flag', '0')
                    ->count();
                return [
                    'sales' => $sales,
                    'sum_a' => $sum->price_a ?? 0,
                    'sum_b' => $sum->price_b ?? 0,
                    'sum_c' => $sum->price_c ?? 0,
                    'sum_e' => $sum->price_e ?? 0,
                    'count' => $count ?? 0,
                ];
            })(),
            '5' => (function () use ($yearMonth, $paginate) {
                $sales = DB::table(getTableName(Line::class))
                    ->leftJoin(getTableName(SalePlan::class), function ($join) use ($yearMonth) {
                        $join->on('lines.line_code', '=', 'sales_plans.line_code');
                        $join->on('sales_plans.year_month', DB::raw($yearMonth));
                    })
                    ->select('lines.*')
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('lines.delete_flag', '0')
                    ->groupBy('lines.line_code');
                $sales = $paginate ? $sales->orderBy('lines.line_code', 'ASC')->paginate(20) : $sales->get();
                $sum = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Line::class), function ($join) {
                        $join->on('lines.line_code', '=', 'sales_plans.line_code');
                    })
                    ->select(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 1 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_a'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 3 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_b'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 2 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_c'))
                    ->addSelect(DB::raw('SUM(CASE WHEN mst_sales_plans.amount_category = 4 AND mst_sales_plans.year_month = "'.$yearMonth.'" THEN mst_sales_plans.amount ELSE 0 END) as price_e'))
                    ->where('lines.delete_flag', '0')
                    ->first();
                $count = DB::table(getTableName(SalePlan::class))
                    ->leftJoin(getTableName(Line::class), function ($join) {
                        $join->on('lines.line_code', '=', 'sales_plans.line_code');
                    })
                    ->where('year_month', $yearMonth)
                    ->where('lines.delete_flag', '0')
                    ->count();
                return [
                    'sales' => $sales,
                    'sum_a' => $sum->price_a ?? 0,
                    'sum_b' => $sum->price_b ?? 0,
                    'sum_c' => $sum->price_c ?? 0,
                    'sum_e' => $sum->price_e ?? 0,
                    'count' => $count ?? 0,
                ];
            })(),
            default => (function () {
                return [
                    'sales' => [],
                    'sum_a' => 0,
                    'sum_b' => 0,
                    'sum_c' => 0,
                    'sum_e' => 0,
                    'count' => 0,
                ];
            })(),
        };

        return $data;
    }
}
