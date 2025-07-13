@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/shipment/actual/index.css')
@endpush

@section('title', '出荷実績検索・一覧')

@section('content')
<div class="content">
    <div class="contentInner">
        <div class="pageHeaderBox rounded">
            出荷実績検索・一覧
        </div>
        <div class="section">
            <h1 class="form-label bar indented">検索</h1>
            <form id="shipment-result-search-form" accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" data-disregard-empty="true">
                <div class="box mb-3">
                    <div class="mb-2 d-flex">
                        <div class="mr-4">
                            <label class="form-label dotted indented">納入日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                    'inputName' => 'due_date_start',
                                    'attributes' => 'data-error-messsage-container=#due_date_error data-field-name=納入日', 
                                    'value' => Request::get('due_date_start')
                                ])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', [
                                    'inputName' => 'due_date_end',
                                    'attributes' => 'data-error-messsage-container=#due_date_error data-field-name=納入日', 
                                    'value' => Request::get('due_date_end')
                                ])
                            </div>
                            <div id="due_date_error" style="color: red"></div>
                        </div>

                        <div class="ml-3">
                            <label class="form-label dotted indented">便No.</label>
                            <div class="d-flex">
                                <input type="text" name="delivery_number_start"
                                    value="{{ Request::get('delivery_number_start') }}"
                                    maxlength="2"
                                    class="accepNumericOnly w-100c">
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                <input type="text" name="delivery_number_end"
                                    value="{{ Request::get('delivery_number_end') }}"
                                    maxlength="2"
                                    class="accepNumericOnly w-100c">
                            </div>
                        </div>

                    </div>

                    <div class="mb-3 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">納入先</label>
                            <div class="d-flex">
                                <input type="text" id="supplier_code" 
                                            data-field-name="納入先"
                                            data-error-messsage-container="#supplier_code_error"
                                            data-validate-exist-model="supplier"
                                            data-validate-exist-column="customer_code"
                                            data-inputautosearch-model="supplier"
                                            data-inputautosearch-column="customer_code"
                                            data-inputautosearch-return="supplier_name_abbreviation"
                                            data-inputautosearch-reference="supplier_name"
                                            name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ request()->get('supplier_code') }}">
                                <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ request()->get('supplier_name') }}" style="margin-right: 10px;">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal"
                                        data-query-field="">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="supplier_code_error"></div>
                        </div>

                        <div class="mr-3 ml-1">
                            <label class="form-label dotted indented">伝票No</label>
                            <div class="d-flex">
                                <input type="text" name="slip_no" 
                                    maxLength="20"
                                    data-field-name="納入先"
                                    data-error-messsage-container="#slip_no_error"
                                    value="{{ Request::get('slip_no') }}"
                                    >
                            </div>
                            <div id="slip_no_error"></div>
                        </div>

                        <div class='mr-3'>
                            <label class="form-label dotted indented">伝票区分</label>
                            <div class="d-flex radio-div">
                                <input type="radio" name="voucher_class"  id="voucher-class-1"
                                    value="1"
                                    {{ Request::get('voucher_class') == 1 ? 'checked' : '' }}
                                >
                                <label for="voucher-class-1">出荷</label>
                                <input type="radio" name="voucher_class"  id="voucher-class-2"
                                    value="2"
                                    {{ Request::get('voucher_class') == 2 ? 'checked' : '' }}
                                >
                                <label for="voucher-class-2">返品</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">製品品番</label>
                            <div class="d-flex">
                                    @php
                                        $product_code  =  request()->get('product_code') ?? '';
                                        $product_name  =  ($product_code) ? request()->get('product_name')  : '';
                                    @endphp
                                    <input type="text" name="product_code" id="product_code" 
                                        data-field-name="製品品番"
                                        data-error-messsage-container="#product_code_error"
                                        data-validate-exist-model="ProductNumber"
                                        data-validate-exist-column="part_number"
                                        data-inputautosearch-model="ProductNumber"
                                        data-inputautosearch-column="part_number"
                                        data-inputautosearch-return="product_name"
                                        data-inputautosearch-reference="product_name"
                                        value="{{ $product_code }}"
                                        class="w-130c mr-2">

                                    <input type="text" readonly 
                                        value="{{ $product_name  }}"
                                        class="middle-name mr-2" name="product_name" id="product_name">

                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="product_code_error"></div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                <input type="text" name="department_code"
                                    id="department_code" style="margin-right: 10px; width: 100px; ime-mode: disabled"
                                    data-validate-exist-model="Department"
                                    data-validate-exist-column="code"
                                    data-inputautosearch-model="Department"
                                    data-inputautosearch-column="code"
                                    data-inputautosearch-return="name"
                                    data-inputautosearch-reference="department_name"
                                    class="text-left acceptNumericOnly"
                                    minlength="6"
                                    maxlength="6"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ request()->get('department_code') }}" 
                                    >
                                <input type="text" readonly
                                    name="department_name"
                                    id="department_name" style="margin-right: 10px; width: 290px;"
                                    value="{{ request()->get('department_name')}}"
                                    class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div data-error-container="delivery_destination_code"></div>

                        </div>
                    </div>

                    <div class="text-center sc relative button-div">
                        <button type="reset" class="btn btn-primary btn-wide">検索条件をクリア</button>
                        <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        <a href="{{ route('shipment.actual.exportExcel', request()->query()) }}" id="export-excel" class="btn btn-success btn-wide absolute-right {{ $shipment_record->total() == 0 ? 'btn-disabled' : '' }}"
                            {{ count($shipment_record) == 0 ? 'onclick="return false;" style=pointer-events:none; opacity:0.5; cursor:not-allowed;"' : '' }}
                            >
                            検索結果をEXCEL出力
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="section">
            <h1 class="form-label bar indented">検索結果</h1>
            <div class="box">
                <div>
                    @if ($shipment_record->total() > 0)
                        {{ $shipment_record->total() }}件中、{{ $shipment_record->firstItem() }}件～{{ $shipment_record->lastItem() }}件を表示してます
                    @endif
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>納入日</th>
                                <th>便No</th>
                                <th>納入先</th>
                                <th>伝票No</th>
                                <th>受入</th>
                                <th>直送先</th>
                                <th>製品品番</th>
                                <th>品名</th>
                                <th>納入数</th>
                                <th>部門</th>
                                <th>備考</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            $total = 0;
                        @endphp
                        @forelse($shipment_record as $entry)
                            <tr shipment-record="{{ $entry->id }}">
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($entry->due_date)->format('Y/m/d') }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->delivery_no }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->customer?->customer_name }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->slip_no }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->acceptance }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->drop_ship_code }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->product_no }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->productNumber?->product_name }}
                                </td>
                                <td class="text-center">
                                    {{ $entry->quantity }}
                                    @php
                                        $total+= $entry->quantity
                                    @endphp
                                </td>
                                <td>
                                    {{ $entry->department?->department_name }}
                                </td>
                                <td>
                                    {{ $entry->remarks }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ 
                                            route('shipment.actual.create')
                                            .'?slip_no=' . $entry->slip_no 
                                            .'&supplier_code=' . $entry->delivery_destination_code
                                            .'&supplier_name=' . $entry->customer?->supplier_name_abbreviation
                                            .'&acceptance=' . $entry->acceptance
                                            .'&drop_ship_code=' . $entry->drop_ship_code
                                            .'&plant=' . $entry->plant
                                            .'&delivery_no=' . $entry->delivery_no
                                            .'&instruction_date=' . \Carbon\Carbon::parse($entry->due_date)->format('Ymd')
                                        }}" 
                                        class="btn btn-blue btn-edit" style="flex: 1; width:100px;">
                                        編集
                                     </a>
                                </td>                                    
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center">検索結果はありません</td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if ($shipment_record && count($shipment_record) > 0)
                            <tfoot>
                                <tr style="background-color: transparent;">
                                    <td class="no-border-1" colspan="7"></td>
                                    <td class="bordered text-center">合計</td>
                                    <td class="bordered text-right">
                                            {{ $total }}
                                    </td>
                                    <td class="no-border-2" colspan="3">
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
            {{ $shipment_record->appends(request()->all())->links() }}
        </div>
    </div>
</div>

@include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '納入先',
    'resultValueElementId' => 'supplier_code',
    'resultNameElementId' => 'supplier_name',
    'model' => 'Customer'
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchProductModal',
    'searchLabel' => '製品品番',
    'resultValueElementId' => 'product_code',
    'resultNameElementId' => 'product_name',
    'model' => 'ProductNumber'
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchDepartmentModal',
    'searchLabel' => '部門',
    'resultValueElementId' => 'department_code',
    'resultNameElementId' => 'department_name',
    'model' => 'Department'
])
@endsection

@push('scripts')
    @vite('resources/js/shipment/actual/index.js')
@endpush