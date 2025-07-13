<?php

use App\Http\Controllers\API\DateFormatValidatorController;
use App\Http\Controllers\API\LookUpAutoSearchController;
use App\Http\Controllers\API\OutsourcedProcessingController;
use App\Http\Controllers\API\ProductMaterialHierarchyController;
use App\Http\Controllers\API\ProductNumberController;
use App\Http\Controllers\API\PurchaseRecordController;
use App\Http\Controllers\API\SupplyMaterialArrivalController;
use App\Http\Controllers\API\SupplyMaterialOrderController;
use App\Http\Controllers\API\ValidateExistController;
use App\Http\Controllers\API\ValidationMessagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('supply-material-order', SupplyMaterialOrderController::class);
Route::apiResource('supply-material-arival', SupplyMaterialArrivalController::class);
Route::apiResource('outsourced-processing', OutsourcedProcessingController::class);
Route::put('outsourced-data/{id}', [OutsourcedProcessingController::class, 'updateData']);
Route::get('product-material-hierarchy', [ProductMaterialHierarchyController::class, 'fetchHierarchy']);
Route::get('export-product-material-hierarchy', [ProductMaterialHierarchyController::class, 'exportHierarchy'])->name('export.product-material.hierarchy');

Route::get("purchase/purchase-requisition-input/{id}", [PurchaseRecordController::class, 'purchaseRequisitionInput']);
Route::get("purchase/purchase-record-input/{id}", [PurchaseRecordController::class, 'purchaseRecordInputProcess']);
Route::get("purchase/approval-route-setting/{id}", [PurchaseRecordController::class, 'purchaseRecordInputProcess']);
Route::get("purchase/purchasing-item-purchase-record-input/{id}", [PurchaseRecordController::class, 'purchasingItemPurchaseRecordInput']);
// Route::put('/outsource-processing/{id}', [OutsourcedProcessingController::class, 'update']);

Route::post("part-number/check-exists", [ProductNumberController::class, 'checkIfExist']);
Route::post('validate-exists', ValidateExistController::class);
Route::post('lookup-autosearch', LookUpAutoSearchController::class);
Route::get('validation-messages', ValidationMessagesController::class);
Route::post('validate-date-format', DateFormatValidatorController::class);
