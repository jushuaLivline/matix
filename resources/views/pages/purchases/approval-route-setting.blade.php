@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    <style>
        .calendar-plugin input {
            text-align: left;
            width: 6rem !important;
        }
        .btnExport {
            cursor: pointer;
        }
    </style>
    @vite('resources/css/sales/sale_plan_search.css')
@endpush

@section('title', '発注金額明細表発行')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>購買品 購入実績入力</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>購買品 購入実績入力</span></h1>
            </div>

            <form class="overlayedSubmitForm" action="{{ route('purchase.approvalRouteSetting.store') }}" method="POST" accept-charset="utf-8">
                @csrf
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic arrival-search w-100">
                        <tbody>
                        <tr class="d-block w-60">
                                <!-- 仕入先 -->
                                <td class="w-20">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">伝票区分</dt>
                                        <dd>
                                        <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="voucher_class" value="1" {{ (request()->type ?? 1) == 1 ? 'checked' : '' }}>
                                                    <span>購入</span>
                                                </label>
                                            </p>
                                            <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="voucher_class" value="2" {{ (request()->type ?? 1) == 2 ? 'checked' : '' }}>
                                                    <span>修正・返品</span>
                                                </label>
                                            </p>
                                            <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="voucher_class" value="3" {{ (request()->type ?? 1) == 3 ? 'checked' : '' }}>
                                                    <span>値引</span>
                                                </label>
                                            </p>
                                            
                                        </dd>
                                    </dl>
                                </td>

                                <td class="w-20">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">伝票種類</dt>
                                        <dd>
                                        <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="slip_type" value="1" {{ (request()->type ?? 1) == 1 ? 'checked' : '' }}>
                                                    <span>納入伝票</span>
                                                </label>
                                            </p>
                                            <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="slip_type" value="2" {{ (request()->type ?? 1) == 2 ? 'checked' : '' }}>
                                                    <span>外注加工伝票</span>
                                                </label>
                                            </p>
                                            <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="slip_type" value="3" {{ (request()->type ?? 1) == 3 ? 'checked' : '' }}>
                                                    <span>購入材伝票</span>
                                                </label>
                                            </p>
                                            
                                        </dd>
                                    </dl>
                                </td>

                                <!-- 年月 -->
                                <td style="max-width: 175px;">
                                    <dl class="formsetBox">
                                        <dt>入荷日</dt>
                                        <dd>
                                            <div class="d-flex">
                                                @include('partials._date_picker', ['inputName' => 'arrival_date'])
                                            </div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-40">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">仕入先</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="supplier_code"
                                                       id="supplier_code"
                                                       class="text-left"
                                                       value="{{ request()->get('supplier_code') }}">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                       name="supplier_name"
                                                       id="supplier_name"
                                                       value="{{ request()->get('supplier_name') }}"
                                                       class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchSupplierModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                         alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>

                                <!-- 年月 -->
                                <td class="w-50">
                                    <dl class="formsetBox">
                                        <dt class="">機番</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="machine_number"
                                                    id="machine_number"
                                                    class="text-left searchOnInput MachineNumber"
                                                    style="margin-right: 10px;"
                                                    value="{{ request()->get('machine_number') }}">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p w-10">
                                                <input type="text" name="machine_number2"
                                                id="machine_number2"
                                                class="text-left"
                                                style="margin-right: 10px; width: 35px;"
                                                value="{{ request()->get('machine_number2') }}">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                name="machine_number_name"
                                                id="machine_number_name"
                                                style="margin-right: 10px;"
                                                value="{{ request()->get('machine_number_name') }}"
                                                class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchMachineNumberModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-40">
                                    <dl class="formsetBox">
                                        <dt class="">部門</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="department_code"
                                                id="department_code"
                                                class="text-left searchOnInput Department"
                                                value="{{ request()->get('department_code') }}"
                                                style="margin-right: 10px;">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                name="department_name"
                                                id="department_name"
                                                style="margin-right: 10px;"
                                                value="{{ request()->get('department_name') }}"
                                                class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchDepartmentModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>

                                <!-- 年月 -->
                                <td class="w-50">
                                    <dl class="formsetBox">
                                        <dt class="">ライン</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="line_code"
                                                id="line_code"
                                                class="text-left searchOnInput Line"
                                                style="margin-right: 10px;"
                                                value="{{ request()->get('line_code') }}">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                        name="line_name"
                                                        id="line_name"
                                                        style="margin-right: 10px;"
                                                        value="{{ request()->get('line_name') }}"
                                                        class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchLineModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-40">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">費目</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="item_code"
                                                    id="item_code"
                                                    class="text-left searchOnInput Item"
                                                    value="{{ request()->get('item_code') }}"
                                                    style="margin-right: 10px;">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="item_name"
                                                    id="item_name"
                                                    style="margin-right: 10px;"
                                                    value="{{ request()->get('item_name') }}"
                                                    class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchItemModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>

                                <!-- 年月 -->
                                <td class="w-50">
                                    <dl class="formsetBox">
                                        <dt class="">品番</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p box-middle-name">
                                                <input type="text" name="product_number_number" id="product_number_number" 
                                                class="text-left searchOnInput ProductNumber" 
                                                value="{{ request()->get('product_number_number') }}"
                                                style="margin-right: 10px;">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="product_number_name"
                                                    id="product_number_name"
                                                    style="margin-right: 10px;"
                                                    value="{{ request()->get('product_number_name') }}"
                                                    class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductNumberModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-20">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">品名</dt>
                                        <dd>
                                            <p class="formPack fixedWidth">
                                                <input type="text" name="product_name"
                                                id="product_name"
                                                class="text-left"
                                                value="{{ request()->get('product_name') }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>

                                <!-- 年月 -->
                                <td class="w-50">
                                    <dl class="formsetBox">
                                        <dt class="">規格</dt>
                                        <dd>
                                            <p class="formPack fixedWidth">
                                                <input type="text" name="standard"
                                                id="standard"
                                                class="text-left"
                                                value="{{ request()->get('standard') }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-40">
                                    <dl class="formsetBox">
                                        <dt class="">使用先</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="where_to_use_code"
                                                        id="where_to_use_code"
                                                        class="text-left searchOnInput Customer"
                                                        value="{{ request()->get('where_to_use_code') }}"
                                                        style="margin-right: 10px;">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                name="where_to_use_name"
                                                id="where_to_use_name"
                                                style="margin-right: 10px;"
                                                value="{{ request()->get('where_to_use_name') }}"
                                                class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchCustomerModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>

                                <!-- 年月 -->
                                <td class="w-30">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">数量</dt>
                                        <dd>
                                            <p class="formPack fixedWidth">
                                                <input type="text" name="quantity"
                                                id="quantity"
                                                class="text-left"
                                                value="{{ request()->get('quantity') }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                                <td class="w-30">
                                    <dl class="formsetBox">
                                        <dt class="">単位</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <select class="" name="unit_code" id="unit_code">
                                                    @foreach ($codes as $code)
                                                        @if ($code->code == request()->get('unit_code'))
                                                        <option value="{{ $code->code }}" selected>{{ request()->name }}</option>
                                                        @else
                                                        <option value="{{ $code->code }}">{{ $code->name }}</option>
                                                        @endif
                                                        
                                                    @endforeach
                                                </select>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-20">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">単価</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="unit_price"
                                                id="unit_price"
                                                class="text-left"
                                                value="{{ request()->get('unit_price') }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>

                                <!-- 年月 -->
                                <td class="w-50">
                                    <dl class="formsetBox">
                                        <dt class="">金額</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="amount"
                                                    id="amount"
                                                    value="{{ request()->get('amount') }}"
                                                    class="middle-name text-left" style="margin-right: 10px;">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open" id="calculate_amount"
                                                        data-target="a" style="padding-top: 10px; width: 90px !important;">
                                                        金額計算
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-40">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">課税区分</dt>
                                        <dd>
                                            <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="tax_classification" value="1" {{ (request()->tax_classification ?? 1) == 1 ? 'checked' : '' }}>
                                                    <span>課税</span>
                                                </label>
                                            </p>
                                            <p class="formPack radioSale">
                                                <label class="radioBasic">
                                                    <input type="radio" name="tax_classification" value="2" {{ (request()->tax_classification ?? 1) == 2 ? 'checked' : '' }}>
                                                    <span>非課税</span>
                                                </label>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>

                            </tr>
                            <tr class="d-block w-50">
                                <!-- 仕入先 -->
                                <td class="w-20">
                                    <dl class="formsetBox">
                                        <dt class="">伝票No.</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="slip_code"
                                                    id="slip_code"
                                                    class="text-left"
                                                    value="{{ request()->get('slip_code') }}"
                                                    style="margin-right: 10px;">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                                <!-- 年月 -->
                                <td class="w-50">
                                    <dl class="formsetBox">
                                        <dt class="">プロジェクトNo.</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="project_code"
                                                    id="project_code"
                                                    class="text-left searchOnInput Project"
                                                    style="margin-right: 10px;"
                                                    value="{{ request()->get('project_code') }}">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="project_name"
                                                    id="project_name"
                                                    style="margin-right: 10px;"
                                                    value="{{ request()->get('project_name') }}"
                                                    class="middle-name text-left">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProjectModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr class="d-block w-50">
                                <td class="w-50">
                                    <dl class="formsetBox">
                                        <dt class="">備考</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <textarea rows="5" cols="100" type="text" name="remarks" id="remarks" value="" class=""
                                                placeholder=""></textarea>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="btnListContainer">
                    <div class="btnContainerMain justify-content-flex-end">
                        <div class="btnContainerMainRight">
                            <button type="button" class="btn btn-blue">
                                メニューに戻る
                            </button>
                            <button type="button" id="btn-populate-input-from-session" data-source="/api/purchase/approval-route-setting/{{ Session::get("approvalRouteSetting") }}" class="btn btn-blue @if(!Session::has("approvalRouteSetting")) btn-disabled @endif" @disabled(!Session::has("approvalRouteSetting"))>
                                前回入力から複写
                            </button>
                            <button type="button" class="btn btn-blue">
                                クリア
                            </button>
                            <button type="submit" class="btn btn-green">
                                この内容で登録する
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            @if((request()->supplier_code ?? '') != '')
                <div class="tableWrap bordertable" style="clear: both;">
                    <div class="mb-2">
                        n件中、n件～n件を表示してます
                        <table class="table table-bordered text-center table-striped-custom">
                            <thead>
                            <tr>
                                <th rowspan="2">品番</th>
                                <th rowspan="2">品名</th>
                                <th rowspan="2">発注</th>
                                <th rowspan="2">月間依頼数</th>
                                <th rowspan="2">単価</th>
                                <th rowspan="2">金額</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_amount = 0;
                                    $total_qty = 0;
                                @endphp
                                @foreach($datas as $data)
                                @php
                                    $total_amount += $data->salePlans()->sum('amount');
                                    $total_qty += $data->salePlans()->sum('quantity');
                                @endphp
                                <tr>
                                    <td class="tA-le">{{ $data->part_number }}</td>
                                    <td class="tA-le">{{ $data->product_name }}</td>
                                    <td class="tA-ri">かんばん</td>
                                    <td class="tA-ri">{{ $data->salePlans()->sum('quantity') }}</td>
                                    <td class="tA-ri">{{ $data->salePlans->amount }}</td>
                                    <td class="tA-ri">{{ $data->salePlans()->sum('amount') }}</td>
                                </tr>
                                @endforeach
                                <tr>
                                    <td class="tA-le text-center" colspan="3">合計</td>
                                    <td class="tA-le">{{ $total_qty }}</td>
                                    <td class="tA-le"></td>
                                    <td class="tA-le">{{ $total_amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchMachineNumberModal',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_number',
        'resultNameElementId' => 'machine_number_name',
        'model' => 'MachineNumber'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'item_code',
        'resultNameElementId' => 'item_name',
        'model' => 'Item'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'product_number_number',
        'resultNameElementId' => 'product_number_name',
        'model' => 'ProductNumber',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProjectModal',
        'searchLabel' => 'プロジェクトNo.',
        'resultValueElementId' => 'project_code',
        'resultNameElementId' => 'project_number_name',
        'model' => 'Project',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '使用先.',
        'resultValueElementId' => 'where_to_use_code',
        'resultNameElementId' => 'where_to_use_name',
        'model' => 'Customer',
    ])
    @php
    $configs = [
        'Supplier' => 'supplier_name',
        'MachineNumber' => 'machine_number_name',
        'Department' => 'department_name',
        'Line' => 'line_name',
        'Item' => 'item_name',
        'ProductNumber' => 'product_number_name',
        'Project' => 'project_name',
        'Customer' => 'where_to_use_name'
    ];
    
    foreach ($configs as $key => $reference) {
        $dataConfigs[$key] = [
            'model' => $key,
            'reference' => $reference
        ];
    }
    @endphp
    
    <x-search-on-input :dataConfigs="$dataConfigs" />
@endsection
@push('scripts')
        <script>
            $("#calculate_amount").on('click', function () {
                let amount = $("#unit_price").val() * $("#quantity").val();
                $("#amount").val(amount);
            });
        </script>
@endpush
