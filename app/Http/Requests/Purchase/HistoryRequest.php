<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class HistoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // 基本情報
            'id' => 'nullable|integer|unsigned',
            'purchase_record_no' => 'required|string|max:255',
            'voucher_class' => 'required|string|max:255',
            'slip_type' => 'required|string|max:255',
            'purchase_category' => 'required|string|max:255',
            
            // 日付関連
            'arrival_date' => 'nullable|date',
            
            // コード関連
            'supplier_code' => 'required|string|max:255',
            'machine_number' => 'nullable|string|max:255',
            'branch_number' => 'nullable|string|max:255',
            'department_code' => 'nullable|string|max:255',
            'line_code' => 'nullable|string|max:255',
            'expense_item' => 'required|string|max:255',
            'subsidy_items' => 'nullable|string|max:255',
            
            // 製品情報
            'part_number' => 'required|string|max:255',
            'product_name' => 'required|string|max:255',
            'standard' => 'nullable|string|max:255',
            'where_used_code' => 'nullable|string|max:255',
            
            // 数量・金額関連
            'quantity' => 'required|integer',
            'unit_code' => 'required|string|max:255',
            'unit_price' => 'required|numeric',
            'amount_of_money' => 'nullable|numeric',
            'tax_classification' => 'required|string|max:255',
            
            // 伝票・プロジェクト情報
            'slip_no' => 'nullable|string|max:255',
            'project_number' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            
            // システム管理項目
            'created_at' => 'nullable|date',
            'creator' => 'nullable|integer|unsigned',
            'updated_at' => 'nullable|date',
            'updator' => 'nullable|integer|unsigned',
        ];
    }

    public function messages()
    {
       return [
           'required' => ':attributeは必須です',
           'string' => ':attributeは文字列で入力してください',
           'numeric' => ':attributeは数値で入力してください',
           'date' => ':attributeは日付を入力してください',
           'integer' => ':attributeは整数で入力してください',
           'max' => ':attributeは:max文字以内で入力してください',
           'min' => ':attributeは:min以上で入力してください',
       ];
    }
    
    public function attributes()
    {
       return [
           // 基本情報
           'id' => 'ID',
           'purchase_record_no' => '購入記録番号',
           'voucher_class' => '伝票区分',
           'slip_type' => '伝票種類',
           'purchase_category' => '購入区分',
    
           // 日付関連
           'arrival_date' => '入荷日',
    
           // コード関連
           'supplier_code' => '仕入先',
           'machine_number' => '機番',
           'department_code' => '部門',
           'line_code' => 'ライン',
           'expense_item' => '費目',
           'subsidy_items' => '補助項目',
    
           // 製品情報
           'part_number' => '品番',
           'product_name' => '品名',
           'standard' => '規格',
           'where_used_code' => '使用先',
    
           // 数量・金額関連
           'quantity' => '数量',
           'unit_code' => '単位',
           'unit_price' => '単価',
           'amount_of_money' => '金額',
           'tax_classification' => '課税区分',
    
           // 伝票・プロジェクト情報
           'slip_no' => '伝票No',
           'project_number' => 'プロジェクトNo.',
           'remarks' => '備考',
    
           // システム管理項目
           'created_at' => '作成日時',
           'creator' => '作成者',
           'updated_at' => '更新日時',
           'updator' => '更新者',
       ];
    }
}