@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/common.css')
    @vite('resources/css/purchase/order/process/detail.css')
@endpush

@php
    $rejectParams = array_merge([$item->id], collect(request()->query())->except('state_classification')->toArray());
    $updateParams = array_merge([$item->id], request()->query());
@endphp

@section('title', '発注詳細')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>発注詳細</span>
                </h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>発注詳細</span></h1>
            </div>

            <form id="approvDetailsForm" data-id={{$item->id}}>
                <div class="tableWrap borderLesstable inputFormArea">
                        @csrf
                        @method('PUT')
                        {{-- <input type="hidden" name="state_classification" value="3"> --}}
                        <input type="hidden" name="purchase_order_details_number" value="1">
                        <input type="hidden" name="purchase_order_number" value="{{ now()->format('ymd').'AA' }}">
                        <input type="hidden" name="order_date" value="{{ now()->format('Ymd') }}">

                        <table class="tableBasic">
                            <tbody>
                                <tr>
                                    <td width="40%">
                                        <dl class="formsetBox">
                                            <dt>購買依頼No.</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw75p mt-1">
                                                    <input type="text" name="item_id" value="{{ $item->id }}" class="ExcludeFromClear" hidden>
                                                    <input type="text" name="requisition_number"class="w-100 ExcludeFromClear"
                                                        value="{{ $item->requisition_number }}" readonly>
                                                </p>
                                                <div class="error_msg"></div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%">
                                        <dl class="formsetBox">
                                            <dt>依頼日</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw75p mt-1">
                                                    <input type="hidden" name="requested_date" class="w-100 ExcludeFromClear"
                                                    value="{{ $item->requested_date?->format('Ymd') }}">
                                                    <input type="text" class="w-100 ExcludeFromClear"
                                                    value="{{ $item->requested_date?->format('m/d/Y') }}" readonly>
                                                </p>
                                                <div class="error_msg"></div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="40%">
                                        <dl class="formsetBox">
                                            <dt>依頼者</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw75p mt-1">
                                                    <input type="text" name="creator" value="{{ $item->creator }}" class="w-100 ExcludeFromClear" hidden>
                                                    <input type="text" value="{{ $item->employee?->employee_name }}" class="w-100 ExcludeFromClear"
                                                    readonly>
                                                </p>
                                                <div class="error_msg"></div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="28.8%%">
                                        <dl class="formsetBox">
                                            <dt class="requiredForm">部門</dt>
                                            <dd>
                                                <div class="d-flex mt-1">
                                                    <input type="text" name="department_code"
                                                        id="department_code"
                                                        class="text-left w-100c mr-10c acceptNumericOnly fetchQueryName"
                                                        data-model="Department"
                                                        data-query="code"
                                                        data-query-get="department_name"
                                                        data-reference="department_name"
                                                        maxlength="6"
                                                        value="{{ $item->department_code }}"
                                                        >        
                                                    <input type="text" readonly
                                                        name="department_name"
                                                        id="department_name"
                                                        value="{{ $item->department?->name }}"
                                                        class="middle-name text-left w-400c mr-10c">
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchDepartmentModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="28.8%%">
                                        <dl class="formsetBox">
                                            <dt>ライン</dt>
                                            <dd>
                                                <div class="d-flex mt-1">
                                                    <input type="text" name="line_code"
                                                        id="line_code"
                                                        class="text-left w-100c mr-10c acceptNumericOnly fetchQueryName"
                                                        data-model="Line"
                                                        data-query="line_code"
                                                        data-query-get="line_name"
                                                        data-reference="line_name"
                                                        maxlength="3"
                                                        value="{{ $item->line_code }}" >
                                                    <input type="text" readonly
                                                        name="line_name"
                                                        id="line_name"
                                                        value="{{ $item->line?->line_name}}"
                                                        class="middle-name text-left w-400c mr-10c">
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchLineModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        <dl class="formsetBox">
                                            <dt class="requiredForm">品番</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                    <input type="text" name="part_number" id="part_number" value="{{ $item->part_number }}" class="w-250c">
                                                </p>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td width="15%">
                                        <dl class="formsetBox">
                                            <dt class="requiredForm">品名</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                    <input type="text" name="product_name"
                                                        id="product_name"
                                                        class="text-left w-250c"
                                                        value="{{ $item->product_name }}">
                                                </p>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td width="15%">
                                        <dl class="formsetBox">
                                            <dt>規格</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                    <input type="text" name="standard"
                                                        id="standard"
                                                        class="text-left w-300c"
                                                        value="{{ $item->standard}}" >
                                                </p>
                                                </dd>
                                            </dl>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="41.8%">
                                        <dl class="formsetBox">
                                            <dt>購入理由</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                    <input type="text" name="reason"
                                                        id="reason"
                                                        class="text-left w-400c"
                                                        value="{{ $item->reason}}" >
                                                </p>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="28.8%">
                                        <dl class="formsetBox">
                                            <dt>発注先</dt>
                                            <dd>
                                                <div class="d-flex mt-1">
                                                    <input type="text" name="supplier_code"
                                                        id="supplier_code" style="margin-right: 10px; width: 100px;"
                                                        class="text-left searchOnInput Supplier acceptNumericOnly fetchQueryName"
                                                        data-model="Customer"
                                                        data-query="customer_code"
                                                        data-query-get="supplier_name_abbreviation"
                                                        data-reference="supplier_name"
                                                        maxlength="6"
                                                        value="{{ $item->supplier_code }}">
                                                    <input type="text" readonly
                                                        name="supplier_name"
                                                        id="supplier_name"
                                                        value="{{ $item->supplier?->customer_name }}"
                                                        class="middle-name text-left w-400c mr-10c">
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchSupplierModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        <dl class="formsetBox">
                                            <dt class="requiredForm">数量</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                    <input type="text" name="quantity"
                                                        id="quantity"
                                                        class="text-left acceptNumericOnly w-120c"
                                                        maxlength="9"
                                                        data-accept-zero=true    
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                        value="{{  $item->quantity }}">
                                                </p>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td width="15%">
                                        <dl class="formsetBox">
                                            <dt>単位</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                    <select class="" name="unit_code" id="unit_code" style="width: 100%; height: 40px">
                                                        @foreach ($units as $unit)
                                                            <option value="{{ $unit->code }}"
                                                                @if ($item->unit_code == $unit->code) selected @endif>
                                                                {{ $unit->name }}</option>
                                                            
                                                        @endforeach
                                                    </select>
                                                </p>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%">
                                        <dl class="formsetBox">
                                            <dt class="requiredForm">単価</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                    <input type="text" name="unit_price"
                                                        id="unit_price"
                                                        class="text-left acceptNumericOnly w-100c mr-10c"
                                                        minlength="1"
                                                        data-accept-zero="true"
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                        value="{{ $item->unit_price }}">
                                                </p>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td width="15%">
                                        <dl class="formsetBox">
                                            <dt>金額</dt>
                                            <dd>
                                                <div class="d-flex mt-1">
                                                    <input type="text" readonly
                                                        name="amount_of_money"
                                                        id="amount_of_money"
                                                        value="{{ $item->amount_of_money }}"
                                                        class="middle-name text-left" style="width: 100%;">
                                                </div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="28.8%">
                                        <dl class="formsetBox">
                                            <dt>費目</dt>
                                            <dd>
                                                <div class="d-flex mt-1">
                                                    <input type="text" name="expense_items"
                                                        id="item_code" style="margin-right: 10px; width: 100px;"
                                                        class="text-left searchOnInput Item acceptNumericOnly fetchQueryName"
                                                        data-model="Item"
                                                        data-query="expense_item"
                                                        data-query-get="item_name"
                                                        data-reference="item_name"
                                                        maxlength="3"
                                                        value="{{ $item->expense_items }}">
                                                    <input type="text" readonly
                                                        name="item_name"
                                                        id="item_name"
                                                        value="{{ $item->expense?->item_name }}"
                                                        class="middle-name text-left w-400c mr-10c">
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchItemModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>

                                <tr>
                                    <td width="28.8%">
                                        <dl class="formsetBox">
                                            <dt>納期</dt>
                                            <dd>
                                                <div class="d-flex mt-1">
                                                    @include('partials._date_picker', ['inputName' => 'deadline', 
                                                    "value" => $item->deadline?->format("Ymd"), 
                                                    'disabledPreviousDates' => false,
                                                    'inputClass' => 'w-220c w-100',
                                                    'attributes' => 'data-error-messsage-container=#request_error_message'])
                                                </div>
                                                <div id="request_error_message"></div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="13.2%%">
                                        <dl class="formsetBox">
                                            <dt>見積書</dt>
                                            <dd>
                                                <div class="d-flex mt-1">
                                                    <label class='mr-1'>
                                                        <input type="radio" name="quotation_existence_flag" value="0"
                                                            @if ($item->quotation_existence_flag == '0') checked @else checked @endif>無し
                                                    </label>
                                                    <label class='mr-1'>
                                                        <input type="radio" name="quotation_existence_flag" value="1"
                                                            @if ($item->quotation_existence_flag == '1') checked @endif>有り
                                                    </label>
                                                </div>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td>
                                        <td width="15%">
                                            <dl class="formsetBox">
                                                <dt>承認方法</dt>
                                                <dd>
                                                    <div class="d-flex mt-1" style="width: 200px">
                                                        <label class='mr-1'>
                                                            <input type="radio" name="approval_method_category" value="1"
                                                                @if ($item->approval_method_category == '1') checked @else checked @endif>システム
                                                        </label>
                                                        <label class='mr-1'>
                                                            <input type="radio" name="approval_method_category" value="2"
                                                                @if ($item->approval_method_category == '2') checked @endif>依頼書
                                                        </label>
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="41.8%">
                                        <dl class="formsetBox">
                                            <dt>備考</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p mt-1">
                                                <input type="text" name="remarks"
                                                value="{{ $item->remarks }}">
                                                </p>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                

                    <table class="table table-bordered text-center ml-2" style="width: auto;">
                        <tbody>
                            <tr height="100">
                                @foreach ($approvals as $approval)
                                    <td width="150" class="approval-row" style="border: 1px solid #b7b7b7;">
                                        @if ($approval->denial_date != '')
                                            <p style="margin-top: 20px; color: red; font-weight: bold !important;" class="status_name">
                                                未承認
                                            </p>
                                            <p style="margin-top: 10px;">
                                                {{ date('Y/m/d', strtotime($approval->denial_date)) }}
                                            </p>
                                        @elseif ($approval->approval_date != '')
                                            <p style="margin-top: 20px; color: green; font-weight: bold !important;" class="status_name">
                                                承認
                                            </p>
                                            <p style="margin-top: 10px;">
                                                {{ date('Y/m/d', strtotime($approval->approval_date)) }}
                                            </p>
                                        @else
                                            <p style="margin-top: 20px; color: red; font-weight: bold !important;" class="status_name">
                                                未承認
                                            </p>
                                            <p style="margin-top: 10px;"><br></p>     
                                        @endif

                                        <p style="margin-top: 10px;">
                                            {{ $approval->employee->employee_name }}
                                        </p>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>                    
                </div>
                @if($approvals->whereNotNull('approval_date')->count() > 0)
                <div class="float-left">
                    <ul class="buttonlistWrap">
                        <li>
                           
                            <button data-target="returnModal" class="btn btn-primary js-modal-open"
                                style="max-width: 250px;">
                                この承認を差し戻す
                            </button>
                        </li>
                    </ul>
                </div>
                @endif
                <div class="float-right">
                    <ul class="buttonlistWrap">
                        <li>
                            <a href="{{ route('purchase.orderProcess.index', request()->query()) }}"
                                class="buttonBasic bColor-ok" style="max-width: 200px;">一覧に戻る</a>
                        </li>
                        {{-- <li>
                            <button type="button"  class="buttonBasic bColor-ok  btn btn-primary" style="max-width: 200px;"
                            data-clear-inputs
                            data-clear-form-target="#approvDetailsForm"
                            data-confirmation-message="本当にクリアしますか？">
                                クリア
                            </button>
                        </li> --}}
                        <li>
                            <button type="button" class="btn btn-danger" style="width: 200px;"
                                data-button-delete
                                data-item-id="{{ $item->id }}">
                                削　除
                            </button>
                        </li>
                        <li>
                            <button id="form-submit" class="btn btn-success" style="width: 190px;">
                                この内容で登録する
                            </button>
                        </li>
                    </ul>
                </div>
            </form>
        </div>
    </div>
   
    <div id="returnModal" class="modal js-modal modal__bg modalSs">
        <div class="modal__content modal_fix_width" style="overflow-x:hidden;">
            <div class="modalTitle">
                承認差戻
            </div>
            <div class="modalInner" style="margin-top: 50px;">
              
                <form action="{{ route('purchase.orderProcessDetail.reject', $rejectParams) }}" method="POST"
                    accept-charset="utf-8">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="state_classification" value="9">
                    <div class="section">
                        <div class="boxModal mb-1">
                            <label for="reason_for_denial">差し戻し理由</label>
                            <br />
                            <textarea id="reason_for_denial" name="reason_for_denial" style="width: 100%;" rows="3"></textarea>
                        </div>
                        <button type="button" class="btn btn-primary js-modal-close mt-2" style="width: 190px;">
                            閉じる
                        </button>
                        <button type="submit" class="btn btn-success mt-2 btn-disabled" style="width: 190px; margin-left: 10px;" disabled data-button-return>
                            差し戻し
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form id="delete-form-{{ $item->id }}" method="POST"
        action="{{ route('purchase.orderProcessDetail.destroy', [$item->id, ... Request::all()]) }}">
        @csrf
        @method('DELETE')
    </form>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'item_code',
        'resultNameElementId' => 'item_name',
        'model' => 'Item',
    ])
    
@endsection

@push('scripts')
    @vite('resources/js/purchase/order/process/detail/index.js')
@endpush