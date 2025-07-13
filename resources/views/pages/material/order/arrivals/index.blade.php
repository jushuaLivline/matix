@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css') 
    @vite('resources/css/order/arrival/index.css') 
@endpush

@section('title', '入荷実績一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                入荷実績一覧
            </div>

            <form class="overlayedSubmitForm with-js-validation" data-disregard-empty="true" id="form_request">
                <div class="section">
                    <h1 class="form-label bar indented">検索</h1>
                    <div class="box mb-3">
                        <div class="mb-2 d-flex">
                            <div class="mr-3" style="width: 360px;">
                                <label class="form-label dotted indented">入荷日</label>
                                <div class="d-flex">
                                    @include('partials._date_picker', ['inputName' => 'arrival_day_from', 
                                    'attributes' => 'data-error-messsage-container=#date_error_message', 
                                    'value' => Request::get('arrival_day_from', date('Ym01'))])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', ['inputName' => 'arrival_day_to', 
                                    'attributes' => 'data-error-messsage-container=#date_error_message', 
                                    'value' => Request::get('arrival_day_to', date('Ymt'))])
                                </div>
                                <div id="date_error_message" style="width: 100%;"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">便No.</label>
                                <div class="d-flex">
                                    <input type="text"
                                           style="width: 40px"
                                           name="flight_from"
                                           maxlength="2"
                                           onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                           value="{{ Request::get('flight_from') }}">
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    <input type="text"
                                           style="width: 40px"
                                           name="flight_to"
                                           maxlength="2"
                                           onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                           value="{{ Request::get('flight_to') }}">
                                </div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">材料メーカー</label>
                                <div class="d-flex">
                                    <input type="text" id="customer_code" name="customer_code"
                                        data-field-name="材料メーカー"
                                        data-error-messsage-container="#customer_code_error_message"
                                        data-validate-exist-model="Customer"
                                        data-validate-exist-column="customer_code"
                                        data-inputautosearch-model="Customer"
                                        data-inputautosearch-column="customer_code"
                                        data-inputautosearch-return="customer_name"
                                        data-inputautosearch-reference="customer_name" class="text-left"
                                        maxlength="6"
                                        style="width: 100px; margin-right: 10px;"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ Request::get('customer_code') }}" class="mr-half">
                                    <input type="text"
                                        id="customer_name"
                                        name="customer_name"
                                        value="{{ Request::get('customer_name') }}"
                                        class="middle-name mr-half"
                                        style="width: 230px"
                                        readonly>
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchManufacturerInfo">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="customer_code_error_message" style="width: 100%;"></div>
                            </div>
                        </div>
                        <br/>
                        <div class="mb-4 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">納入No</label>
                                <div class="d-flex">
                                    <input type="text"
                                           id=""
                                           style="width: 100%"
                                           name="delivery_no"
                                           value="{{ Request::get('delivery_no') }}">
                                </div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">材料品番</label>
                                <div class="d-flex">
                                    <input type="text" id="product_code" name="product_code"
                                        data-field-name="材料品番"
                                        data-error-messsage-container="#product_code_error_message"
                                        data-validate-exist-model="ProductNumber"
                                        data-validate-exist-column="part_number"
                                        data-inputautosearch-model="ProductNumber"
                                        data-inputautosearch-column="part_number"
                                        data-inputautosearch-return="product_name"
                                        data-inputautosearch-reference="product_name" 
                                        class="text-left"
                                        style="margin-right: 10px; width: 160px;"
                                        value="{{ Request::get('product_code') }}" >
                                    <input type="text"
                                        id="product_name"
                                        name="product_name"
                                        value="{{ Request::get('product_name') }}"
                                        class="middle-name mr-half"
                                        style="width: 300px"
                                        readonly>
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="product_code_error_message" style="width: 100%;"></div>
                            </div>
                        </div>
                        <a href="{{ route('material.arrivalExport.csv', Request::all()) }}" class="float-right btn btn-green {{ $supplyMaterialArrivals->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
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
                </div>
            </form>

            <div class="section">
                <h1 class="form-label bar indented">集計結果</h1>
                <div class="box">
                    @if(isset($supplyMaterialArrivals))
                        <div class="mb-2">
                            @if($supplyMaterialArrivals && $supplyMaterialArrivals->count() > 0)
                                {{ $supplyMaterialArrivals->total() }}件中、{{ $supplyMaterialArrivals->firstItem() }}件～{{ $supplyMaterialArrivals->lastItem() }} 件を表示してます
                            @endif
                            <form id="orderArrivalForm">
                            <table class="table table-bordered text-center table-striped-custom with-js-validation">
                                <thead>
                                <tr>
                                    <th style="width: 180px;">納入No</th>
                                    <th>材料品番</th>
                                    <th>品名</th>
                                    <th style="width: 180px;">入荷日</th>
                                    <th style="width: 80px;">便No.</th>
                                    <th style="width: 100px;">入荷数</th>
                                    <th style="width: 180px;">伝票区分</th>
                                    <th style="width: 150px;">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse ($supplyMaterialArrivals as $supplyMaterialArrival)
                                        <tr data-supply-material-arrival-id="{{ $supplyMaterialArrival->id }}">
                                            <td>
                                                <input type="text" disabled 
                                                    id="delivery_no_{{ $supplyMaterialArrival->id }}"
                                                    name="delivery_no"
                                                    data-original-value="{{ $supplyMaterialArrival->delivery_no }}"
                                                    value="{{ $supplyMaterialArrival->delivery_no }}">
                                                <div id="delivery_no_error" class="error_message"></div>
                                            </td>
                                            <td>
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="text" 
                                                        id="product_code_{{ $supplyMaterialArrival->id }}"
                                                        name="product_code"
                                                        data-error-messsage-container="#product_code_error"
                                                        data-validate-exist-model="ProductNumber"
                                                        data-validate-exist-column="part_number"
                                                        data-inputautosearch-model="ProductNumber"
                                                        data-inputautosearch-column="part_number"
                                                        data-inputautosearch-return="product_name"
                                                        data-inputautosearch-reference="product_name_{{ $supplyMaterialArrival->id }}" 
                                                        data-original-value="{{ $supplyMaterialArrival->material_no }}" 
                                                        value="{{ $supplyMaterialArrival->material_no }}" 
                                                        class="mr-1"
                                                        disabled>
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchProductModal_{{ $supplyMaterialArrival->id }}"
                                                            disabled>
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                                {{-- <input type="text" disabled class="numberCharacter" name="material_no" value=""> --}}
                                                <div id="product_code_error" class="error_message"></div>
                                            </td>
                                            <td>
                                                <input type="text" readonly
                                                    id="product_name_{{ $supplyMaterialArrival->id }}"
                                                    name="product_name_{{ $supplyMaterialArrival->id }}"
                                                    data-original-value="{{ $supplyMaterialArrival->productMaterial?->product_name }}" 
                                                    value="{{ $supplyMaterialArrival->productMaterial?->product_name }}"
                                                    class="middle-name"
                                                    style="width: 170px"
                                                    disabled>
                                                <input type="text" name="part_number" value="{{ $supplyMaterialArrival->material_no }}" hidden>
                                            </td>
                                            <td>
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="text" 
                                                        name="arrival_date" style="text-align: center" 
                                                        id="arrival_date_{{ $supplyMaterialArrival->id }}" 
                                                        data-format="YYYYMMDD"
                                                        minlength="8"
                                                        maxlength="8"
                                                        pattern="\d*" 
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        data-original-value="{{ $supplyMaterialArrival->arrival_day->format('Ymd') }}" 
                                                        value="{{ $supplyMaterialArrival->arrival_day->format('Ymd') }}"
                                                        disabled>
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1" 
                                                            data-target="arrival_date_{{ $supplyMaterialArrival->id }}"
                                                            data-format="YYYYMMDD"
                                                            disabled>
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                    </button>
                                                </div>
                                                <div id="arrival_date_error" class="error_message"></div>
                                            </td>
                                            <td>
                                                <input type="text" disabled 
                                                    id="flight_no_{{ $supplyMaterialArrival->id }}"
                                                    name="flight_no"
                                                    maxlength="2"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    data-original-value="{{ $supplyMaterialArrival->flight_no }}" 
                                                    value="{{ $supplyMaterialArrival->flight_no }}">
                                                    <div id="flight_no_error" class="error_message"></div>
                                            </td>
                                            <td>
                                                <input type="text" disabled 
                                                    id="arrival_quantity_{{ $supplyMaterialArrival->id }}"
                                                    name="arrival_quantity" 
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    data-original-value="{{ $supplyMaterialArrival->arrival_quantity }}" 
                                                    value="{{ $supplyMaterialArrival->arrival_quantity }}">
                                                    <div id="arrival_quantity_error" class="error_message"></div>
                                            </td>
                                            <td>
                                                <input type="text" readonly 
                                                    value="{{ $supplyMaterialArrival->voucher_class == 1 ? '支給' :
                                                    ($supplyMaterialArrival->voucher_class == 2 ? '返品' :
                                                    ($supplyMaterialArrival->voucher_class == 3 ? '材不返品' : '')) }}">
                                            </td>
                                            <td>
                                                <div class="center" id="EditDelete">
                                                    <button type="button" onclick="enableInputs(this)" class="btn btn-block btn-blue mr-1" id="edit">編集</button>
                                                    <button type="button" onclick="confirmDelete(this)" class="btn btn-block btn-orange" style="margin-left: 2px" id="delete">削除</button>
                                                </div>
                                                
                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button type="button" onclick="updateData(this)" class="btn btn-block btn-green mr-1" id="update">更新</button>
                                                    <button type="button" data-row-index="{{ $supplyMaterialArrival->id }}" onclick="cancelEdit(this)" class="btn btn-block btn-gray" style="margin-left: 1px" id="undo">取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductModal_'. $supplyMaterialArrival->id,
                                            'searchLabel' => '材料',
                                            'resultValueElementId' => 'product_code_'. $supplyMaterialArrival->id,
                                            'resultNameElementId' => 'product_name_'. $supplyMaterialArrival->id,
                                            'model' => 'ProductMaterial'
                                        ])
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">検索結果はありません</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </form>
                        </div>
                        {{ $supplyMaterialArrivals->appends(request()->all())->links() }}
                    @else
                        <table class="table table-bordered text-center table-striped-custom">
                            <thead>
                            <tr>
                                <th>納入No</th>
                                <th>材料品番</th>
                                <th>品名</th>
                                <th>入荷日</th>
                                <th>便No.</th>
                                <th>入荷数</th>
                                <th>伝票区分</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tr>
                                <td colspan="8" class="text-center">検索結果はありません</td>
                            </tr>
                        </table>
                    @endif
                </div>
                <div class="space-between mt-1">
                    <div>
                        <p class="text-red" id="warningInputs" style="display:none;">登録に必要ないくつかの情報が入力されていません！</p>
                    </div>
                    <div>
                        {{-- <a href="#" class="btn btn-blue" style="width: 250px"> メニューに戻る </a> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="flash-message" id="flash-message">「支給材情報は正常に削除されました」</div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchManufacturerInfo',
        'searchLabel' => '材料メーカー',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '材料品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
@endsection
@push('scripts')
    @vite(['resources/js/material/order/arrivals/index.js'])
@endpush