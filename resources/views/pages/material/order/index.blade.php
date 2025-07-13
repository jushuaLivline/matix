@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    {{-- @vite('resources/css/materials/ordering_data.css') --}}
@endpush

@section('title', '発注データ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                発注データ一覧
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form class="overlayedSubmitForm with-js-validation" data-disregard-empty="true" id="form_request">
                    <div class="box mb-3">
                        <div class="mb-4 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">材料メーカー</label>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-half">
                                        <input type="text" id="manufacturer_code" name="manufacturer_code"
                                            data-field-name="材料メーカー"
                                            data-error-messsage-container="#customer-error"
                                            data-validate-exist-model="Customer" data-validate-exist-column="customer_code"
                                            data-inputautosearch-model="Customer"
                                            data-inputautosearch-column="customer_code"
                                            data-inputautosearch-return="customer_name"
                                            data-inputautosearch-reference="manufacturer_name" maxlength="6"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            value="{{ old('manufacturer_code', Request::get('manufacturer_code') ?? '') }}"
                                            style="width: 100px;">
                                    </p>
                                    <p class="formPack fixedWidth fpfw50 box-middle-name mr-half">
                                        <input type="text" readonly id="manufacturer_name" name="manufacturer_name"
                                            value="{{ old('manufacturer_name', Request::get('manufacturer_name') ?? '') }}"
                                            class="middle-name" style="width: 230px">
                                    </p>
                                    <div class="formPack fixedWidth fpfw25p">
                                        <buttoSupplier type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchManufacturerModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </buttoSupplier>
                                    </div>
                                    <div class="error_msg"></div>
                                  
                                </div>
                                <div id="customer-error"></div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">発注日</label>
                                <div class="d-flex" style="width: 360px">
                                    @include('partials._date_picker', [
                                        'inputName' => 'arrival_day_from',
                                        //'value' => Request::get('arrival_day_from', date('Ym01')),
                                        'value' => request('arrival_day_from') ?: '',
                                        'attributes' => 'data-error-messsage-container=#order_date_error_message', 
                                        'dateFormat' => 'YYYYMMDD', 
                                    ])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', [
                                        'inputName' => 'arrival_day_to',
                                        //'value' => Request::get('arrival_day_to', date('Ymt')),
                                        'value' => request('arrival_day_to') ?: '',
                                        'attributes' => 'data-error-messsage-container=#order_date_error_message', 
                                        'dateFormat' => 'YYYYMMDD', 
                                    ])
                                </div>
                                <div id ="order_date_error_message"></div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">指示日</label>
                                <div class="d-flex" style="width: 360px">
                                    @include('partials._date_picker', [
                                        'inputName' => 'instruction_date_start',
                                        //'value' => Request::get('instruction_date_start', date('Ym01')),
                                        'value' => request('instruction_date_start') ?: '',
                                        'attributes' => 'data-error-messsage-container=#date_error_message', 
                                        'dateFormat' => 'YYYYMMDD', 
                                    ])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', [
                                        'inputName' => 'instruction_date_end',
                                        //'value' => Request::get('instruction_date_end', date('Ymt')),
                                        'value' => request('instruction_date_end') ?: '',
                                        'attributes' => 'data-error-messsage-container=#date_error_message', 
                                        'dateFormat' => 'YYYYMMDD', 
                                    ])
                                    </button>
                                </div>
                                <div id ="date_error_message"></div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">便No</label>
                                <div class="d-flex">
                                    <input type="text" id="" style="width: 40px" name="instruction_no_from" maxlength="2"
                                        class="acceptNumericOnly"
                                        data-error-messsage-container="#flight_no_error"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ old('instruction_no_from', Request::get('instruction_no_from') ?? '') }}">
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    <input type="text" id="" style="width: 40px" name="instruction_no_to" maxlength="2"
                                        data-error-messsage-container="#flight_no_error"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ old('instruction_no_to', Request::get('instruction_no_to') ?? '') }}">
                                </div>
                                <div id ="flight_no_error"></div>
                            </div>
                        </div>
                        <a href="{{ route('material.order.export.csv', Request::all()) }}"
                            class="float-right btn btn-green {{ $supplyMaterialOrders->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                        <ul class="buttonlistWrap">
                            <li>
                                <a class="btn btn-primary" style="min-width: 300px" id="resetForm">検索条件をクリア</a>
                            </li>
                            <li>
                                <button class="btn btn-blue" type="submit" style="min-width: 300px">
                                    検索
                                </button>
                            </li>
                        </ul>
                    </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">集計結果</h1>
                <div class="box">
                    <div class="mb-2">
                        @if (isset($supplyMaterialOrders))
                            @if ($supplyMaterialOrders && $supplyMaterialOrders->total() > 0)
                            {{ $supplyMaterialOrders->total() }}件中、{{ $supplyMaterialOrders->firstItem() }}件～{{ $supplyMaterialOrders->lastItem() }}
                            件を表示してます
                            @endif
                            <form id="temporaryValidationForm">
                            <table class="table table-bordered text-center table-striped-custom with-js-validation"
                                style="overflow-x:auto;">
                                <thead>
                                    <tr>
                                        <th width="350px;">管理No.</th>
                                        <th width="350px;">材料品番</th>
                                        <th width="350px;">材料メーカーコード</th>
                                        <th rowspan="2" width="100px;">背番号</th>
                                        <th rowspan="2" width="250px;">指示日</th>
                                        <th rowspan="2" width="250px;">便No.</th>
                                        <th rowspan="2" width="250px;">枚数</th>
                                        <th rowspan="2" width="250px;">収容数</th>
                                        <th rowspan="2" width="250px;">数量</th>
                                        <th rowspan="2" width="200px;">操作</th>
                                    </tr>
                                    <tr>
                                        <th>枝番</th>
                                        <th>材料品名</th>
                                        <th>材料メーカー名</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @forelse ($supplyMaterialOrders as $supplyMaterialOrder)
                                        <tr data-supply-material-order-id="{{ $supplyMaterialOrder->id }}">
                                            <td>
                                                <input type="text"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    class="valign-middle" name="management_no" maxlength="5"
                                                    id="management_no_{{ $supplyMaterialOrder->id }}"
                                                    data-original-value="{{ $supplyMaterialOrder->management_no }}"
                                                    value="{{ $supplyMaterialOrder->management_no }}" readonly>
                                                    
                                            </td>
                                            <td class="center" style="border: none">
                                                <input type="text" id="material_no_{{ $supplyMaterialOrder->id }}"
                                                    name="material_no"
                                                    value="{{ $supplyMaterialOrder->material_number }}"
                                                    data-validate-exist-model="ProductNumber"
                                                    data-validate-exist-column="part_number"
                                                    data-inputautosearch-model="ProductNumber"
                                                    data-inputautosearch-column="part_number"
                                                    data-inputautosearch-return="product_name"
                                                    data-inputautosearch-reference="product_name_{{ $supplyMaterialOrder->id }}"
                                                    class="mr-1"
                                                    data-original-value="{{ $supplyMaterialOrder->material_number }}"
                                                    readonly>
                                            </td>
                                            <td class="valign-middle">{{ $supplyMaterialOrder->supplier_code }}</td>
                                            
                                            <td rowspan="2" class="valign-middle">
                                                {{ optional($supplyMaterialOrder->kanban)->printed_jersey_number }}
                                            </td>
                                            <td rowspan="2" class="valign-middle">
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="text" name="instruction_date"
                                                        style="text-align: center"
                                                        id="instruction_date_{{ $supplyMaterialOrder->id }}"
                                                        data-format="YYYYMMDD"
                                                        value="{{ $supplyMaterialOrder->instruction_date?->format('Ymd') }}"
                                                        minlength="8" maxlength="8"
                                                        data-original-value="{{ $supplyMaterialOrder->instruction_date?->format('Ymd') }}"
                                                        class="acceptNumericOnly"
                                                        disabled>
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1"
                                                        data-target="instruction_date_{{ $supplyMaterialOrder->id }}"
                                                        data-format="YYYYMMDD" disabled>
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}"
                                                            alt="iconsvg_calendar_w.svg">
                                                    </button>
                                                </div>
                                                <div id="instruction_date_error" class="error_message text-left"></div>
                                            </td>
                                            <td rowspan="2" class="valign-middle">
                                                <input type="text" class="valign-middle acceptNumericOnly"
                                                    maxlength="2"
                                                    data-error-messsage-container="#instruction_no_error_message_{{ $supplyMaterialOrder->id }}"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    id="instruction_no_{{ $supplyMaterialOrder->id }}"
                                                    name="instruction_no"
                                                    data-original-value="{{ $supplyMaterialOrder->instruction_no }}"
                                                    value="{{ $supplyMaterialOrder->instruction_no }}" disabled>

                                                    <!-- <div id="instruction_no_error_message_{{ $supplyMaterialOrder->id }}"></div> -->
                                                    <div id="instruction_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td rowspan="2" class="valign-middle ">
                                                <input type="text" class="valign-middle"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    id="instruction_kanban_quantity_{{ $supplyMaterialOrder->id }}"
                                                    name="instruction_kanban_quantity"
                                                    data-original-value="{{ $supplyMaterialOrder->instruction_kanban_quantity }}"
                                                    value="{{ $supplyMaterialOrder->instruction_kanban_quantity }}"
                                                    readonly>
                                            </td>
                                            <td rowspan="2" class="valign-middle ">
                                                <input name="number_of_accomodated"
                                                    id="number_of_accomodated_{{ $supplyMaterialOrder->id }}"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    data-original-value="{{ optional($supplyMaterialOrder->kanban)->number_of_accomodated }}"
                                                    value="{{ optional($supplyMaterialOrder->kanban)->number_of_accomodated }}"
                                                    readonly>
                                            <td rowspan="2" class="valign-middle ">
                                                <input name="arrival_quantity"
                                                    id="arrival_quantity_{{ $supplyMaterialOrder->id }}"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    data-original-value="{{ $supplyMaterialOrder->arrival_quantity }}"
                                                    value={{$supplyMaterialOrder->instruction_kanban_quantity * optional($supplyMaterialOrder->kanban)->number_of_accomodated}}
                                                    disabled>
                                            </td>
                                            <td rowspan="2" class="valign-middle ">
                                                <div class="center" id="EditDelete">
                                                    <button type="button" onclick="enableInputs(this)"
                                                        class="btn btn-block btn-blue mr-1" id="edit">編集</button>
                                                    <button type="button" onclick="confirmDelete(this)" class="btn btn-block btn-orange"
                                                        style="margin-left: 2px" id="delete">削除</button>
                                                </div>

                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button type="button" onclick="updateData(this)"
                                                        class="btn btn-block btn-green mr-1" id="update">更新</button>
                                                    <button type="button" onclick="cancelEdit(this)" class="btn btn-block btn-gray"
                                                        data-row-index="{{ $supplyMaterialOrder->id }}"
                                                        style="margin-left: 1px" id="undo">取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>{{ $supplyMaterialOrder->branch_number }}</td>
                                            <td>
                                                <input type="text" readonly
                                                    id="product_name_{{ $supplyMaterialOrder->id }}"
                                                    name="product_name_{{ $supplyMaterialOrder->id }}"
                                                    data-original-value="{{ optional($supplyMaterialOrder->product)->product_name }}"
                                                    value="{{ optional($supplyMaterialOrder->product)->product_name }}"
                                                    class="middle-name" style="width: 170px">

                                            </td>
                                            <td>{{ $supplyMaterialOrder->supplier?->customer_name }}</td>
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductModal_' . $supplyMaterialOrder->id,
                                            'searchLabel' => '材料',
                                            'resultValueElementId' => 'material_no_' . $supplyMaterialOrder->id,
                                            'resultNameElementId' => 'product_name_' . $supplyMaterialOrder->id,
                                            'model' => 'ProductMaterial',
                                        ])
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">検索結果はありません</td>
                                        </tr>
                                    @endforelse
                                   
                                </tbody>
                            </table>
                            </form> 
                            {{ $supplyMaterialOrders->appends(request()->all())->links() }}
                        @else
                            <table class="table table-bordered text-center table-striped-custom">
                                <thead>
                                    <tr>
                                        <th width="350px;">管理No.</th>
                                        <th width="350px;">材料品番</th>
                                        <th width="350px;">材料メーカーコード</th>
                                        <th rowspan="2" width="100px;">背番号</th>
                                        <th rowspan="2" width="250px;">指示日</th>
                                        <th rowspan="2" width="250px;">便No.</th>
                                        <th rowspan="2" width="250px;">枚数</th>
                                        <th rowspan="2" width="250px;">収容数</th>
                                        <th rowspan="2" width="250px;">数量</th>
                                        <th rowspan="2" width="200px;">操作</th>
                                    </tr>
                                    <tr>
                                        <th>枝番</th>
                                        <th>材料品名</th>
                                        <th>材料メーカー名</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="11" class="text-center">検索結果はありません</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '材料',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductMaterial',
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchManufacturerModal',
        'searchLabel' => '材料メーカー',
        'resultValueElementId' => 'manufacturer_code',
        'resultNameElementId' => 'manufacturer_name',
        'model' => 'Supplier',
    ])

@endsection
@push('scripts')
    @vite(['resources/js/material/order/index.js'])
@endpush