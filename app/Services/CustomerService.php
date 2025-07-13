<?php
namespace App\Services;

use App\Exports\CustomersExport;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class CustomerService{

    // public function getCustomers()
    // {
    //     return Customer::paginate(20);
    // }

    public function createCustomer($data)
    {
        return Customer::create($data);
    }

    public function updateCustomer($data, $id)
    {
        $customer = Customer::find($id);
        $fieldsToUpdate = $data->except('_token');

        if (isset($fieldsToUpdate['customer_flag'])) {
            $customer->customer_flag = $fieldsToUpdate['customer_flag'] ? 1 : 0;
        } else {
            $customer->customer_flag = 0; // Assuming unchecked checkbox means value should be 0
        }

        if (isset($fieldsToUpdate['supplier_tag'])) {
            $customer->supplier_tag = $fieldsToUpdate['supplier_tag'] ? 1 : 0;
        } else {
            $customer->supplier_tag = 0; // Assuming unchecked checkbox means value should be 0
        }

        if (isset($fieldsToUpdate['purchase_report_apply_flag'])) {
            $customer->purchase_report_apply_flag = $fieldsToUpdate['purchase_report_apply_flag'] ? 1 : 0;
        } else {
            $customer->purchase_report_apply_flag = 0; // Assuming unchecked checkbox means value should be 0
        }

        if (isset($fieldsToUpdate['delete_flag'])) {
            $customer->delete_flag = $fieldsToUpdate['delete_flag'] ? 1 : 0;
        } else {
            $customer->delete_flag = 0; // Assuming unchecked checkbox means value should be 0
        }

        foreach ($fieldsToUpdate as $field => $value) {
            if ($customer->$field != $value) {
                $customer->$field = $value;
            }
        }

        $updated = $customer->save();

        return $updated;
    }

    public function searchQuery($data)
    {
        $customer_code = $data->input('customer_code');
        $customer_name = $data->input('customer_name');
        $customer_type = $data->input('supplier_type');
        $delete_flag = $data->input('delete_flag');

        $query = Customer::query();

        if ($customer_code) {
            $query->where('customer_code', 'LIKE', '%' . $customer_code . '%');
        }

        if ($customer_name) {
            $query->where('customer_name', 'LIKE', '%' . $customer_name . '%');
        }

        if ($customer_type) {
            if ($customer_type == 2) {

                $query->where('customer_flag', $customer_type);

            }else if ($customer_type == 3) {

                $query->where('supplier_tag', $customer_type);

            }
        }

        if ($delete_flag) {
            $query->where('delete_flag',  $delete_flag);
        }

        return $query->select('customer_code', 'customer_name', 'post_code', 'address_1', 'telephone_number', 'supplier_tag', 'supplier_classication')->get();
    }
}