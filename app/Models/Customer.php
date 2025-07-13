<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PaginateWithLimit;

class Customer extends Model
{
    use HasFactory, HasModelUtility;
    use PaginateWithLimit;
    /**
     * @var string $table
     */
    // protected $table = 'mst_customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $fillable = [
        'customer_code',
        'customer_name',
        'supplier_name_abbreviation',
        'business_partner_kana_name',
        'branch_factory_name',
        'kana_name_of_branch_factory',
        'post_code',
        'address_1',
        'address_2',
        'telephone_number',
        'fax_number',
        'capital',
        'representative_name',
        'customer_flag',
        'supplier_tag',
        'supplier_classication',
        'purchase_report_apply_flag',
        'sales_amount_rounding_indicator',
        'purchase_amount_rounding_indicator',
        'transfer_source_bank_code',
        'transfer_source_bank_branch_code',
        'transfer_source_account_number',
        'transfer_source_account_clarification',
        'payee_bank_code',
        'transfer_destination_bank_branch_code',
        'transfer_account_number',
        'transfer_account_clasification',
        'transfer_fee_burden_category',
        'bill_ratio',
        'transfer_fee_condition_amount',
        'amount_less_than_transfer_fee_conditions',
        'transfer_fee_condition_or_more_amount',
        'delete_flag'
    ];

    public function salePlans()
    {
        return $this->hasMany(SalePlan::class, 'customer_code', 'customer_code');
    }

    public function salePerformances()
    {
        return $this->hasMany(SalePerformance::class, 'customer_code', 'customer_code');
    }

    public function saleActuals()
    {
        return $this->hasMany(SalesActual::class, 'customer_code', 'customer_code');
    }

    public function purchaseRecords()
    {
        return $this->hasMany(PurchaseRecord::class, 'supplier_code', 'customer_code');
    }
}
