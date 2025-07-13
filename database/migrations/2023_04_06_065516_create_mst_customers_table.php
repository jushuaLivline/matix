<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_code', 45)->unique();
            $table->string('customer_name', 45)->nullable();
            $table->string("business_partner_kana_name")->nullable();
            $table->string("branch_factory_name")->nullable();
            $table->string("kana_name_of_branch_factory")->nullable();
            $table->string("supplier_name_abbreviation")->nullable();
            $table->string("factory_classification_code")->nullable();
            $table->string("post_code")->nullable();
            $table->string("address_1")->nullable();
            $table->string("address_2")->nullable();
            $table->string("telephone_number")->nullable();
            $table->string("fax_number")->nullable();
            $table->string("representative_name")->nullable();
            $table->double("capital")->nullable();
            $table->boolean("customer_flag")->default(0)->nullable();
            $table->boolean("supplier_tag")->default(0)->nullable();
            $table->string("supplier_classication")->comment("1: Processing manufacturer 2: Material manufacturer 3: Others")->nullable();
            $table->string("purchase_report_apply_flag")->comment("Check when applying to purchase results when accepting purchases")->nullable();
            $table->string("sales_amount_rounding_indicator", 1)->comment("1: Round down 2: Round up 3: Round off")->nullable();
            $table->string("purchase_amount_rounding_indicator", 1)->comment("1: Round down 2: Round up 3: Round off")->nullable();
            $table->string("bill_ratio")->nullable();
            $table->string("transfer_source_bank_code")->nullable();
            $table->string("transfer_source_bank_branch_code")->nullable();
            $table->string("transfer_source_account_number")->nullable();
            $table->string("transfer_source_account_clarification")->nullable();
            $table->string("payee_bank_code")->nullable();
            $table->string("transfer_destination_bank_branch_code")->nullable();
            $table->string("transfer_account_number")->nullable();
            $table->string("transfer_account_clasification")->nullable();
            $table->string("transfer_fee_burden_category")->nullable();
            $table->string("transfer_fee_condition_amount")->nullable();
            $table->string("amount_less_than_transfer_fee_conditions")->nullable();
            $table->string("transfer_fee_condition_or_more_amount")->nullable();
            $table->boolean("delete_flag")->nullable();
            $table->dateTime("created_at")->useCurrent();
            $table->unsignedBigInteger("creator")->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->unsignedBigInteger("updator")->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
