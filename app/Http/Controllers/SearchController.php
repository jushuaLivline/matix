<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Item;
use App\Models\Line;
use App\Models\MachineNumber;
use App\Models\ManufacturerInfo;
use App\Models\Process;
use App\Models\ProductNumber;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    public function search(Request $request) {
        $model = $request->input('model');
        $query = $request->input('query');
        $hint = $request->input('hint') ?? null;
        $additionalData = $request->input('additional-data') ?? null;
        $queryByField = $request->input('queryByField') ?? null;

        $results = [];
       
        if ($model == 'Customer') {
            $results = $this->searchCustomer($query);
        } else if ($model == 'Supplier') {
            $results = $this->searchSupplier($query, $queryByField);
        } else if ($model == 'Department') {
            $results = $this->searchDepartment($query);
        } else if ($model == 'Department_name') {
            $results = $this->searchDepartmentName($query);
        } else if ($model == 'Line') {
            $results = $this->searchLine($query);
        } else if ($model == 'ManufacturerInfo') {
            $results = $this->searchManufacturerInfo($query);
        } else if ($model == 'NotSupplier') {
            $results = $this->searchNotSupplier($query);
        } else if ($model == 'ProductNumber') {
            if ($hint == 'prod_cat_zero') {
                $results = $this->searchProductNumber($query, true, $queryByField);
            } else {
                $results = $this->searchProductNumber($query,null,$queryByField);
            }
        } else if ($model == 'Process') {
            $results = $this->searchProcess($query, $queryByField);
        } else if ($model == 'ProductMaterial') {
            $results = $this->searchProductMaterial($query, $additionalData);
        } else if ($model == 'Project') {
            $results = $this->searchProject($query);
        }else if ($model == 'Product') {
            $results = $this->searchProduct($query, $additionalData);  // with product_category of 1
        } else if ($model == 'MachineNumber') {
            $results = $this->searchMachineNumber($query);
        } else if ($model == 'Employee') {
            $results = $this->searchEmployee($query);
        } else if ($model == 'Item') {
            $results = $this->searchItem($query);
        } else if($model == "ProcessProcessOrder") {
            $results = $this->searchProcessProcessOrder($query);
        } else if($model == "ProductProcessOrder") {
            $results = $this->searchProductProcessOrder($query);
        }

        return response()->json($results);
    }

    function searchCustomer($query) {
        $results = DB::table(Customer::tableName())->selectRaw("customer_code as code, supplier_name_abbreviation as name")
                        ->whereRaw('(customer_code LIKE ? OR supplier_name_abbreviation LIKE ?) AND customer_flag = 1 AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchSupplier($query, $queryByField) {
        $results = DB::table(Customer::tableName())->selectRaw("customer_code as code, supplier_name_abbreviation as name")
                        ->whereRaw('(customer_code LIKE ? OR supplier_name_abbreviation LIKE ?) AND supplier_tag = 1 AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%']);
         // Add extra conditions if $queryByField is provided
         if (!empty($queryByField)) {
            $results->whereRaw($queryByField);
        }

        return $results->get();
    }

    function searchDepartment($query) {
        $results = DB::table(Department::tableName())->selectRaw("code as code, name as name")
                        ->whereRaw('(code LIKE ? OR name LIKE ?) AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchDepartmentName($query) {
        $results = DB::table(Department::tableName())->selectRaw("code as code, department_name as name")
                        ->whereRaw('(code LIKE ? OR name LIKE ?) AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchLine($query) {
        $results = DB::table(Line::tableName())->selectRaw("line_code as code, line_name as name")
                        ->whereRaw('(line_code LIKE ? OR line_name LIKE ?) AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchManufacturerInfo($query) {
        $results = DB::table(ManufacturerInfo::tableName())->selectRaw("material_manufacturer_code as code, person_in_charge as name")
                        ->whereRaw('(material_manufacturer_code LIKE ? OR person_in_charge LIKE ?)', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchNotSupplier($query) {
        $results = DB::table(Customer::tableName())->selectRaw("customer_code as code, customer_name as name")
                        ->whereRaw('(customer_code LIKE ? OR customer_name LIKE ?) AND supplier_tag = 0 AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchProductNumber($query, $addWhere = null, $queryByField = null) {

        // Build the base query
        $results = DB::table(ProductNumber::tableName())
            ->selectRaw("part_number as code, product_name as name, product_category as pcategory")
            ->where(function ($q) use ($query) {
                $q->where('part_number', 'LIKE', '%' . $query . '%')
                 ->orWhere('product_name', 'LIKE', '%' . $query . '%');
            })
            ->where('delete_flag', '=', 0); // Ensure delete_flag is 0
                        
        // Add additional conditions if $addWhere is true
        if ($addWhere) {
            $results->where('product_category', '=', 0);
        }
        // Add extra conditions if $queryByField is provided
        if (!empty($queryByField)) {
            $results->whereRaw($queryByField);
        }
        return $results->get();
    }

    function searchProcess($query, $queryByField=null) {
        $results = DB::table(Process::tableName())->selectRaw("process_code as code, abbreviation_process_name as name, inside_and_outside_division as pcategory")
                        ->whereRaw('(process_code LIKE ? OR process_name LIKE ?)  AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%']);

        // Add extra conditions if $queryByField is provided
        if (!empty($queryByField)) {
            $results->whereRaw($queryByField);
        }

        return $results->get();
    }

    function searchProductMaterial($query, $additionalData = null) {
        $data = [];
        if($additionalData){
            $data = explode("=", trim($additionalData));
        }

        $results = DB::table(ProductNumber::tableName())->selectRaw("part_number as code, product_name as name")
                        ->whereRaw('(part_number LIKE ? OR product_name LIKE ?) AND product_category = 0  AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->when(array_key_exists(0, $data) && array_key_exists(1, $data), function($q) use ($data){
                            $q->where($data[0], $data[1]);
                        })
                        ->get();

        return $results;
    }

    function searchProduct($query, $additionalData = null) {
        $data = [];
        if($additionalData){
            $data = explode("=", trim($additionalData));
        }

        $results = DB::table(ProductNumber::tableName())->selectRaw("part_number as code, product_name as name")
                        ->whereRaw('(part_number LIKE ? OR product_name LIKE ?) AND product_category = 1  AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->when(array_key_exists(0, $data) && array_key_exists(1, $data), function($q) use ($data){
                            $q->where($data[0], $data[1]);
                        })
                        ->get();

        return $results;
    }

    function searchProject($query) {
        $results = DB::table(Project::tableName())->selectRaw("project_number as code, project_name as name")
                        ->whereRaw('(project_number LIKE ? OR project_name LIKE ?) AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchMachineNumber($query) {
        $results = DB::table(MachineNumber::tableName())->selectRaw("machine_number as code, machine_number_name as name, branch_number")
                        ->whereRaw('(machine_number LIKE ? OR machine_number_name LIKE ?) AND delete_flag = 0 AND completion_date is NULL', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchEmployee($query) {
        $results = DB::table(Employee::tableName())->selectRaw("employee_code as code, employee_name as name")
                        ->whereRaw('(employee_code LIKE ? OR employee_name LIKE ?) AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchItem($query) {
        $results = Item::selectRaw("expense_item as code, item_name as name")
                        ->whereRaw('(expense_item LIKE ? OR item_name LIKE ?) AND delete_flag = 0', ['%' . $query . '%', '%' . $query . '%'])
                        ->get();

        return $results;
    }

    function searchProcessProcessOrder($queryString) {
        $queryBuilder = Process::query()
            ->select('process_code as code', 'abbreviation_process_name as name')
            ->where(function ($query) use ($queryString) {
                $query->where('process_code', 'LIKE', '%' . $queryString . '%')
                      ->orWhere('process_name', 'LIKE', '%' . $queryString . '%');
            })
            ->where('delete_flag', 0)
            ->whereHas('process_order');
    
        return $queryBuilder->get();
    }

    function searchProductProcessOrder($queryString){
        $queryBuilder = ProductNumber::query()
            ->select('part_number as code', 'product_name as name')
            ->where(function ($query) use ($queryString) {
                $query->where('part_number', 'LIKE', '%' . $queryString . '%')
                    ->orWhere('product_name', 'LIKE', '%' . $queryString . '%');
            })
            ->where('delete_flag', 0)
            ->whereHas('process_order', function ($query) {
                $query->whereColumn('process_orders.part_number', 'product_numbers.part_number');
            });
    
        return $queryBuilder->get();
    }
}
