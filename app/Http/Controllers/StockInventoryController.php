<?php

namespace App\Http\Controllers;

use App\Constants\ProductConstant;
use App\Exports\StockInventoryExport;
use App\Models\Customer;
use App\Models\Department;
use App\Models\Line;
use App\Models\ProductNumber;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockInventoryController extends Controller
{
    public function index(Request $request)
    {
        $productCategory = ProductConstant::CATEGORY;
        $products = [];
        $count = 0;

        if ($request->line_code != null && $request->line_name == null) {
            $line_model = Line::where('line_code', $request->line_code)->where('delete_flag', '0')->first();
            $request->merge(['line_name' => $line_model->line_name]);
        } else if ($request->line_code == null) {
            $request->merge(['line_name' => null]);
        }
        if ($request->department_code != null && $request->department_name == null) {
            $department_model = Department::where('code', $request->department_code)->where('delete_flag', '0')->first();
            $request->merge(['department_name' => $department_model->department_name]);
        } else if ($request->department_code == null)  {
            $request->merge(['department_name' => null]);
        }
        if ($request->customer_code != null && $request->customer_name == null) {
            $customer_model = Customer::where('customer_code', $request->customer_code)->where('supplier_tag', '0')->where('delete_flag', '0')->first();
            $request->merge(['customer_name' => $customer_model->customer_name]);
        } else if ($request->customer_code == null)  {
            $request->merge(['customer_name' => null]);
        }

        if ($request->all()) {
            $csvFilePath = public_path('csv/Stock Inventory - Dummy data.csv');

            if (!file_exists($csvFilePath)) {
                return response()->json(['error' => 'CSV file not found'], 404);
            }

            $csvData = array_map('str_getcsv', file($csvFilePath));
            $headers = array_shift($csvData);

            $jsonData = [];
            foreach ($csvData as $row) {
                $jsonData[] = array_combine($headers, $row);
            }
            $stockCsv = [];
            foreach ($jsonData as $csvRow) {
                $stockCsv[$csvRow["part_number"]] = $csvRow["Stock"];
            }

            $query = ProductNumber::query()
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
                            ->when($request->product_code, function($query) use ($request) {
                                $query->where("product_code", "LIKE", "%". $request->product_code . "%");
                            })
                            ->when($request->customer_code, function($query) use ($request) {
                                $query->where("customer_code", "LIKE", "%". $request->customer_code . "%");
                            })
                            ->when($request->product_category, function($query) use ($request) {
                                $query->where("product_category", "LIKE", "%". $request->product_category . "%");
                            })
                            ->when($request->production_division, function($query) use ($request) {
                                $query->where("production_division", $request->production_division);
                            })
                            ->where(function($query) use ($request) {
                                if ($request->delete_flag != '2') {
                                    $query->where("delete_flag", $request->delete_flag);
                                }
                            })
                            ->where('product_category', '1');

            $count = (clone $query)->count();

            $products = $query
                            ->paginate(20);
            $departments = Department::selectRaw("code as id, name as name")->get();
            $customers = Customer::selectRaw("customer_code as id, customer_name as name")->where('supplier_tag', '0')->get();
            $lines = Line::selectRaw("line_code as id, line_name as name")->get();
            $departmentMap = [];
            foreach ($departments as $department) {
                $departmentMap[$department->id] = $department;
            }

            foreach ($products as $key => $product) {
                $product->category = $product->product_category;
                $product->customer = '';
                $product->department = '';
                $product->line = '';

                if (isset($departmentMap[$product->department_code])) {
                    $product->department = $departmentMap[$product->department_code]->name;
                }
                foreach ($customers as $key => $customer) {
                    if ($customer->id == $product->customer_code) {
                        $product->customer = $customer->name;
                    }
                }
                foreach ($lines as $key => $line) {
                    if ($line->id == $product->line_code) {
                        $product->line = $line->name;
                    }
                }

                if (isset($stockCsv[$product->part_number])) {
                    $product->stock = $stockCsv[$product->part_number];
                } else {
                    $product->stock = 0;
                }
            }
            $parameters = [
                'part_number' => $request->part_number,
                'product_name' => $request->product_name,
                'line_code' => $request->line_code,
                'line_name' => $request->line_name,
                'department_code' => $request->department_code,
                'department_name' => $request->department_name,
                'supplier_code' => $request->supplier_code,
                'supplier_name' => $request->supplier_name,
                'product_category' => $request->product_category,
                'delete_flag' => $request->delete_flag
            ];
            $products->appends($parameters);
        }

        return view('pages.stock_inventory_list', [
            'productCategory' => $productCategory,
            'products' => $products,
            'count' => $count,
            'productionDivision' => ProductConstant::PRODUCTION_DIVISION,
        ]);
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
                        ->when($request->product_code, function($query) use ($request) {
                            $query->where("product_code", "LIKE", "%". $request->product_code . "%");
                        })
                        ->when($request->product_category, function($query) use ($request) {
                            $query->where("product_category", "LIKE", "%". $request->product_category . "%");
                        })
                        ->where(function($query) use ($request) {
                            if ($request->delete_flag != '2') {
                                $query->where("delete_flag", $request->delete_flag);
                            }
                        })
                        ->where('product_category', '1')
                        ->selectRaw('
                            part_number,
                            product_name,
                            line_code,
                            department_code
                            ')
                        ->get();
        $csvFilePath = public_path('csv/Stock Inventory - Dummy data.csv');

        if (!file_exists($csvFilePath)) {
            return response()->json(['error' => 'CSV file not found'], 404);
        }

        $csvData = array_map('str_getcsv', file($csvFilePath));
        $headers = array_shift($csvData);

        $jsonData = [];
        foreach ($csvData as $row) {
            $jsonData[] = array_combine($headers, $row);
        }
        $stockCsv = [];
        foreach ($jsonData as $csvRow) {
            $stockCsv[$csvRow["part_number"]] = $csvRow["Stock"];
        }

        $departments = Department::selectRaw("code as id, name as name")->get();
        $lines = Line::selectRaw("line_code as id, line_name as name")->get();
        $departmentMap = [];
        foreach ($departments as $department) {
            $departmentMap[$department->id] = $department;
        }
        foreach ($products as $key => $product) {
            if (isset($departmentMap[$product->department_code])) {
                $product->department = $departmentMap[$product->department_code]->name;
            }
            foreach ($lines as $key => $line) {
                if ($line->id == $product->line_code) {
                    $product->line_code = $line->name;
                }
            }

            if (isset($stockCsv[$product->part_number])) {
                $product->stock = $stockCsv[$product->part_number];
            } else {
                $product->stock = 0;
            }
        }

        $fileName = '製品在庫検索・一覧.xlsx';
        return Excel::download(new StockInventoryExport($products), $fileName);

    }
}
