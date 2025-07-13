<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\EstimateController;
use App\Http\Controllers\EstimateReplyDetailController;
use App\Http\Controllers\MonthyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CostPriceController;
use App\Http\Controllers\DailyProductionControlController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentInspectionController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\InspectionItemController;
use App\Http\Controllers\MachineNumberController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\PurchaseRecordController; 
use App\Http\Controllers\OutsourceController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MstMachineController;
use App\Http\Controllers\MstLineController;

use App\Http\Controllers\TemporaryUploadController;
use App\Http\Controllers\StockInventoryController;
use Illuminate\Support\Facades\Route;

// PURCHASE
use App\Http\Controllers\Purchase\RequisitionController;
use App\Http\Controllers\Purchase\ReceiptController;
use App\Http\Controllers\Purchase\AcceptanceController;
use App\Http\Controllers\Purchase\HistoryController;
use App\Http\Controllers\Purchase\OrderController as PurchaseOrderController;
use App\Http\Controllers\Purchase\Order\ProcessController as PurchaseProcessController;
use App\Http\Controllers\Purchase\Order\Process\DetailController as PurchaseProcessDetailController;
use App\Http\Controllers\Purchase\Order\ConfirmController as PurchaseConfirmController;
use App\Http\Controllers\Purchase\ActualController as PurchaseActualController;
use App\Http\Controllers\Purchase\Order\ReissueController as PurchaseReissueController;
use App\Http\Controllers\Purchase\Approval\ListController;
use App\Http\Controllers\Purchase\Approval\DetailController as PurchaseApprovalDetailController;
use App\Http\Controllers\Purchase\SupplierController as PurchaseSupplierController;

// MATERIALS
use App\Http\Controllers\Material\Kanban\Controller;
use App\Http\Controllers\Material\Kanban\TemporaryController;
use App\Http\Controllers\Material\ReturnController;
use App\Http\Controllers\Material\Order\InspectionController;
use App\Http\Controllers\Material\Order\DetailController;
use App\Http\Controllers\Material\FractionController;
use App\Http\Controllers\Material\ProcurementController;
use App\Http\Controllers\Material\Setting\GroupController;
use App\Http\Controllers\Material\Setting\ManufacturerController;
use App\Http\Controllers\Material\OrderController as MaterialOrderControler;
use App\Http\Controllers\Material\ArrivalsController;
use App\Http\Controllers\Material\ReturnSummaryController;

// OUTSOURCE
use App\Http\Controllers\Outsource\FractionController as OutsourceFractionController;
use App\Http\Controllers\Outsource\SupplyController;
use App\Http\Controllers\Outsource\KanbanController;
use App\Http\Controllers\Outsource\Kanban\TemporaryController as OutsourceTemporaryController;
use App\Http\Controllers\Outsource\SpecifiedController;
use App\Http\Controllers\Outsource\OrderController  as OutsourceOrderControler;
use App\Http\Controllers\Outsource\Supply\KanbanController as SupplyKanbanController;
use App\Http\Controllers\Outsource\Inspection\CancelController;
use App\Http\Controllers\Outsource\Supply\ReplenishmentController;

use App\Http\Controllers\Outsource\Arrival\PendingController;
use App\Http\Controllers\Outsource\Defect\MaterialController as DefectMaterialController;
use App\Http\Controllers\Outsource\ArrivalController as OutsourceArrivalController;
use App\Http\Controllers\Outsource\Defect\ProcessController;
use App\Http\Controllers\Outsource\Order\SlipController;
use App\Http\Controllers\Outsource\InspectionController as OutsourceInspectionController;
use App\Http\Controllers\Outsource\Delivery\ReissueController;

// ORDER
use App\Http\Controllers\Order\ConfirmedOrderController;
use App\Http\Controllers\Order\Kanban\ForecastController as KanbanForecast;
use App\Http\Controllers\Order\Parts\ForecastController as PartsForecastController;
use App\Http\Controllers\Order\Forecast\SummaryController;
use App\Http\Controllers\Order\ForecastController;

// SHIPMENT
use App\Http\Controllers\Shipment\ActualController;
use App\Http\Controllers\Shipment\SummaryController as ShipmentSummaryController;

// MASTER
use App\Http\Controllers\Master\SupplierController;
use App\Http\Controllers\Master\LineController as MasterLineController;

// ESTIMATE
use App\Http\Controllers\Estimate\SearchController as EstimateSearchController;
use App\Http\Controllers\Estimate\Response\CreateController as ResponseCreateController;
use App\Http\Controllers\Estimate\Request\CreateController;
use App\Http\Controllers\Estimate\DetailController as EstimateDetailController;

// MASTER
use App\Http\Controllers\Master\PartController;
use App\Http\Controllers\Master\Process\SequenceController as ProcessSequenceModal;
use App\Http\Controllers\Master\Process\UnitPriceController as ProcessUnitPriceModal;
use App\Http\Controllers\Master\Process\PartNumberUnitPriceController;
use App\Http\Controllers\Master\ProjectController as MasterProjectController;
use App\Http\Controllers\Master\KanbanController as MasterKanbanController;
use App\Http\Controllers\Master\MachineController;
use App\Http\Controllers\Master\EmployeeController;
use App\Http\Controllers\Master\CalendarController;

// AUTH
use App\Http\Controllers\Auth\RemindController as AuthRemindController;
use App\Http\Controllers\Auth\PasswordController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("login", [AuthController::class, 'login'])->name("auth.login");
Route::post("login", [AuthController::class, 'processLogin'])->name("auth.login.process");

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    // AUTH-3
    Route::get('remind/id', [AuthRemindController::class, 'getRemindId'])->name('authRemindGetId');
    Route::post('remind/id', [AuthRemindController::class, 'remindId'])->name('authRemindProcessId');

    // AUTH-4
    Route::resource('remind/password', AuthRemindController::class)->only(['index', 'store'])->names('authRemind');

    Route::resource('reset/password', PasswordController::class)->only(['index', 'update'])->names('resetPassword');
});

Route::get("complete/{type?}", [AuthController::class, 'complete'])->name("auth.complete");

