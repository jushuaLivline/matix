@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/outsources/outsourced_processing_order.css')
@endpush

@section('title', '発注データ一覧')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>発注データ一覧</span></h1>
            </div>

            <div id="successUpdate" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">発注データの更新が完了しました</div>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>
            
            <div class="tableWrap borderLesstable inputFormArea">
                <form id="orderDataListForm" class="overlayedSubmitForm with-js-validation" data-disregard-empty="true">
                    <table class="tableBasic" style="width: 100%; margin-bottom:50px;">
                        <tbody>
                            <tr>
                                <!-- 得意先 -->
                                <td style="max-width: 340px;">
                                    <dl class="formsetBox">
                                        <dt>仕入先</dt>
                                        <dd>
                                            <div class="d-flex">
                                                <input type="text" id="supplier_code" 
                                                    data-field-name="仕入先"
                                                    data-validate-exist-model="customer"
                                                    data-validate-exist-column="customer_code"
                                                    data-inputautosearch-model="supplier"
                                                    data-inputautosearch-column="customer_code"
                                                    data-inputautosearch-return="supplier_name_abbreviation"
                                                    data-inputautosearch-reference="supplier_name"
                                                    name="supplier_code" style="width:100px; margin-right: 10px;" 
                                                    value="{{ request()->get('supplier_code') }}">
                                                <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ request()->get('supplier_name') }}" style="margin-right: 10px;">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchSupplierModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div data-error-container="supplier_code"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td  style="max-width: 600px;">
                                    <dl class="formsetBox">
                                        <dt>製品品番</dt>
                                        <dd>
                                            <p class="formPack  mr-1">
                                                <input type="text" name="product_code" id="product_code" 
                                                data-field-name="製品品番"
                                                    data-validate-exist-model="ProductNumber"
                                                    data-validate-exist-column="part_number"
                                                    data-inputautosearch-model="ProductNumber"
                                                    data-inputautosearch-column="part_number"
                                                    data-inputautosearch-return="product_name"
                                                    data-inputautosearch-reference="product_name"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    value="<?php echo e(Request::get('product_code')); ?>"
                                                    class="w-130c">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly value="<?php echo e(Request::get('product_name')); ?>"
                                                    class="middle-name" name="product_name" id="product_name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchProductNumberModal">
                                                    <img src="<?php echo e(asset('images/icons/magnifying_glass.svg')); ?>"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div data-error-container="product_code"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <!-- 見積依頼日 -->
                                <td>
                                    <dl class="formsetBox">
                                        <dt class="">指示日</dt>
                                        <dd>
                                            
                                            @php
                                                $startDate = Request::get('instruction_date_from', date('Ymt')) ;
                                                $endDate = Request::get('instruction_date_to', date('Ymt')) ;
                                            @endphp
                                            <div class="d-flex">
                                                @include('partials._date_picker', [
                                                        'inputName' => 'instruction_date_from', 
                                                        'attributes' => 'data-error-messsage-container=#date_error_message', 
                                                        'inputClass' => 'w-130c mr-2',
                                                        'value' => $startDate,
                                                        'enableDateStart' => true,
                                                        'enableDateEnd' => true,
                                                        'required' => false
                                                        ])
                                                <span style="font-size:24px; padding:5px 10px;"> ~ </span>
                                                @include('partials._date_picker', [
                                                        'inputName' => 'instruction_date_to', 
                                                        'attributes' => 'data-error-messsage-container=#date_error_message', 
                                                        'inputClass' => 'w-130c mr-2',
                                                        'vallue' => $endDate,
                                                        'enableDateStart' => true,
                                                        'enableDateEnd' => true,
                                                        'required' => false
                                                        ])
                                                </div>
                                                <div id="date_error_message" style="width: 100%;"></div>
                                            </div>
                                        </dd>
                                    </dl>
                                </td>
                                <!-- 便No -->
                                <td>
                                    <dl class="formsetBox">
                                        <dt class="">便No</dt>
                                        <dd>
                                            <p class="formPack fixedWidth mr-1">
                                                <input type="text"
                                                    id=""
                                                    classs="acceptNumericOnly"
                                                    style="width: 40px !important"
                                                    name="order_number_start"
                                                    value="{{ old('order_number_start', Request::get('order_number_start') ?? '') }}">
                                            </p>
                                            <p class="formPack">～</p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text"
                                                    id=""
                                                    classs="acceptNumericOnly"
                                                    style="width: 40px !important"
                                                    name="order_number_end"
                                                    value="{{ old('order_number_end', Request::get('order_number_end') ?? '') }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary btn-wide" 
                        data-clear-inputs
                        data-clear-form-target="#orderDataListForm">
                            クリア
                        </button>
                        <button type="submit" class="btn btn-primary btn-wide" type="submit">
                            検索
                        </button>
                    </div>
                    <a href="{{ route('outsource.order.export.csv', Request::all()) }}" type="button" 
                                class="float-right btn btn-success {{ $outsourcedProcesses->total() == 0 ? 'btn-disabled' : '' }}" 
                                id="exportBtn"
                                style="margin-top:-40px;">検索結果をEXCEL出力</a>
                </form>
                
            </div>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>

     
            <div class="tableWrap bordertable" style="clear: both;">
                @if ($outsourcedProcesses && $outsourcedProcesses->total() > 0)
                    <ul class="headerList">
                        <li>{{ $outsourcedProcesses->total() }}件中、{{ $outsourcedProcesses->firstItem() }}件～{{ $outsourcedProcesses->lastItem() }} 件を表示してます</li>
                    </ul>
                @endif

                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th>指示日</th>
                        <th>管理No.</th>
                        <th>製品品番</th>
                        <th>仕入先コード</th>
                        <th rowspan="2">発注区分</th>
                        <th rowspan="2">背番号</th>
                        <th rowspan="2">枚数</th>
                        <th rowspan="2">収容数</th>
                        <th rowspan="2">数量</th>
                        <th rowspan="2" width="130px">操作</th>
                    </tr>
                    <tr>
                        <th>便No.</th>
                        <th>枝番</th>
                        <th>品名</th>
                        <th>仕入先名</th>
                    </tr>
                    @forelse ($outsourcedProcesses as $outsourcedProcess)

                        <tr data-id="{{ $outsourcedProcess->id }}" data-order-classification="{{ $outsourcedProcess->order_classification }}">
                            <td class="tA-cn" style="width: 150px;">
                                <div class="center">
                                    <input type="text" name="instruction_date" style="text-align: center" 
                                        id="instruction_date_{{ $outsourcedProcess->id }}" 
                                        data-format="YYYYMMDD"
                                        minlength="8"
                                        maxlength="8"
                                        pattern="\d*" 
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        value="{{ $outsourcedProcess->instruction_date->format('Ymd') }}"
                                        old="{{ $outsourcedProcess->instruction_date->format('Ymd') }}"
                                        disabled>
                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1 btn-disabled" 
                                            data-target="instruction_date_{{ $outsourcedProcess->id }}"
                                            data-format="YYYYMMDD"
                                            disabled>
                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                    </button>
                                </div>
                            </td>
                            <td class="tA-ri" style="width: 150px;">
                                <input type="number" name="management_no" 
                                value="{{ $outsourcedProcess->management_no ?? '' }}" 
                                old="{{ $outsourcedProcess->management_no ?? '' }}" 
                                disabled>   
                            </td>
                            <td class="tA-ri">
                                <div class="center">
                                    <input type="text" id="product_code_{{ $outsourcedProcess->id }}" name="product_code" 
                                        value="{{ $outsourcedProcess->product_code }}" 
                                        old="{{ $outsourcedProcess->product_code }}" 
                                        class="" disabled>
                                    <button type="button" class="btnSubmitCustom js-modal-open btn-disabled"
                                            data-target="searchProductModal_{{ $outsourcedProcess->id }}" disabled>
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </td>
                            <td class="tA-ri">
                                <div class="center">
                                    <input type="text" id="supplier_code_{{ $outsourcedProcess->id }}" name="supplier_code"     
                                            value="{{ $outsourcedProcess->supplier_code }}" 
                                            old="{{ $outsourcedProcess->supplier_code }}" 
                                            class="" disabled>
                                    <button type="button" class="btnSubmitCustom js-modal-open btn-disabled"
                                            data-target="searchSupplierModal_{{ $outsourcedProcess->id }}"
                                            disabled>
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </td>
                            <td rowspan="2" class="tA-cn" style="width: 80px;" >
                                @if($outsourcedProcess->order_classification == 1)
                                    通常
                                @elseif($outsourcedProcess->order_classification == 2)
                                    臨時
                                @elseif($outsourcedProcess->order_classification == 3)
                                    端数指示
                                @else
                                    随時
                                @endif

                            </td>
                            <td rowspan="2" class="tA-cn" style="width: 70px;" >
                                {{ optional($outsourcedProcess->product)->uniform_number ?? '' }}    
                            </td>
                            <td rowspan="2" class="tA-cn" style="width: 80px;" >
                                <input name="instruction_kanban_quantity" 
                                value="{{ $outsourcedProcess->instruction_kanban_quantity }}" 
                                old="{{ $outsourcedProcess->instruction_kanban_quantity }}" 
                                
                                disabled>
                            </td>
                            <td rowspan="2" class="tA-cn" style="width: 100px;" >
                                <input name="arrival_number" value="{{ $outsourcedProcess->arrival_number}}" disabled>
                            </td>
                            <td rowspan="2" class="tA-cn" style="width: 100px;" >
                                <input name="arrival_quantity" value="{{ $outsourcedProcess->arrival_quantity }}" disabled>
                            </td>
                            <td rowspan="2" class="tA-cn">
                                <div class="center" id="EditDelete">
                                    <button class="btn btn-block btn-blue" id="edit"
                                        data-input-enable>編集</button>
                                    <button class="btn btn-block btn-orange" 
                                        style="margin-left: 2px" id="delete"
                                        data-input-delete
                                        data-id="{{ $outsourcedProcess->id }}">削除</button>
                                </div>
                                
                                <div class="center" id="UdpateUndo" style="display: none;">
                                    <button class="btn btn-block btn-green" 
                                        id="update"
                                        data-input-update
                                        data-id="{{ $outsourcedProcess->id }}">更新</button>
                                    <button  class="btn btn-block btn-gray" 
                                        style="margin-left: 1px" 
                                        id="undo"
                                        data-input-cancel>取消</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="tA-ri">
                                <input type="number" name="incoming_flight_number"  id="incoming_flight_number_{{ $outsourcedProcess->id }}"  
                                value="{{ $outsourcedProcess->incoming_flight_number }}" 
                                old="{{ $outsourcedProcess->incoming_flight_number }}" 
                                disabled>
                            </td>
                            <td class="tA-ri">{{ $outsourcedProcess->branch_number ?? '' }}</td>
                            <td class="tA-le">
                                <input type="text" readonly
                                    id="product_name_{{ $outsourcedProcess->id }}"
                                    name="product_name"
                                    value="{{ optional($outsourcedProcess->product)->product_name }}"
                                    old="{{ optional($outsourcedProcess->product)->product_name }}"
                                    class="middle-name text-left">
                            </td>
                            <td class="tA-le">
                                <input type="text" readonly
                                    id="supplier_name_{{ $outsourcedProcess->id }}"
                                    name="supplier_name"
                                    value="{{ optional($outsourcedProcess->supplier)->customer_name }}"
                                    old="{{ optional($outsourcedProcess->supplier)->customer_name }}"
                                    class="middle-name text-left">
                            </td>
                        </tr>

                        @include('partials.modals.masters._search', [
                            'modalId' => 'searchProductModal_'. $outsourcedProcess->id,
                            'searchLabel' => '品番',
                            'resultValueElementId' => 'product_code_'. $outsourcedProcess->id,
                            'resultNameElementId' => 'product_name_'. $outsourcedProcess->id,
                            'model' => 'ProductNumber'
                        ])
                        @include('partials.modals.masters._search', [
                            'modalId' => 'searchSupplierModal_'. $outsourcedProcess->id,
                            'searchLabel' => '仕入先',
                            'resultValueElementId' => 'supplier_code_'. $outsourcedProcess->id,
                            'resultNameElementId' => 'supplier_name_'. $outsourcedProcess->id,
                            'model' => 'Supplier'
                        ])
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">検索結果はありません</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $outsourcedProcesses->appends(request()->all())->links() }}
           
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
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

@php
$dataConfigs['Supplier'] = [
    'model' => 'Supplier',
    'reference' => 'supplier_name'
];
$dataConfigs['ProductMaterial'] = [
    'model' => 'ProductMaterial',
    'reference' => 'product_name'
];
@endphp

<x-search-on-input :dataConfigs="$dataConfigs" />
@endsection
@push('scripts')
@vite('resources/js/outsource/order/index.js')
@endpush
