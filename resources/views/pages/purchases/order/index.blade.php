@extends('layouts.app')
@section('title', '発注データ一覧')

@push('styles')
    @vite('resources/css/purchase/order_data_list.css')
    @vite('resources/css/index.css')
@endpush
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>発注データ一覧</span>
                </h1>
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38; ">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <div>
                <div id="mainDiv">
                    <form accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" id="orderSearchList" 
                    data-disregard-empty="true">
                        <div class="d-flex">
                            <div class="flex-1 block">
                                <div class="mb-3 formset">
                                    <label for="#">発注日</label>
                                    <div class="d-flex">
                                        @include('partials._date_picker', ['inputName' => 'order_date_start', 'value' => $requestData["order_date_start"] ?? '', 'required' => false, 'attributes' => 'data-error-messsage-container=.err_msg_order'])
                                        <p class="formPack">～</p>
                                        @include('partials._date_picker', ['inputName' => 'order_date_end',  'value' => $requestData["order_date_end"] ?? '', 'required' => false, 'attributes' => 'data-error-messsage-container=.err_msg_order'])
                                    </div>
                                    <div class="err_msg_order"></div>
                                </div>
            
                                <div class="mb-3 formset">
                                    <label for="#">入荷日</label>
                                    <div class="d-flex">
                                        @include('partials._date_picker', ['inputName' => 'arrival_date_start', 'required' => false, 'value' =>$requestData["arrival_date_start"] ?? '', 'attributes' => 'data-error-messsage-container=.err_msg_arrival'])
                                        <p class="formPack">～</p>
                                        @include('partials._date_picker', ['inputName' => 'arrival_date_end', 'required' => false, 'value' => $requestData["arrival_date_end"] ?? '', 'attributes' => 'data-error-messsage-container=.err_msg_arrival'])
                                    </div>
                                    <div class="err_msg_arrival"></div>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">納期</label>
                                    <div class="d-flex">
                                        @include('partials._date_picker', ['inputName' => 'deadline_date_start', 'required' => false, 'value' => $requestData["deadline_date_start"] ?? '', 'attributes' => 'data-error-messsage-container=.err_msg_deadline'])
                                        <p class="formPack">～</p>
                                        @include('partials._date_picker', ['inputName' => 'deadline_date_end', 'required' => false, 'value' => $requestData["deadline_date_end"] ?? '', 'attributes' => 'data-error-messsage-container=.err_msg_deadline'])
                                    </div>
                                    <div class="err_msg_deadline"></div>
                                </div>
            
                                <div class="mb-3 formset">
                                    <label for="#">機番</label>
                                    <div class="d-flex">
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="machine_code_start"
                                            value="{{ $requestData['machine_code_start'] ?? '' }}"
                                            data-error-messsage-container = "#machine_code-error"
                                            data-validate-exist-model="machine_number" 
                                            data-validate-exist-column="machine_number" 
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="5"
                                            id="machine_code_start" class="">
                                        </p>
                                        <p class="formPack">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchMachineModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                        <p class="formPack">～</p>
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="machine_code_end"
                                            data-error-messsage-container = "#machine_code-error"
                                            data-validate-exist-model="machine_number" 
                                            data-validate-exist-column="machine_number" 
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="5"
                                            value="{{ $requestData['machine_code_end'] ?? '' }}" id="machine_code_end" class="">
                                        </p>
                                        <p class="formPack">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchMachine2Modal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                    </div>
                                    <div id="machine_code-error"></div>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">ライン</label>
                                    <div class="d-flex">
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="line_code_start"
                                            data-validate-exist-model="line" 
                                            data-validate-exist-column="line_code" 
                                            data-error-messsage-container = "#line_code-error"
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="3"
                                            value="{{ $requestData['line_code_start']  ?? ''}}" 
                                            id="line_code_start" class="">
                                        </p>
                                        <p class="">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchLineModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                        <p class="formPack">～</p>
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="line_code_end"
                                            data-validate-exist-model="line" 
                                            data-validate-exist-column="line_code" 
                                            data-error-messsage-container = "#line_code-error"
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="3"
                                            value="{{ $requestData['line_code_end'] ?? '' }}" id="line_code_end" class="">
                                        </p>
                                        <p class="formPack fixedWidth ">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchLine2Modal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                    </div>
                                    <div id="line_code-error"></div>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">品番</label>
                                    <div class="d-flex">
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="part_number"  
                                            value="{{ $requestData['part_number'] ?? '' }}" 
                                            data-validate-exist-model="product_number" 
                                            data-validate-exist-column="part_number" 
                                            data-inputautosearch-model="product_number" 
                                            data-inputautosearch-column="part_number" 
                                            data-inputautosearch-return="product_name" 
                                            data-inputautosearch-reference="part_number_name" 
                                            maxlength="70"
                                            id="part_number" class="">
                                        </p>
                                        <p class="formPack fixedWidth fpfw50p box-middle-name">
                                            <input type="text" readonly
                                                name="part_number_name"
                                                id="part_number_name"
                                                value="{{ $requestData['part_number_name'] ?? '' }}"
                                                class="middle-name">
                                        </p>
                                        <p class="formPack">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchPartNumberModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                    </div>
                                    <div data-error-container = 'part_number' style=""></div>
                                </div>

                                <!-- -->
                                <!-- -->

                                <div class="mb-3 formset">
                                    <label for="#">規格</label>
                                    <p class="formPack fixedWidth fullwidth">
                                        <input type="text" name="standard" value="{{ $requestData["standard"] ?? '' }}">
                                    </p>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">注文書No.</label>
                                    <p class="formPack fixedWidth fullwidth">
                                        <input type="text" name="purchase_order_number" value="{{ $requestData["purchase_order_number"] ?? '' }}">
                                    </p>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">伝票No.</label>
                                    <div>
                                        <p class="formPack fixedWidth fullwidth">
                                            <input type="text" name="slip_no" value="{{ $requestData["slip_no"] ?? '' }}" value="" class="">
                                        </p>
                                    </div>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                            <div class="flex-1 block" style="width: px;">
                                <div class="mb-3 formset">
                                    <label for="#">発注先</label>
                                    <div class="d-flex">
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="supplier_code"  
                                            value="{{ $requestData['supplier_code'] ?? '' }}"
                                            data-validate-exist-model="supplier" 
                                            data-validate-exist-column="customer_code" 
                                            data-inputautosearch-model="supplier" 
                                            data-inputautosearch-column="customer_code" 
                                            data-inputautosearch-return="supplier_name_abbreviation" 
                                            data-inputautosearch-reference="supplier_name" 
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="6"
                                            style="width: 100px"
                                            id="supplier_code" class="">
                                        </p>
                                        <p class="formPack fixedWidth fpfw50p box-middle-name">
                                            <input type="text" readonly
                                                name="supplier_name"
                                                id="supplier_name"
                                                value="{{ $requestData['supplier_name'] ?? '' }}"
                                                class="middle-name">
                                        </p>
                                        <p class="formPack">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchSupplierModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                    </div>
                                    <div data-error-container='supplier_code'></div>
                                </div>
            
                                <div class="mb-3 formset">
                                    <label for="#">部門</label>
                                    <div class="d-flex">
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="department_code_start"  
                                            value="{{ $requestData['department_code_start'] ?? ''}}" 
                                            data-validate-exist-model="department" 
                                            data-validate-exist-column="code" 
                                            data-error-messsage-container = "#department_code-error"
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" 
                                            maxlength="6"
                                            style="width: 100px"
                                            id="department_code_start" class="">
                                        </p>
                                        <p class="formPack">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchDepartmentModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                        <p class="formPack">～</p>
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="department_code_end"
                                            value="{{ $requestData['department_code_end'] ?? '' }}" 
                                            data-validate-exist-model="department" 
                                            data-validate-exist-column="code" 
                                            data-error-messsage-container = "#department_code-error"
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="6"
                                            style="width: 100px"
                                            value="{{ $requestData['department_code_end'] ?? '' }}" 
                                            id="department_code_end" class="">
                                        </p>
                                        <p class="">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchDepartment2Modal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                    </div>
                                    <div id="department_code-error"></div>
                                </div>
            
                                <div class="mb-3 formset">
                                    <label for="#">依頼者</label>
                                    <div class="d-flex">
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="employee_code"
                                            data-error-messsage-container = "#employee_code-error"
                                            data-validate-exist-model="employee" 
                                            data-validate-exist-column="employee_code" 
                                            data-inputautosearch-model="employee" 
                                            data-inputautosearch-column="employee_code" 
                                            data-inputautosearch-return="employee_name" 
                                            data-inputautosearch-reference="employee_name" 
                                            maxlength="4"
                                            style="width: 100px"
                                            value="{{ $requestData['employee_code'] ?? '' }}" id="employee_code" class="">
                                        </p>
                                        <p class="formPack fixedWidth fpfw50p box-middle-name">
                                            <input type="text" readonly
                                                name="employee_name"
                                                id="employee_name"
                                                value="{{ $requestData['employee_name'] ?? '' }}"
                                                class="middle-name">
                                        </p>
                                        <p class="formPack fixedWidth">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchEmployeeModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                    </div>
                                    <div id="employee_code-error"></div>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">品名</label>
                                    <p class="formPack fixedWidth fullwidth" style="width: 400px;">
                                        <input type="text" name="requistion_product_name" value="{{ $requestData["requistion_product_name"] ?? '' }}" id="product_name">
                                    </p>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">費目</label>
                                    <div class="d-flex">
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="expense_item_start"
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="3"
                                            style="width: 100px"
                                            data-error-messsage-container = "#expense_item-error"
                                            data-validate-exist-model="item" 
                                            data-validate-exist-column="expense_item" 
                                            value="{{ $requestData['expense_item_start'] ?? '' }}" 
                                            id="expense_item_start" class="">
                                        </p>
                                        <p class="">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchItemModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                        <p class="formPack">～</p>
                                        <p class="formPack fixedWidth ">
                                            <input type="text" name="expense_item_end"
                                            pattern="[0-9]*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                            maxlength="3"
                                            style="width: 100px"
                                            data-error-messsage-container = "#expense_item-error"
                                            data-validate-exist-model="item" 
                                            data-validate-exist-column="expense_item"   
                                            value="{{ $requestData['expense_item_end'] ?? '' }}" 
                                            id="expense_item_end" class="">
                                        </p>
                                        <p class="formPack fixedWidth">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchItem2Modal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                    </div>
                                    <div id="expense_item-error"></div>
                                </div>
            
                                <div class="mb-3 formset">
                                    <label for="#">購買依頼No.</label>
                                    <div>
                                        <p class="formPack fixedWidth fullwidth" style="width: 400px;">
                                            <input type="text" name="requisition_number" value="{{ $requestData["requisition_number"] ?? '' }}">
                                        </p>
                                    </div>
                                </div>
            
                                <div class="mb-3 formset">
                                    <label for="">入荷状況</label>
                                    <div class="d-flex">
                                        <label class='mr-1'>
                                            <input type="radio" name="status" class="ExcludeFromClear" value="all" {{ ($requestData['status'] ?? '') === 'all' ? 'checked' : 'checked' }}> すべて
                                        </label>
                                        <label class='mr-1'>
                                            <input type="radio" name="status" value="non-stock" {{ ($requestData['status'] ?? '') === 'non-stock' ? 'checked' : '' }}> 未入荷
                                        </label>
                                        <label class='mr-1'>
                                            <input type="radio" name="status" value="in-stock" {{ ($requestData['status'] ?? '') === 'in-stock' ? 'checked' : '' }}> 一部入荷
                                        </label>
                                        <label class='mr-1'>
                                            <input type="radio" name="status" value="arrive-stock" {{ ($requestData['status'] ?? '') === 'arrive-stock' ? 'checked' : '' }}> 入荷済
                                        </label>

                                    </div>
                                    <div class="error_msg"></div>
                                </div>

                                <div class="mb-3 formset">
                                    <label for="#">購買受入</label>
                                    <div class="d-flex">
                                        <label class='mr-1'>
                                            <input type="radio" name="acceptance" class="ExcludeFromClear" value="all" {{ ($requestData['acceptance'] ?? '') === 'all' ? 'checked' : 'checked' }}> すべて
                                        </label>
                                        <label class='mr-1'>
                                            <input type="radio" name="acceptance" value="incomplete" {{ ($requestData['acceptance'] ?? '') === 'incomplete' ? 'checked' : '' }}> 未完了
                                        </label>
                                        <label class='mr-1'>
                                            <input type="radio" name="acceptance" value="complete" {{ ($requestData['acceptance'] ?? '') === 'complete' ? 'checked' : '' }}> 完了
                                        </label>

                                    </div>
                                    <div class="error_msg"></div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <button type="button" 
                                class="btn btn-sm btn-primary"
                                data-clear-inputs
                                data-clear-form-target="#orderSearchList"
                                >検索条件をクリア</button>
                            <button class="btn btn-primary btn-sm" role="button" type="submit">検索</button>
                        </div>
                    </form>
                    @if (!empty($items))
                        <a href="{{ route("purchase.order.excel_export", Request::all()) }}" 
                        class="float-right btn btn-success mb-3 {{ $items->total() == 0 ? 'btn-disabled' : '' }}" 
                        style="margin-top: -52px;"
                        >検索結果をEXCEL出力</a>
                    @endif
                </div>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    @if($items && $items->total() > 0)
                        {{ $items->total() }}件中、{{ $items->firstItem() }}件～{{ $items->lastItem() }} 件を表示しています
                    @endif
                </ul>
                <table class="tableBasic list-table bordered">
                    <tbody>
                    <tr>
                        <th>部門</th>
                        <th>発注先</th>
                        <th>数量</th>
                        <th>単位</th>
                        <th width="100px;">発注日</th>

                        <th rowspan="2" width="90px;">入荷</th>
                        <th rowspan="2" width="90px;">購買 受入</th>
                        <th rowspan="2" width="100px;">依頼者</th>
                        <th rowspan="2">購買依頼No.</th>
                        <th rowspan="2">注文書No.</th>
                    </tr>
                    <tr>
                        <th>ライン</th>
                        <th>品番・品名・規格</th>
                        <th>単価</th>
                        <th>金額</th>
                        <th>納期</th>
                    </tr>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td class="tA-le">{{ $item->department?->name }}</td>
                                <td class="tA-le">{{ $item->supplier?->customer_name }}</td>
                                <td class="tA-ri" style="text-align:center">{{ $item->quantity }}</td>
                                <td class="tA-le" style="text-align:center">{{ $item->unit?->name }}</td>
                                <td class="tA-cn">{{ $item->order_date?->format('Y-m-d') }}</td>
                                <td class="tA-cn" rowspan="2">
                                    <a href="{{ route("purchase.receipt.edit", array_merge([$item->id], ['order_number' => $item->purchase_order_number, 
                                            'order_details_number' => $item->purchase_order_details_number])) }}" class="buttonBasic bColor-ok">編集</a>
                                </td>
                                <td class="tA-cn" rowspan="2">
                                    <a href="{{ route("purchase.acceptance.edit", array_merge([$item->id], ['order_number' => $item->purchase_order_number, 
                                        'order_details_number' => $item->purchase_order_details_number])) }}" class="buttonBasic bColor-ok">編集</a>
                                </td>
                                <td class="tA-le" rowspan="2" style="text-align:center">{{ $item->employee?->employee_name }}</td>
                                <td class="tA-le" rowspan="2" style="text-align: center">{{ $item->requisition_number }}</td>
                                <td class="tA-le" rowspan="2" style="text-align: center">{{ $item->purchase_order_number }}</td>
                            </tr>
                            <tr>
                                <td class="tA-le">{{ $item->line?->line_name }}</td>
                                <td class="tA-le">
                                    <a href="{{ route('purchase.order.edit', $item->id) }}">
                                        {{ $item->part_number }}・{{ $item->product_name }}・{{ $item->standard }}
                                    </a>
                                </td>
                                <td class="tA-ri" style="text-align:center">{{ $item->unit_price }}</td>
                                <td class="tA-ri" style="text-align:center">{{ number_format($item->amount_of_money) }}</td>
                                <td class="tA-cn">{{ $item->deadline?->format('Y-m-d') }}</td>
    
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="tA-cn">
                                    検索結果はありません
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                        <tfoot style="border: none; background-color: lightblue;">
                            <tr>
                                <td class="tr-no-border bg-white"></td>
                                <td class="tr-no-border bg-white"></td>
                                <td class="text-center">合計</td>
                                <td class="tA-ri">{{ number_format($amount_of_money) }}</td>
                            </tr>
                        </tfoot>
                    </tbody>
                </table>
                @if ($items)
                {{ $items->appends(request()->all())->links() }}
                @endif
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number',
        'resultNameElementId' => 'part_number_name',
        'model' => 'ProductNumber'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_start',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartment2Modal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_end',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_start',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLine2Modal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_end',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchMachineModal',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_code_start',
        'resultNameElementId' => 'machine_name',
        'model' => 'MachineNumber'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchMachine2Modal',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_code_end',
        'resultNameElementId' => 'machine_name',
        'model' => 'MachineNumber'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '依頼者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_start',
        'resultNameElementId' => 'Item_name',
        'model' => 'Item'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItem2Modal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_end',
        'resultNameElementId' => 'Item_name',
        'model' => 'Item'
    ])
@endsection

