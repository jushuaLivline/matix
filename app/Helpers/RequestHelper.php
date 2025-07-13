<?php

namespace App\Helpers;

use App\Models\Customer;
use App\Models\Department;
use App\Models\Line;
use App\Models\Process;
use App\Models\ProductNumber;
use Illuminate\Support\Facades\DB;

class RequestHelper
{
    public static function processRequest($request)
    {
        //Line   
        if ($request->line_code != null && $request->line_name == null) {
            $line_model = DB::table(Line::tableName())->where('line_code', $request->line_code)->where('delete_flag', '0')->first();
            $request->merge(['line_name' => $line_model->line_name]);
        } else if ($request->line_code == null) {
            $request->merge(['line_name' => null]);
        }
        //Department
        if ($request->department_code != null && $request->department_name == null) {
            $department_model = DB::table(Department::tableName())->where('code', $request->department_code)->where('delete_flag', '0')->first();
            $request->merge(['department_name' => $department_model->department_name]);
        } else if ($request->department_code == null)  {
            $request->merge(['department_name' => null]);
        }
        //ProductNumber
        if ($request->product_code != null && $request->product_name == null) {
            $department_model = DB::table(ProductNumber::tableName())->where('part_number', $request->product_code)->where('delete_flag', '0')->first();
            $request->merge(['product_name' => $department_model->product_name]);
        } else if ($request->product_code == null)  {
            $request->merge(['product_name' => null]);
        }
        //Customer
        if ($request->customer_code != null && $request->customer_name == null) {
            $customer_model = DB::table(Customer::tableName())->where('customer_code', $request->customer_code)->where('customer_flag', '1')->where('delete_flag', '0')->first();
            $request->merge(['customer_name' => $customer_model->customer_name]);
        } else if ($request->customer_code == null)  {
            $request->merge(['customer_name' => null]);
        }
        //Supplier
        if ($request->supplier_code != null && $request->supplier_name == null) {
            $supplier_model = DB::table(Customer::tableName())->where('customer_code', $request->supplier_code)->where('supplier_tag', '1')->where('delete_flag', '0')->first();
            $request->merge(['supplier_name' => $supplier_model->customer_name]);
        } else if ($request->supplier_code == null)  {
            $request->merge(['supplier_name' => null]);
        }
        //Manufacturer
        if ($request->manufacturer_code != null && $request->manufacturer_name == null) {
            $manufacturer_model = DB::table(Customer::tableName())->where('customer_code', $request->manufacturer_code)->where('supplier_tag', '1')->where('delete_flag', '0')->first();
            $request->merge(['manufacturer_name' => $manufacturer_model->customer_name]);
        } else if ($request->manufacturer_code == null)  {
            $request->merge(['manufacturer_name' => null]);
        }
        //Process
        if ($request->process_code != null && $request->process_name == null) {
            $process_model = DB::table(Process::tableName())->where('process_code', $request->process_code)->where('delete_flag', '0')->first();
            $request->merge(['process_name' => $process_model->process_name]);
        } else if ($request->process_code == null)  {
            $request->merge(['process_name' => null]);
        }
        
        return $request;
    }

    public static function convertArraysToAssociative($arrayFields)
    {
        // Ensure all fields are arrays
        $fields = array_map(fn($value) => (array) $value, $arrayFields);

        // Transpose the array to group values by index
        $structuredData = array_map(null, ...array_values($fields));

        // Convert grouped values into associative arrays
        $insertData = array_map(fn($values) => array_combine(array_keys($fields), $values), $structuredData);

        return $insertData;
    }

}