Route::group(['middleware' => 'auth:web'], function () {
    Route::get('/', function () {
        return redirect()->route('dashboard.index');
    });

    Route::get("/stock-inventory/list", [StockInventoryController::class, 'index'])->name("stockInventory.index");
    Route::post('/stock-inventory/export-csv', [StockInventoryController::class, 'exportCSV'])->name('stockInventory.export.csv');

    Route::get("dashboard", [DashboardController::class, 'index'])->name("dashboard.index");
    Route::get("logout", [AuthController::class, 'logout'])->name("auth.logout");
    Route::get("order/data-acquisition", [OrderController::class, 'dataAcquisition'])->name("order.data.acquisition");
    Route::post("order/data-acquisition", [OrderController::class, 'processDataAcquisition'])->name("order.process.data.acquisition");
    Route::get("order/kanban-input", [OrderController::class, 'kanbanInput'])->name("order.kanban.input");
    Route::post("order/kanban-input", [OrderController::class, 'kanbanInputSave'])->name("order.kanban.input.save");
    Route::get("order/specified-part", [OrderController::class, 'specifiedPart'])->name("order.specified.part");
    Route::post("order/specified-part", [OrderController::class, 'specifiedPartPost'])->name("order.specified.part.post");
    Route::get("order/data-import-content-confirmation/{input_id}", [OrderController::class, 'dataImportContentConfirmation'])->name("order.data.import.content.confirmation");
    Route::post("order/data-import-content-confirmation/{input_id}", [OrderController::class, 'processDataImportContentConfirmation'])->name("order.process.data.import.content.confirmation");
    Route::get("order/detailed", [OrderController::class, 'detailed'])->name("order.detailed");
    Route::get("order/quantity-calculation", [OrderController::class, 'quantityCalculation'])->name("order.quantity.calculation");
    Route::post("order/quantity-calculation", [OrderController::class, 'quantityCalculationPost'])->name("order.quantity.calculation.post");


    Route::group(['prefix' => 'order', 'as' => 'order.'], function () {
        // Order - 8 - Fixed
        Route::prefix('kanban/forecast')->controller(KanbanForecast::class)->group(function () {
            Route::resource('/', KanbanForecast::class)->except(['show', 'destroy', 'edit'])->names('kanbanForecast');
            //Route::post('/store', [KanbanForecast::class, 'store'])->name('kanbanForecast.store');
        });

        // Order 9
        Route::prefix('parts/forecast')->controller(PartsForecastController::class)->group(function () {
            Route::resource('/', PartsForecastController::class)
                ->except(['show', 'destroy', 'edit']);
            Route::post('/search-month', 'searchMonth')->name('search.month');
            Route::post('/add-update', 'addUpdate')->name('add.update');
        });

        //Order 16 - Put first before ORDER 12, 13 to avoid conflict
        Route::prefix('forecast/summary')->controller(controller: SummaryController::class)->group(function () {
            Route::get('/excel_export', [SummaryController::class, 'excel_export'])->name('forecastSummaryExcelExport');
            Route::resource('/', SummaryController::class)->only(['index'])->names('forecastSummary');
        });

        //Order 12, 13
        Route::prefix('forecast')->controller(ForecastController::class)->group(function () {
            Route::get('/excel_export', [ForecastController::class, 'excel_export'])->name('forecastExcelExport');
            Route::resource('/', ForecastController::class)->only(['index'])->names('forecast');
            Route::get('/{id}', [ForecastController::class, 'show'])->name('forecast.show');
            Route::put('/{id}', [ForecastController::class, 'update'])->name('forecast.update');
        });

        // ORDER 17 & 18
        Route::resource('confirmed', ConfirmedOrderController::class)
                    ->except(['show', 'destroy', 'edit', 'store'])
                    ->names('confirmed');
        Route::prefix('confirmed')->controller(ConfirmedOrderController::class)->group(function () {
            Route::delete('delete', 'delete')->name('confirmed.delete');
            Route::post('bulk-register', 'bulkRegister')->name('confirmed.bulk.register'); // Fix naming consistency
            Route::get('excel_export', 'excel_export')->name('confirmed.export');

        });
    });

    Route::group(['prefix' => 'material', 'as' => 'material.'], function () {
        //Material 19 - Fixed
        Route::resource('kanban', Controller::class)->except(['show','destroy', 'edit'])->names('kanbanCreate');
        Route::get("ajax-check-kanban-management-no", [Controller::class, 'ajaxCheckKanbanManagementNo'])->name("ajaxCheckKanbanManagementNo");
        
        //Material 20 - Fixed
        Route::prefix('kanban/temporary')->controller(TemporaryController::class)->group(function () {
            Route::resource('/', TemporaryController::class)->except(['show', 'destroy', 'edit'])->names('kanbanTemporary');
            Route::get('/fetch-kanban-details', 'fetchKanbanDetails')->name('fetch.kanban.details');
            Route::get('/fetch-product-details', 'fetchProductDetails')->name('fetch.product.details');
            Route::post('/update-temporary-data', 'updateTemporaryData')->name('updateTemporaryData');
            Route::post('/save-temporary-data', 'saveTemporaryData')->name('saveTemporaryData');
            Route::delete('/remove-temporary-data/{tempDataId}', 'removeTemporaryData');
        });
        
        //Material 21 - Fixed
        Route::post('/fraction/store_session', [FractionController::class, 'store_session'])->name('storeData');
        Route::delete('/fraction/cancel_session/{tempDataId}', [FractionController::class, 'cancel_session']);
        Route::resource('fraction', FractionController::class)->except(['show','destroy', 'edit'])->names('fractionCreate');
        
        //Material 22 - Fixed
        Route::resource('order', MaterialOrderControler::class)->except(['create','store','show','destroy','edit','update']);
        Route::get('/order/excel_export', [MaterialOrderControler::class, 'excel_export'])->name('order.export.csv');

        //Material 23 - Fixed
        Route::resource('/order/detail', DetailController::class)->except(['create','store','show','destroy','edit','update'])->names('order.detail');
        Route::get("/order/detail/PDF_export", [DetailController::class, 'PDF_export'])->name("order.detail.pdf_export");

        //Material 24 - Fixed
        Route::delete('/order/inspection/cancel_session/{tempDataId}', [InspectionController::class, 'cancel_session']);
        Route::post('/order/inspection/store_session', [InspectionController::class, 'store_session'])->name('order.inspections.storeData');
        Route::resource('/order/inspection', InspectionController::class)->except(['show','destroy', 'edit'])->names('order.inspections');

        //Material 25 - Fixed
        Route::resource("/order/arrivals", ArrivalsController::class)->except(['create','store','show','edit','update','destroy'])->names([
            'index' => 'received.materials.index',
        ]);
        Route::get('/order/arrivals/excel_export', [ArrivalsController::class, 'excel_export'])->name('arrivalExport.csv');

        //material 27,28 - Fixed
        Route::resource('return', ReturnController::class)->except(['show','destroy', 'edit'])->names('returnCreate');
        Route::get('/return/excel_export', [ReturnController::class, 'excel_export'])->name('returnExcelExport');

        //Material 29 - Fixed
        Route::resource("return/summary", ReturnSummaryController::class)
            ->except(['create','store','show','edit','update','destroy']) 
            ->names('return.summary');
        Route::get('/return/summary/excel_export', [ReturnSummaryController::class, 'excel_export'])->name('return.summary.excel_eport');

        //Material 30, 31 - Fixed
        Route::get('/procurement/excel_export', [ProcurementController::class, 'excel_export'])->name('excel.export');
        Route::get('/procurement/pdf_export', [ProcurementController::class, 'pdf_export'])->name('pdf.export');
        Route::resource('procurement', ProcurementController::class);

        //Material 32
        Route::resource('/setting/group', GroupController::class)->only(['store', 'update', 'destroy'])->names('settingGroup');

        //Material 34
        Route::resource('/setting/manufacturer', ManufacturerController::class)->only(['store', 'update'])->names('settingManufacturer');
    });

    Route::group(['prefix' => 'outsource', 'as' => 'outsource.'], function () {
        // OUTSOURCE - 36 - Fixed
        Route::resource('kanban', KanbanController::class)->except(['show','destroy', 'edit'])->names('kanbanCreate');
        Route::get("ajax-check-kanban-management-no", [KanbanController::class, 'ajaxCheckKanbanManagementNo'])->name("ajaxCheckKanbanManagementNo");

        // OUTSOURCE - 37 - Fixed
        Route::prefix('kanban/temporary')->controller(OutsourceTemporaryController::class)->group(function () {
            Route::resource('/', OutsourceTemporaryController::class)->except(['show', 'destroy', 'edit'])->names('kanbanTemporary');
            Route::post('/temporary-kanban-entry/search-by-management-no-and-instruction-date', [OutsourceTemporaryController::class, 'searchByManagementNoAndInstructionDate'])->name('kanban.searchByManagementNoAndInstructionDate');
            Route::post('/kanban-store-data', [OutsourceTemporaryController::class, 'kanbanStoreData'])->name('kanban.storeData');
            Route::post('/kanban-update-data', [OutsourceTemporaryController::class, 'kanbanUpdateData'])->name('kanban.updateData');
            Route::post('/kanban-temp-data', [OutsourceTemporaryController::class, 'kanbanTempData'])->name('kanban.temp.storeData');
            Route::post('/kanban-temp-update-data', [OutsourceTemporaryController::class, 'kanbanTempUpdateData'])->name('kanban.temp.updateData');
            Route::post('/kanban-fetch-uniform-capacity', [OutsourceTemporaryController::class, 'kanbanFetchUniformCapacity'])->name('kanban.fetch.uniform.capacity');
            Route::delete('/kanban-delete-data/{instructionDataId}', [OutsourceTemporaryController::class, 'kanbanDeleteData'])->name('kanban.deleteData');
        });
        
        // OUTSOURCE - 38 - Fixed
        Route::prefix('fraction')->controller(OutsourceFractionController::class)->group(function () {
            Route::resource('/', OutsourceFractionController::class)
                ->except(['show', 'destroy', 'edit'])
                ->names([
                    'create' => 'fraction.temporary.instructionEntry',
                    'store' => 'fraction.storeData',
                ]);
            Route::post('/temp-data', 'tempData')->name('fraction.temp.storeData');
            Route::post('/temp-update-data', 'tempUpdateData')->name('fraction.temp.updateData');
            Route::post('/get-process-name', 'getProcessName')->name('fraction.getProcessName');
            Route::delete('/delete-data/{instructionDataId}', 'deleteData')->name('fraction.deleteData');
        });
      
        // OUTSOURCE - 39 - Fixed
        Route::resource('order', OutsourceOrderControler::class)->only(['index']);
        Route::get('/order/excel_export', [OutsourceOrderControler::class, 'excel_export'])->name('order.export.csv');

        // OUTSOURCE - 40 - Fixed
        Route::resource('/order/slip', SlipController::class)->only(['index'])->names('order.slip');
        Route::get('/order/slip/pdf_export', [SlipController::class, 'pdf_export'])->name('order.slip.pdf.export');
         
        
        // OUTSOURCE - 41 - Fixed
        Route::prefix('delivery')->controller(ReissueController::class)->group(function () {
            Route::resource('/reissue', ReissueController::class)
                ->except(['show', 'destroy', 'edit']);

            Route::get('/reissue-invoice-pdf', 'reissueInvoicePdf')->name('reissue.invoice.pdf');
            Route::post('/get-customer-name', 'getCustomerName')->name('get.customer.name');
        });
      
        // OUTSOURCE - 43 - Fixed
        Route::resource('/delivery/specified', SpecifiedController::class)->only(['index'])->names('delivery.specified');
        Route::get('/delivery/specified/pdf_export', [SpecifiedController::class, 'pdf_export'])->name('pdf.export');
        
        // OUTSOURCE - 44 - Fixed
        Route::prefix('supply/kanban')->controller(SupplyKanbanController::class)->group(function () {
            Route::resource('/', SupplyKanbanController::class)->except(['show', 'destroy', 'edit'])->names('kanbanSupply');
        });
        

        // OUTSOURCE - 45 - Fixed
        Route::resource('supply/replenishment', ReplenishmentController::class)->except(['show', 'destroy', 'edit'])->names('supplyReplenishment');
        Route::prefix('supply/replenishment')->controller(ReplenishmentController::class)->group(function () {
            Route::post('/store_session', [ReplenishmentController::class, 'store_session'])->name('supplyReplenishment.storeData');
            Route::delete('/cancel_session/{tempDataId}', [ReplenishmentController::class, 'cancel_session']);
        });
                
        // OUTSOURCE - 46 - Fixed
        Route::get('supply/excel_export', [SupplyController::class, 'excel_export'])->name('supply.excel_export');
        Route::post('supply/get-supplier-name', [SupplyController::class, 'getSupplierName'])->name('supply.getSupplierName');
        Route::resource('supply', SupplyController::class)
        ->only(['index','create', 'store', 'edit', 'update'])
        ->names([ 
            'edit'   => 'supplyEdit', 
            'update' => 'supplyUpdate'
        ]);
        
        // OUTSOURCE - 48 - Fixed
        Route::resource('inspection', OutsourceInspectionController::class)->except(['show','destroy', 'edit'])->names('inspectionCreate');
        
        // OUTSOURCE - 49- Fixed
        Route::prefix('arrival')->controller(OutsourceArrivalController::class)->group(function () {
            Route::get('/', [OutsourceArrivalController::class, 'index'])->name("arrival");
            Route::get('/export', [OutsourceArrivalController::class, 'export'])->name('arrivalExport');
        });

        // OUTSOURCE - 50 - Fixed
        Route::resource('/inspection/cancel', CancelController::class)->only(['index', 'destroy'])->names('inspectionCancel');
      
        // OUTSOURCE - 51 - Fixed
        Route::resource('arrival/pending', PendingController::class)->only(['index'])->names('arrival.pending');
        Route::get('/arrival/pending/excel_export', [PendingController::class, 'excel_export'])->name('arrival.pending.export.csv');

        // OUTSOURCE - 52 - Fixed && OUTSOURCE - 53 - Fixed
        Route::prefix('defect/material')->controller(DefectMaterialController::class)->group(function () {
            Route::resource('/', DefectMaterialController::class)
                ->only(['index', 'create'])
                ->names('defect.material');
            // OUTSOURCE-53 Update table row record via ajax - [OLD]
            Route::put('/{id}', [DefectMaterialController::class, 'update'])->name('defect.material.update');
            // OUTROURCE-52 same UI as Create page.
            Route::get('/edit/{id}', [DefectMaterialController::class, 'edit'])->name('defect.material.edit');
            Route::put('/edit/{id}/update', [DefectMaterialController::class, 'updateDefectRecord'])->name('defect.material.updateDefectRecord');

            Route::get('/excel_export', [DefectMaterialController::class, 'excel_export'])->name('defect.material.export.csv');
            Route::post('/dump', [DefectMaterialController::class, 'defectRecordDumpData'])->name("defect.material.dump");
            Route::post('/update/record', [DefectMaterialController::class, 'defectRecordDumpUpdate'])->name("defect.material.update.record");
            Route::post('/fetch-record', [DefectMaterialController::class, 'fetchRecord'])->name("defect.material.fetch.record");
            Route::post('/fetch-query-name', [DefectMaterialController::class, 'fetchQueryName'])->name("defect.material.fetch.query.name");
            Route::delete('/delete-temp/{id}', [DefectMaterialController::class, 'deleteTemp'])->name('defect.material.delete');
            Route::get('/store', [DefectMaterialController::class, 'store'])->name('defect.material.store.get');
        });

        // OUTSOURCE - 54 - Fixed
        Route::prefix('defect/process')->controller(ProcessController::class)->group(function () {
            Route::resource('/', ProcessController::class)
                ->except(['show', 'destroy','edit','update']) // Exclude unnecessary methods
                ->names('defect.process');
            Route::get('/get-product-unit-price/{productCode}', [ProcessController::class, 'getProductUnitPrice'])->name('defect.process.getProductUnitPrice');
            Route::post('/store_session', [ProcessController::class, 'store_session'])->name('defect.process.storeData');
            Route::delete('/cancel_session/{tempDataId}', [ProcessController::class, 'cancel_session']);
            Route::put('/update_session/{id}', [ProcessController::class, 'update_session'])->name('defect.process.updateSession');
           
          
            // OUSTOURCE-55
            Route::get('/process-defect-export', [ProcessController::class, 'machiningDefectExport'])->name('process.defect.export');
            // Update table row record via ajax | not using now
            // Route::put('/update-defect-item', [ProcessController::class, 'updateDefectItem'])->name('defect.process.updateDefectItem');

            // Update the record
            Route::get('/edit/{id}', [ProcessController::class, 'edit'])->name('defect.process.edit');
            Route::put('/{id}', [ProcessController::class, 'update'])->name('defect.process.update');
        });
    });

    Route::group(['prefix' => 'shipment', 'as' => 'shipment.'], function () {
        // SHIPMENT - 58 - Fixed
        Route::resource('actual', ActualController::class)
        ->except(['show', 'destroy', 'store']);
        Route::post('actual/shipment-entry', [ActualController::class, 'addShipmentEntry'])->name('actual.add');
        Route::post('actual/store-data', [ActualController::class, 'storeShipmentRecord'])->name('actual.store');
        Route::get('actual/exportExcel', [ActualController::class, 'exportExcel'])->name('actual.exportExcel');
      
        // SHIPMENTINSPECTION - 59
        Route::prefix('summary')->controller(controller: ShipmentSummaryController::class)->group(function () {
            Route::get('/excel_export', [ShipmentSummaryController::class, 'excel_export'])->name('shipmentSummaryExcelExport');
            Route::resource('/', ShipmentSummaryController::class)->only(['index', 'destroy'])->names('shipmentSummary');
        });
    });


    Route::group(['prefix' => 'estimate', 'as' => 'estimate.'], function () {
        // Estimate 182 - Fixed
        Route::prefix('request')->controller(controller: ResponseCreateController::class)->group(function () {
            Route::resource('/', CreateController::class)->except(['show','destroy', 'edit'])->names('requestCreate');
            Route::post('/store_file', [CreateController::class, 'store_file'])->name('requestCreateStoreFile');
        });

        // ESTIMATE - 183
        Route::resource('search', EstimateSearchController::class)->only(['index'])->names('estimateSearch');
        
        // ESTIMATE - 185
        Route::resource('detail', EstimateDetailController::class)->only(['show'])->names('estimateDetail');
        
        // ESTIMATE - 199
        Route::resource('response', ResponseCreateController::class)->only(['store', 'edit', 'update'])->names('estimateResponse');
        Route::prefix('response')->controller(controller: ResponseCreateController::class)->group(function () {
            Route::get('/create/{id}', [ResponseCreateController::class, 'create'])->name('estimateResponseCreate');
            Route::post('/store_temp_file', [ResponseCreateController::class, 'store_temp_file'])->name('estimateResponseStoreTempFile');
            Route::post('/remove_temp_file', [ResponseCreateController::class, 'remove_temp_file'])->name('estimateResponseRemoveTempFile');
            Route::get('/download/{filename}', [ResponseCreateController::class, 'downloadFile'])->name('estimateResponseDownload');
        });
    });

    Route::group(['prefix' => 'sales', 'as' => 'sales.'], function () {
        // SALE-81 発注金額明細表発行
        Route::get('issuance-of-statement-of-order-amount', [SaleController::class, 'issuanceOfStatementOfOrderAmount'])->name('issuanceOfStatementOfOrderAmount');
        Route::post('export', [SaleController::class, 'export'])->name('export');

        //SALE-82 販売計画表
        Route::get('sale-plan-search', [SaleController::class, 'salePlanSearch'])->name('salePlanSearch');

        //SALE-90 販売実績表
        Route::get('sale-performance-table', [SaleController::class, 'salePerformanceSearch'])->name('salePerformanceSearch');
    });

    Route::group(['prefix' => 'monthly', 'as' => 'monthly.'], function () {
        // MONTHLY-124 AI買取データ取込
        Route::get('collect-ai-purchase-data', [MonthyController::class, 'CollectAIPurchaseData']);

        // MONTHLY-125 売上締め処理
        Route::get('sales-closing-process', [MonthyController::class, 'salesClosingProcess']);

        // MONTHLY-126 東陽請求データ取込
        Route::get('toyobilling-data-import', [MonthyController::class, 'toyobillingDataImport']);

        // MONTHLY-127 購入実績アンマッチ一覧
        Route::get('list-unmatched-purchase-results', [MonthyController::class, 'listUnmatchedPurchaseResults'])->name('listUnmatchedPurchaseResults');

        // MONTHLY-128 訂正東陽請求データ出力
        Route::get('toyo-billing-data-output', [MonthyController::class, 'ToyoBillingDataOutput']);

        // MONTHLY-129 購買締め処理
        Route::get('purchasing-closing-process', [MonthyController::class, 'PurchasingClosingProcess']);

        // MONTHLY-130 支払予定検索・一覧
        Route::get('payment-schedule-list', [MonthyController::class, 'paymentScheduleList'])->name('paymentScheduleList');

        // MONTHLY-131 支払予定詳細
        Route::get('payment-schedule-details', [MonthyController::class, 'paymentScheduleDetails'])->name('paymentScheduleDetails');

        // MONTHLY-132 支払条件変更
        Route::get('payment-terms-change/{paymentDetailId}', [MonthyController::class, 'paymentTermsChange'])->name('paymentTermsChange');
        Route::post('payment-terms-change/{paymentDetailId}', [MonthyController::class, 'paymentTermsChangeProcess'])->name('paymentTermsChangeProcess');
        // MONTHLY-133 経理締め処理
        Route::get('accounting-closing', [MonthyController::class, 'accountingClosing']);
    });

    Route::group(['prefix' => 'purchase', 'as' => 'purchase.'], function () {
        // PURCHASE 62, 63, 64 - Fixed
        Route::resource('actual', PurchaseActualController::class)->except(['show','destroy'])->names('purchaseActual');
        Route::prefix('actual')->controller( PurchaseActualController::class)->group(function () {
            // 62
            Route::get('/production/create', [PurchaseActualController::class, 'create'])->name('purchaseProduction');
            Route::get('/production/create/{id}', [PurchaseActualController::class, 'duplicate'])->name('purchaseProduction.duplicate');
            Route::post('/production/production_store', [PurchaseActualController::class, 'store'])->name('purchaseProduction.store');
            Route::get('/production/{id}/edit', [PurchaseActualController::class, 'edit'])->name('purchaseProduction.edit');
            Route::put('/production/{id}', [PurchaseActualController::class, 'update'])->name('purchaseProduction.update');

            // 63
            Route::get('/item/create', [PurchaseActualController::class, 'create'])->name('purchaseItem');
            Route::get('/item/create/{id}', [PurchaseActualController::class, 'duplicate'])->name('purchaseItem.duplicate');
            Route::post('/item/item_store', [PurchaseActualController::class, 'store'])->name('purchaseItem.store');
            Route::get('/item/{id}/edit', [PurchaseActualController::class, 'edit'])->name('purchaseItem.edit');
            Route::put('/item/{id}', [PurchaseActualController::class, 'update'])->name('purchaseItem.update');

            
            Route::get('/excel_export', [PurchaseActualController::class, 'excel_export'])->name('purchaseActual.export');
        });

        Route::get('history/copy_previous_input', [HistoryController::class, 'copy_previous_input'])->name('history.copy_previous_input');
        Route::get('history/duplicate/{id}', [HistoryController::class, 'duplicate'])->name('history.duplicate');
        Route::get('history/excel_export', [HistoryController::class, 'excel_export'])->name('history.export');
        Route::resource('history', HistoryController::class)->except('show');
        
        // PURCHASE 65 & 66 
        Route::resource('supplier', PurchaseSupplierController::class)->only('index', 'show')->names('purchaseAmountSearch');
        Route::get('supplier/search/excel_export', [PurchaseSupplierController::class, 'excel_export'])->name('purchaseAmountSearch.excel_export');
        Route::get('supplier/detail/excel_export', [PurchaseSupplierController::class, 'excel_export_detail'])->name('purchaseSupplierDetail.excel_export');

        // PURCHASE 67 & 68
        Route::get('requisition/copy_previous_input', [RequisitionController::class, 'copy_previous_input'])->name('requisition.copy_previous_input');
        Route::resource('requisition', RequisitionController::class)
        ->only(['index','create', 'store', 'edit', 'update'])
        ->names('requisition');
        Route::get('requisition/export', [RequisitionController::class, 'purchaseRequisitionSearchExport'])->name('purchaseRequisitionSearch.export');

        // PURCHASE 69
        Route::get('approval-route-list', [PurchaseRecordController::class, 'getApprovalRouteList'])->name('getApprovalRouteList');
        Route::delete('approval-route-list/{id}', [PurchaseRecordController::class, 'deleteApprovalRoute'])->name('deleteApprovalRoute');
        Route::delete('approval-route-detail/{id}', [PurchaseRecordController::class, 'deleteApprovalRouteDetail'])->name('deleteApprovalRouteDetail');
        Route::get('approval-route-details/{id?}', [PurchaseRecordController::class, 'getApprovalRouteDetails'])->name('getApprovalRouteDetails');
        Route::get('approval-route-setting', [PurchaseRecordController::class, 'approvalRouteSetting'])->name('approvalRouteSetting');
        Route::post('approval-route-setting-store', [PurchaseRecordController::class, 'approvalRouteSettingStore'])->name('approvalRouteSetting.store');
        Route::post('save-approval-route', [PurchaseRecordController::class, 'saveApprovalRoute'])->name('saveApprovalRoute');
        Route::post('update-approval-route', [PurchaseRecordController::class, 'updateApprovalRoute'])->name('updateApprovalRoute');
        Route::post('reorder-approval-route', [PurchaseRecordController::class, 'reorderApprovalRoute'])->name('reorderApprovalRoute');
        
        // PURCHASE 70
        Route::prefix('approval')->controller(ListController::class)->group(function () {
            Route::resource('/list', ListController::class)->except(['show',])->names('approval.list');
            Route::get('list/excel_export', [ListController::class, 'excel_export'])
                ->name('approval.list.excel_export');
            Route::post('list/requisition_approval', [ListController::class, 'requisition_approval'])->name('approval.list.requisition_approval');
        });
        
        // PURCHASE 71
        Route::prefix('approval')->controller(PurchaseApprovalDetailController::class)->group(function () {
            Route::get('detail/{id}', [PurchaseApprovalDetailController::class, 'showRequisitionApprovalDetails'])->name('detail.showRequisitionApprovalDetails');
            Route::post('detail/purchase-approval-add', [PurchaseApprovalDetailController::class, 'addApprovalUser'])->name('detail.purchaseAddApprovalUser');
            Route::post('detail/purchase-approval-remove', [PurchaseApprovalDetailController::class, 'removeApprovalUser'])->name('detail.purchaseRemoveApprovalUser');
            Route::post('detail/purchase-requisition/approve/{id}', [PurchaseApprovalDetailController::class, 'purchaseRequisitionApprove'])->name('detail.purchaseRequisitionApprove');
            Route::post('detail/purchase-requisition/deny/{id}', [PurchaseApprovalDetailController::class, 'purchaseRequisitionDenied'])->name('detail.purchaseRequisitionDenied');
        });
        
        //Purchase 72
        Route::resource('order/process', PurchaseProcessController::class)->only('index')->names('orderProcess');

        //Purchase 73
        Route::resource('order/process/detail', PurchaseProcessDetailController::class)->names('orderProcessDetail');
        Route::put('order/process/detail/reject/{id}', [PurchaseProcessDetailController::class, 'reject'])->name('orderProcessDetail.reject');
        
        //Purchase 74
        Route::resource('order/confirm', PurchaseConfirmController::class)->only('index','store')->names('orderConfirm');
        Route::get('order/confirm/excel_export', [PurchaseConfirmController::class, 'excel_export'])->name('orderConfirm.excelExport');


        //Purchase  PurchaseReissueController
        Route::resource('order/reissue', PurchaseReissueController::class)->only('index')->names('orderReissue');
        // Route::get('order-form-reissue', [OrderFormController::class, 'orderFormReissue'])->name('order.form.reissue');
        Route::get('order/reissue/excel_export/{id}', [PurchaseReissueController::class, 'excel_export'])->name('orderReissue.excelExport');
        
        // Purchase 77
        Route::resource('order', PurchaseOrderController::class)->except(['create','show','destroy'])->names('order');

        Route::prefix('order')->controller( PurchaseOrderController::class)->group(function () {
            Route::put('/cancel/{id}', [PurchaseOrderController::class, 'cancel'])->name('order.cancel');
            Route::get('/excel_export', [PurchaseOrderController::class, 'excel_export'])->name('order.excel_export');
        });

        // PURCHASE 79
        Route::resource('receipt', ReceiptController::class)->only(['edit','store','destroy'])->names('receipt');
      
        // PURCHASE 80
        Route::resource('acceptance', AcceptanceController::class)->only(['edit', 'update', 'store', 'destroy'])->names('acceptance');

        Route::get("order-processing", [PurchaseRecordController::class, 'orderProcessing'])->name("order.processing");
        Route::get("order-processing/export", [PurchaseRecordController::class, 'orderProcessingExport'])->name("order.processing.export");
        Route::get("order-processing/{purchase_requisition}", [PurchaseRecordController::class, 'orderProcessingDetail'])->name("order.processing.detail");
        Route::put("order-processing/{purchase_requisition}", [PurchaseRecordController::class, 'orderProcessingUpdate'])->name("order.processing.update");
        Route::delete("order-processing/{purchase_requisition}", [PurchaseRecordController::class, 'orderProcessingDelete'])->name("order.processing.delete");
    });

    Route::group(['prefix' => 'facility', 'as' => 'facility.'], function () {
        Route::get('individual-monthly-report', [FacilityController::class, 'individualMonthlyReport'])->name('work.list');
        Route::get('work', [FacilityController::class, 'workDetail'])->name('work.input.detail');
        Route::post('work/item-store', [FacilityController::class, 'storeItemWorkDetail'])->name('work.input.store');
        Route::post('work/bulk-store', [FacilityController::class, 'bulkStoreWorkDetails'])->name('work.bulk.store');
    });

    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        // MASTER 141 & 142
        Route::resource('supplier', SupplierController::class)
            ->except(['show', 'destroy']);
        Route::get('supplier/excel_export', [SupplierController::class, 'excel_export'])
            ->name('supplier.excel_export');

        // MASTER - 143, 144
        Route::resource('line', MasterLineController::class)->except(methods: ['show', 'destroy'])->names('masterLine');
        Route::prefix('line')->controller(MasterLineController::class)->group(function () {
            Route::get('/export', [MasterLineController::class, 'excel_export'])->name('masterLine.export');
            Route::get('/check_line_code', [MasterLineController::class, 'check_line_code'])->name('masterLine.checkLineCode');
            Route::get('/copy_previous_input', [MasterLineController::class, 'copy_previous_input'])->name('masterLine.copy');
            Route::post('/delete/{id}', [MasterLineController::class, 'delete'])->name('masterLine.delete');
        }); 
            
        // MASTER 145
        Route::resource('part', PartController::class)
            ->except(['show']);
        Route::get('part/excel_export', [PartController::class, 'excel_export'])->name('part.excel_export');

        // MASTER-146 品番マスタ登録・編集
        // MASTER-146 工程順序設定 MODAL
        Route::resource('process-sequence-setting', ProcessSequenceModal::class)->only( ['store','update', 'destroy'])->names('processSequence');
        Route::prefix('process-sequence-setting')->controller(ProcessSequenceModal::class)->group(function () {
            Route::get('/get-data', [ProcessSequenceModal::class, 'getProcessOrder'])->name('processSequence.getProcessOrder');
            Route::post('/save-session', [ProcessSequenceModal::class, 'saveSession'])->name('processSequence.saveSession');
            Route::post('/delete-row', [ProcessSequenceModal::class, 'deleteSessionRow'])->name('processSequence.deleteSessionRow');
            Route::post('/order', [ProcessSequenceModal::class, 'changeOrderRow'])->name('processSequence.changeOrderRow');
        });

        // MASTER-146 工程単価設定 MODAL
        Route::resource('process-unit-pirce-setting', ProcessUnitPriceModal::class)->only( ['store','update', 'destroy'])->names('processUnitPrice');
        Route::prefix('process-unit-pirce-setting')->controller(ProcessUnitPriceModal::class)->group(function () {
            Route::get('/get-data', [ProcessUnitPriceModal::class, 'getProcessSetting'])->name('processUnitPrice.getProcessSetting');
            Route::post('/save-session', [ProcessUnitPriceModal::class, 'saveSession'])->name('processUnitPrice.saveSession');
            Route::post('/delete-row', [ProcessUnitPriceModal::class, 'deleteSessionRow'])->name('processUnitPrice.deleteSessionRow');
        });

        // MASTER-146 品番単価設定 MODAL
        Route::resource('part-number-unit-pirce-setting', PartNumberUnitPriceController::class)->only( ['store', 'update','destroy'])->names('partNumberUnitPrice');
        Route::prefix('part-number-unit-pirce-setting')->controller(PartNumberUnitPriceController::class)->group(function () {
            Route::get('/get-data', [PartNumberUnitPriceController::class, 'getPartNumberUnitPriceSetting'])->name('partNumberUnitPrice.getProcessSetting');
            Route::post('/save-session', [PartNumberUnitPriceController::class, 'saveSession'])->name('partNumberUnitPrice.saveSession');
            Route::post('/delete-row', [PartNumberUnitPriceController::class, 'deleteSessionRow'])->name('partNumberUnitPrice.deleteSessionRow');
        });

        // MASTER 153, 154, 155
        Route::resource('kanban', MasterKanbanController::class)
            ->except(['show']);
        Route::get('kanban/excel_export', [MasterKanbanController::class, 'excel_export'])
            ->name('kanban.excel_export');
        Route::get('kanban/get_previous_input', [MasterKanbanController::class, 'get_previous_input'])->name('kanban.get_previous_input');

        // MASTER 156 & 157
        Route::resource('project', MasterProjectController::class)
            ->except(['show']);
        Route::get('project/excel_export', [MasterProjectController::class, 'excel_export'])
            ->name('project.excel_export');

        // MASTER 160, 161
        Route::resource('employee', EmployeeController::class)
            ->except(['show']);
        Route::get('master/excel_export', [EmployeeController::class, 'excel_export'])
            ->name('employee.excel_export');

        // MASTER 163
        Route::resource('calendar', CalendarController::class)
            ->except(['show']);
        Route::post('calendar/check-exists', [CalendarController::class, 'checkExists'])->name('calendar.checkExists');
        Route::post('calendar/calendar-operations', [CalendarController::class, 'calendarOperations'])->name('calendar.calendar-operations');
      
        Route::get('products/create', [MasterController::class, 'productCreate'])->name('products.create');
        Route::post('products/create', [MasterController::class, 'productStore'])->name('products.store');
        Route::get('products/{id}/edit', [MasterController::class, 'productEdit'])->name('products.edit');
        Route::post('products/{id}/edit', [MasterController::class, 'productUpdate'])->name('products.update');
        Route::post('products/{id}/delete', [MasterController::class, 'productDelete'])->name('products.delete');
        Route::post('products/{id}/hard-delete', [MasterController::class, 'productHardDelete'])->name('products.productHardDelete');
        Route::get('products/export', [MasterController::class, 'exportCSV'])->name('products.exportCsv');
        Route::post('products/create/duplicate', [MasterController::class, 'productDuplicate'])->name('products.duplicate');
        // Production Simulation download
        Route::get('products/download', [MasterController::class, 'productionSimulation'])->name('products.productionSimulation');
      
        Route::get('customers', [MasterController::class, 'customers'])->name('index');
        Route::get('customer/{id?}', [MasterController::class, 'customerCreateOrUpdate'])->name('customer.createOrUpdate');
        Route::post('customer', [MasterController::class, 'store'])->name('customer.store');
        Route::get('customer-search', [MasterController::class, 'customerSearch'])->name('customers.searchQuery');
        Route::post('customers/export', [MasterController::class, 'dowloadCSV'])->name('customers.dowloadCSV');
        Route::post('customer/{id}', [MasterController::class, 'update'])->name('customer.update');
        Route::post('customer/{id}/delete', [MasterController::class, 'customerDelete'])->name('customer.delete');
        Route::get('customers/duplicate', [MasterController::class, 'loadSessionForm'])->name('customer.duplicate');

        Route::get('lines', [MasterController::class, 'lines'])->name('lines.index');
        Route::get('line/{id?}', [MasterController::class, 'linesCreateOrUpdate'])->name('lines.createOrUpdate');
        Route::post('lines', [MasterController::class, 'linesStore'])->name('lines.store');
        Route::get('lines/search', [MasterController::class, 'lineSearch'])->name('lines.search');
        Route::post('lines/export', [MasterController::class, 'lineDowloadCSV'])->name('lines.dowloadCSV');
        Route::post('lines/{id}', [MasterController::class, 'linesUpdate'])->name('lines.update');
        Route::post('line/{id}/delete', [MasterController::class, 'lineDelete'])->name('line.delete');
        Route::get('lines/duplicate', [MasterController::class, 'loadSessionFormLine'])->name('line.duplicate');

        Route::get('departments', [MasterController::class, 'departments'])->name('departments.index');
        Route::get('department/search', [MasterController::class, 'departmentSearch'])->name('departments.search');
        Route::get('department/{id?}', [MasterController::class, 'departmentCreateOrUpdate'])->name('department.createOrUpdate');
        Route::post('department/{id}', [MasterController::class, 'departmentUpdate'])->name('department.update');
        Route::post('department', [MasterController::class, 'departmentStore'])->name('department.store');
        Route::post('departments/export', [MasterController::class, 'departmentDowloadCSV'])->name('departments.dowloadCSV');
        Route::post('department/{id}/delete', [MasterController::class, 'departmentDelete'])->name('department.delete');
        Route::get('departments/duplicate', [MasterController::class, 'loadSessionFormDepartment'])->name('department.duplicate');

      
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('employees/create', [EmployeeController::class, 'employeeCreate'])->name('employees.create');
        Route::post('employees/create', [EmployeeController::class, 'employeeStore'])->name('employees.store');
        Route::get('employees/{id}/edit', [EmployeeController::class, 'employeeEdit'])->name('employees.edit');
        Route::post('employees/{id}/edit', [EmployeeController::class, 'employeeUpdate'])->name('employees.update');
        Route::post('employees/export', [EmployeeController::class, 'exportCSV'])->name('employees.exportCsv');
        Route::get('employees-search', [EmployeeController::class, 'employeeSearch'])->name('employees.employeeSearch');

        // MASTER-158
        // MASTER-159 機番マスタ登録・編集
        Route::resource('machine', MachineController::class)->except( ['show'])->names('masterMachine');
        Route::prefix('machine')->controller(MachineController::class)->group(function () {
            Route::get('/export', [MachineController::class, 'excel_export'])->name('masterMachine.export');
            Route::get('/duplicate', [MachineController::class, 'duplicate'])->name('masterMachineNumberDuplicate');
            Route::get('/check-machine-number', [MachineController::class, 'checkMachineNumber'])->name('masterMachineNumberCheckMachineNumber');
        });


        Route::get('machine-numbers', [MachineNumberController::class, 'index'])->name('machineNumbers.index');
        Route::post('machine-numbers/export', [MachineNumberController::class, 'exportCSV'])->name('machineNumbers.exportCsv');
        Route::get('machine-numbers-search', [MachineNumberController::class, 'machineNumberSearch'])->name('machineNumbers.machineNumberSearch');
        Route::get('machine-numbers-search-code/{code}', [MachineNumberController::class, 'machineNumberSearchByMachineNumber'])->name('machineNumbers.machineNumberSearchByMachineNumber');


        Route::get('processes', [MasterController::class, 'processes'])->name('processes.index');
        Route::get('process/{id?}', [MasterController::class, 'processCreateOrUpdate'])->name('process.createOrUpdate');
        Route::post('process', [MasterController::class, 'processStore'])->name('process.store');
        Route::post('process/{id}', [MasterController::class, 'processUpdate'])->name('process.update');
        Route::get('process-search', [MasterController::class, 'processSearch'])->name('process.search');
        Route::post('processes/export', [MasterController::class, 'processDowloadCSV'])->name('processes.dowloadCSV');
        Route::post('process/{id}/delete', [MasterController::class, 'processDelete'])->name('process.delete');
        Route::get('processes/duplicate', [MasterController::class, 'loadSessionFormProcess'])->name('process.duplicate');

        Route::post('configuration/create', [MasterController::class, 'configurationCreate'])->name('configuration.post');
        Route::post('configuration/edit', [MasterController::class, 'configurationEdit'])->name('configuration.editOrDelete');
        Route::post('configuration/delete', [MasterController::class, 'configurationSoftDelete'])->name('configuration.softDelete');

        Route::get('/product-material-hierarchy/{partNumber}', [MasterController::class, 'productMaterialHierarchy'])->name('productMaterialHierarchy');
    });

    Route::post("search", [SearchController::class, 'search'])->name("search");

    Route::get("attachment/{attachment}/download", [AttachmentController::class, 'download'])->name("attachment.download");
    Route::get("estimate/{estimate}/answer", [EstimateReplyDetailController::class, 'create'])->name("estimate.reply.create");
    Route::post("estimate/{estimate}/answer", [EstimateReplyDetailController::class, 'store'])->name("estimate.reply.store");
    Route::post("estimate/filtered", [EstimateController::class, 'filtered'])->name("estimate.index.filtered");
    
    // remove this once done migrating to new route
    Route::resource("estimate", EstimateController::class)->except('show');
    
    Route::resource("temporary-upload", TemporaryUploadController::class);

    Route::prefix('admin')->group(function () {
        Route::get('facility-management-master/list', [MstLineController::class, 'linesIndex'])->name('lines.index');
        Route::post('facility-management-master', [MstLineController::class, 'linesStore'])->name('lines.store');
        Route::get('facility-management-master/create', [MstMachineController::class, 'machinesCreate'])
            ->name('machines.create');
        Route::post('facility-management-master/create', [MstMachineController::class, 'machinesStore'])
            ->name('machines.store');
        Route::get('inspection-item-basic-set/list', [InspectionItemController::class, 'inspectionItemIndex'])
            ->name('inspection-item.index');
        Route::post('inspection-item-basic-set/list', [InspectionItemController::class, 'inspectionItemList'])
            ->name('inspection-item.list');
        Route::post('inspection-item-basic-set', [InspectionItemController::class, 'inspectionItemStore'])
            ->name('inspection-item.store');

        Route::get('equipment-inspection/list', [EquipmentInspectionController::class, 'equipmentInspectionIndex'])
            ->name('equipment-inspection.index');
        Route::post('equipment-inspection/list', [EquipmentInspectionController::class, 'equipmentInspectionList'])
            ->name('equipment-inspection.list');
        Route::get('equipment-inspection/create', [EquipmentInspectionController::class, 'equipmentInspectionCreate'])
            ->name('equipment-inspection.create');
        Route::post('equipment-inspection', [EquipmentInspectionController::class, 'equipmentInspectionStore'])
            ->name('equipment-inspection.store');
        Route::get('equipment-inspection/{id}/edit', [EquipmentInspectionController::class, 'equipmentInspectionEdit'])
            ->name('equipment-inspection.edit');
        // Route::get('equipment-inspection/edit', [EquipmentInspectionController::class, 'equipmentInspectionEditIndex']);
        Route::get('equipment-inspection/{id}/tablet', [EquipmentInspectionController::class, 'equipmentInspectionTablet'])
            ->name('equipment-inspection.tablet');
        // Route::get('equipment-inspection/tablet', [EquipmentInspectionController::class, 'equipmentInspectionTabletIndex']);
        // Route::get('daily-production-control-table/{id}/edit', [DailyProductionControlController::class, 'dailyReport'])
        // 	->name('daily.report');
        Route::get('daily-production-control-table/edit', [DailyProductionControlController::class, 'dailyReportEdit'])
            ->name('daily.edit');
        Route::post('daily-production-control-table/list', [DailyProductionControlController::class, 'dailyReportList'])
            ->name('daily.list');
        Route::post('daily-production-control-table', [DailyProductionControlController::class, 'dailyReportStore'])
            ->name('daily.store');
        Route::get('daily-production-control-table/{id}/reference', [DailyProductionControlController::class, 'dailyReference'])
            ->name('daily.reference');
        Route::get('daily-production-control-table/reference', [DailyProductionControlController::class, 'dailyReferenceList'])
            ->name('daily.reference.list');
        Route::get('daily-production-control-table/reference/search', [DailyProductionControlController::class, 'dailyProdSearch'])->name('daily.reference.search');
        // Route::get('daily-production-control-table/reference', [DailyProductionControlController::class, 'dailyReference7']);
        // Route::put('equipment-inspection/{id}', [EquipmentInspectionController::class, 'equipmentInspectionUpdate'])
            // ->name('equipment-inspection.update');
        // Route::delete('equipment-inspection/{id}', [EquipmentInspectionController::class, 'equipmentInspectionDestroy'])->name('equipment-inspection.destroy');
    });

    Route::group(['prefix' => 'cost', 'as' => 'cost.'], function () {

        // COSTPRICE-134
        Route::get('list', [CostPriceController::class, 'index'])->name('index');
        Route::get('list-search', [CostPriceController::class, 'listSearch'])->name('listSearch');
        Route::post('list/export', [CostPriceController::class, 'listExportCSV'])->name('listExportCSV');
        Route::get('list-ajax', [CostPriceController::class, 'listAjax'])->name('listAjax');

        // COSTPRICE-135
        Route::get('purchase-breakdown', [CostPriceController::class, 'purchaseBreakdown'])->name('purchaseBreakdown');
        Route::get('purchase-breakdown-search', [CostPriceController::class, 'purchaseBreakdownSearch'])->name('purchaseBreakdownSearch');
        Route::post('purchase-breakdown/export', [CostPriceController::class, 'purchaseBreakdownExportCSV'])->name('purchaseBreakdownExportCSV');

        // COSTPRICE-136
        Route::get('purchase-data', [CostPriceController::class, 'purchaseData'])->name('purchaseData');
        Route::get('purchase-data-search', [CostPriceController::class, 'purchaseDataSearch'])->name('purchaseDataSearch');
        Route::post('purchase-data/export', [CostPriceController::class, 'purchaseDataExportCSV'])->name('purchaseDataExportCSV');
    });

});