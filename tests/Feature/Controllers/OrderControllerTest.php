<?php

namespace Tests\Feature\Controllers;

use App\Models\Configuration;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Process;
use App\Models\ProcessOrder;
use App\Models\ProductNumber;
use App\Models\ProductPrice;
use App\Models\SalePlan;
use App\Models\UnofficialNotice;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_submit_process()
    {
        // create users for authentication
        $user = Employee::factory()->create();
        $this->actingAs($user);

        $yearMonth = now()->format('Ym');

        // create customers 
        $customer = Customer::factory()->create();

        // create products numbers
        $productNumber = ProductNumber::factory()->create([
            'part_number' => 12345,
            'customer_code' => $customer->customer_code
        ]);
        
        // create product prices
        $productPrice = ProductPrice::factory()->create([
            'part_number' => $productNumber->part_number
        ]);

        // create configurations 
        $configurations = Configuration::factory()->create([
            'parent_part_number' => $productNumber->part_number
        ]);

        
        // create process 
        $process = Process::factory()->create();

        // create process orders 
        $processOrder = ProcessOrder::factory()->create([
            'part_number' => $productNumber->part_number,
            'process_code' => $process->process_code
        ]);

        //create unofficialNotice
        $unofficialNotice = UnofficialNotice::factory()->create([
            'product_number' => $productNumber->part_number,
            'year_and_month' => $yearMonth
        ]);

        $this->assertDatabaseCount(UnofficialNotice::class, 1);

        // Order 15 request
        $response = $this->post('/order/quantity-calculation', [
            'month' => $yearMonth
        ]);
        
        $this->assertDatabaseCount(SalePlan::class, 3);
        
        //create create sales plan for current, next and next 2 months
        $this->assertDatabaseHas(SalePlan::class, ['year_month' => $yearMonth]);
        $this->assertDatabaseHas(SalePlan::class, ['year_month' => Carbon::parse($yearMonth . "01")->addMonths(1)->format("Ym")]);
        $this->assertDatabaseHas(SalePlan::class, ['year_month' => Carbon::parse($yearMonth . "01")->addMonths(2)->format("Ym")]);

        // Checking Quantity
        $salesPlan = SalePlan::first();
        if( in_array($productNumber->product_category, [0, 3]) ){
             $this->assertEquals( 
                    (int) $salesPlan->quantity,
                    (int) $unofficialNotice->current_month * (int) $configurations->number_used
                );
        }

        // Checking Amount Category
        if($process->inside_and_outside_division == 2){
            $this->assertEquals($salesPlan->amount_category, 2);
        } else {
            match ($productNumber->product_category) {
                0 => $this->assertEquals($salesPlan->amount_category, 3),
                1 => $this->assertEquals($salesPlan->amount_category, 1),
                3 => $this->assertEquals($salesPlan->amount_category, 2),
            };
        }

        // Checking the Amount of Money
        if (in_array($productNumber->product_category, [0, 3])) {
            $unitPrice = $productPrice->unit_price;
        } else {
            $unitPrice = $productPrice->sell_price;
        }

        $amountCategory = match ($productNumber->product_category) {
            0 => 3,
            1 => 1,
            3 => 2,
        };

        // Getting Outsourced processing. Ref: ORDER-15 Specs #6-i-2
        if($process->inside_and_outside_division == 2){
            $amountCategory = 2;
        }

        if (in_array($amountCategory, [2, 3])) {
            $roundingIndicator = $customer->purchase_amount_rounding_indicator;
        } else {
            $roundingIndicator = $customer->sales_amount_rounding_indicator;
        }

        $subTotal = $unofficialNotice->current_month * $unitPrice;

        $amount = match ( (int) $roundingIndicator) {
            1 => (int) floor($subTotal),
            2 => (int) ceil($subTotal),
            3 => (int) round($subTotal),
        };

        $this->assertEquals($salesPlan->amount, $amount);
        $response->assertRedirect();
    }
     
    public function test_for_multiple_unofficial_notice()
    {
        // create users for authentication
        $user = Employee::factory()->create();
        $this->actingAs($user);

        $yearMonth = now()->format('Ym');

        // create customers 
        $customer = Customer::factory()->create();

        // create products numbers
        $productNumber = ProductNumber::factory()->create([
            'part_number' => 12345,
            'customer_code' => $customer->customer_code,
            'product_category' => 1
        ]);
        
        // create product prices
        $productPrice = ProductPrice::factory()->create([
            'part_number' => $productNumber->part_number
        ]);

        // create configurations 
        $configurations = Configuration::factory()->create([
            'parent_part_number' => $productNumber->part_number
        ]);

        
        // create process 
        $process = Process::factory()->create();

        // create process orders 
        $processOrder = ProcessOrder::factory()->create([
            'part_number' => $productNumber->part_number,
            'process_code' => $process->process_code
        ]);

        //create unofficialNotice
        $unofficialNotice = UnofficialNotice::factory()->count(5)->create([
            'product_number' => $productNumber->part_number,
            'year_and_month' => $yearMonth
        ]);

        $this->assertDatabaseCount(UnofficialNotice::class, 5);

        // Order 15 request
        $response = $this->post('/order/quantity-calculation', [
            'month' => $yearMonth
        ]);
        
        $this->assertDatabaseCount(SalePlan::class, 15);
        
        //create create sales plan for current, next and next 2 months
        $this->assertDatabaseHas(SalePlan::class, ['year_month' => $yearMonth]);
        $this->assertDatabaseHas(SalePlan::class, ['year_month' => Carbon::parse($yearMonth . "01")->addMonths(1)->format("Ym")]);
        $this->assertDatabaseHas(SalePlan::class, ['year_month' => Carbon::parse($yearMonth . "01")->addMonths(2)->format("Ym")]);
        $response->assertRedirect();
    }
     
}
