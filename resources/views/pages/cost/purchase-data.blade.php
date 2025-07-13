@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '費目別・仕入データリスト')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>費目別・仕入データリスト</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('cost.purchaseDataSearch')  }}" method="GET" accept-charset="utf-8" id="createReqFrm" class="overlayedSubmitForm">
                <div class="tableWrap borderLesstable inputFormArea">
                    <div class="row-content">
                        <!-- 品番 -->
                        <div class="flex-row">
                            <label for="year_month" class="label_for" style="padding-bottom: 5px;">
                                年月
                                <span class="required">必須</span>
                            </label>

                            <input type="text" style="width: 150px;" id="year_month" name="year_month" placeholder="YYYYMM" value="{{ Request::get('year_month') }}">
                            <div class="error_msg"></div>
                        </div>
                        <!-- 費目 -->
                        <div class="flex-row">
                            <label for="expense_item" class="label_for" style="padding-bottom: 5px;">
                                費目
                                <span class="required">必須</span>
                            </label>
                            <div class="d-flex">
                                <input type="text" id="expense_item" name="expense_item" value="{{ Request::get('expense_item') }}" class="mr-25  searchOnInput ExpenseItem" style="width: 100px">
                                <input type="text" readonly
                                id="item_name"
                                name="item_name"
                                value="{{ Request::get('item_name') }}"
                                class="middle-name mr-25"
                                style="width: 170px">
                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom btnSubmitCustom--size js-modal-open"
                                            data-target="searchItemModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                            <div class="error_msg"></div>
                        </div>
                        <!-- 部門 -->
                        <div class="flex-row">
                            <label for="department_code" class="label_for">部門</label>
                            <div class="d-flex">
                                <input type="text" id="department_code" name="department_code" value="{{ Request::get('department_code') }}" class="mr-25" style="width: 100px">
                                <input type="text" readonly
                                        id="department_name"
                                        name="department_name"
                                        value="{{ Request::get('department_name') }}"
                                        class="middle-name mr-25"
                                        style="width: 170px">
                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom btnSubmitCustom--size js-modal-open"
                                                    data-target="searchDepartmentModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                        </div>
                        <!-- ライン -->
                        <div class="flex-row">
                            <label for="line_code" class="label_for">ライン</label>
                            <div class="d-flex">
                                <input type="text" id="line_code" name="line_code" value="{{ Request::get('line_code') }}" class="mr-25" style="width: 80px">
                                <input type="text" readonly
                                        id="line_name"
                                        name="line_name"
                                        value="{{ Request::get('line_name') }}"
                                        class="middle-name mr-25"
                                        style="width: 135px">
                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom btnSubmitCustom--size js-modal-open"
                                                    data-target="searchLineModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="row-group-content">
                        <!-- 仕入先 -->
                        <div class="flex-row">
                            <label for="supplier_code" class="label_for">仕入先</label>
                            <div class="d-flex">
                                <input type="text" id="supplier_code" name="supplier_code" value="{{ Request::get('supplier_code') }}" class="mr-25" style="width: 100px">
                                <input type="text" readonly
                                        id="supplier_name"
                                        name="supplier_name"
                                        value="{{ Request::get('supplier_name') }}"
                                        class="middle-name mr-25"
                                        style="width: 170px">
                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom btnSubmitCustom--size js-modal-open"
                                                    data-target="searchSupplierModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                        </div>
                        <!-- 得意先 -->
                        <div class="flex-row">
                            <label for="customer_code" class="label_for">得意先</label>
                            <div class="d-flex">
                                <input type="text" id="customer_code" name="customer_code" value="{{ Request::get('customer_code') }}" class="mr-25" style="width: 100px">
                                <input type="text" readonly
                                        id="customer_name"
                                        name="customer_name"
                                        value="{{ Request::get('customer_name') }}"
                                        class="middle-name mr-25"
                                        style="width: 170px">
                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom btnSubmitCustom--size js-modal-open"
                                                    data-target="searchCustomerModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center sc relative">
                        <a href="#" id="export_csv" class="btn btn-green absolute-right">検索結果をEXCEL出力</a>
                        @php
                            $currentYear = now()->year;
                            $currentMonth = now()->format('m');
                        @endphp
                        <a href="{{ route("cost.purchaseData") }}?year_month={{ $currentYear.$currentMonth }}"
                            class="btn btn-blue js-btn-reset-reload" id="search">検索条件をクリア</a>
                        <button type="submit"
                        class="btn btn-blue" id="search">検索</button>
                        {{-- <a href="{{ route("estimate.index") }}"
                        class="buttonBasic btn-reset bColor-ok js-btn-reset-reload" style="width: 250px!important; background-color: green;">検索結果をEXCEL出力 --}}
                        </div>
                    </div>
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    @if (count($data) > 0)
                        <li>{{ $count }}件中、{{ $data->firstItem() }}件～{{ $data->lastItem() }} 件を表示してます</li>
                    @else
                        <li></li>
                    @endif
                    <li></li>
                </ul>
                <table class="tableBasic list-table">
                    <tbody>
                        <tr>
                            <th style="width: 120px;">部門コード</th>
                            <th style="width: 300px;">部門名</th>
                            <th style="width: 120px;">ラインコード</th>
                            <th style="width: 250px;">ライン名</th>
                            <th style="width: 80px;">品番</th>
                            <th style="width: 300px;">品名</th>
                            <th style="width: 80px;">数量</th>
                            <th style="width: 80px;">単位</th>
                            <th style="width: 80px;">単価</th>
                            <th style="width: 80px;">金額</th>
                            <th style="width: 80px;">伝票No.</th>
                            <th style="width: 250px;">仕入先名</th>
                            <th style="width: 250px;">得意先名</th>
                            <th style="width: 80px;">入荷日</th>
                        </tr>
                    @if (count($data) <= 0)
                        @include('partials._no_record', ['colspan' => 14])
                    @else
                        @foreach ($data as $purchase_data)
                            <tr>
                                <td>{{ isset($purchase_data->department_code) ? $purchase_data->department_code : '' }}</td>
                                <td>{{ isset($purchase_data->department) ? $purchase_data->department : '' }}</td>
                                <td>{{ isset($purchase_data->line_code) ? $purchase_data->line_code : '' }}</td>
                                <td>{{ isset($purchase_data->line) ? $purchase_data->line : '' }}</td>
                                <td>{{ isset($purchase_data->part_number) ? $purchase_data->part_number : '' }}</td>
                                <td>{{ isset($purchase_data->product_name) ? $purchase_data->product_name : '' }}</td>
                                <td style="text-align: right;">{{ isset($purchase_data->quantity) ? $purchase_data->quantity : 0 }}</td>
                                <td style="text-align: center;">{{ isset($purchase_data->unit) ? $purchase_data->unit : '' }}</td>
                                <td style="text-align: right;">{{ isset($purchase_data->unit_price) ? $purchase_data->unit_price : 0 }}</td>
                                <td style="text-align: right;">{{ isset($purchase_data->amount) ? number_format($purchase_data->amount, 2) : 0 }}</td>
                                <td>{{ isset($purchase_data->slip_no) ? $purchase_data->slip_no : '' }}</td>
                                <td>{{ isset($purchase_data->supplier) ? $purchase_data->supplier : '' }}</td>
                                <td>{{ isset($purchase_data->customer) ? $purchase_data->customer : '' }}</td>
                                <td style="text-align: center;">{{ $purchase_data->date }}</td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
                @if (count($data) > 0)
                    {{ $data->links() }}
                @endif
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '取引先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'NotSupplier'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ラインコード',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item',
        'resultNameElementId' => 'item_name',
        'model' => 'Item'
    ])

@php
$dataConfigs['ExpenseItem'] = [
    'model' => 'Item',
    'reference' => 'item_name'
];
@endphp

<x-search-on-input :dataConfigs="$dataConfigs" />
@endsection
@push('styles')
<style>
    .required {
        border: 1px transparent;
        padding: 4px 15px;
        background-color: #ed7d32;
        color: white;
    }
    th, td {
        padding: 8px;
    }

    .footer {
        position: static;
    }
</style>
@endpush
@push('scripts')
    @vite(['resources/js/cost/purchase-data/data-form.js'])
@endpush