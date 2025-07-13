@extends('layouts.app')

@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')
    <style>
        .calendar-plugin input {
            text-align: left;
            width: 6rem !important;
        }
        .btnExport {
            cursor: pointer;
        }
    </style>
@endpush

@php
    $purchase_category = request()->get('purchase_category');
    $previous_data = session('previous_data');
@endphp

@section('title', "購買品購入実績入力")

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>購買品購入実績入力 </span></h1>
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="pagettlWrap">
                <h1><span>購買品購入実績入力</span></h1>
            </div>
           
            <form id="form_request" class="overlayedSubmitForm with-js-validation" action="{{  route('purchase.purchaseItem.store') }}" method="POST" accept-charset="utf-8"
                data-confirmation-message="購買品購入実績情報を登録します、よろしいでしょうか？">
                @csrf
                <input type="hidden" name="purchase_category" value="2">
                <input type="hidden" name="creator" value="{{auth()->user()->employee_code}}">
                <div class="box">
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">伝票区分</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="1" {{((request()->get('voucher_class') ?? 1) == 1) ? 'checked' : ''}}>
                                        <span>購入</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="6" {{((request()->get('voucher_class') ?? 1) == 6) ? 'checked' : ''}}>
                                            <span>修正・返品</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="9" {{((request()->get('voucher_class') ?? 1) == 9) ? 'checked' : ''}}>
                                            <span>値引</span>
                                    </label>
                                </p>
                            </div>
                            @error('voucher_class')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="mb-4">
                        <div class="" style="width: 30%">
                            <label class="form-label dotted indented">入荷日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                    'inputName' => 'arrival_date', 
                                    'value' => request()->get('arrival_date') ?? ''
                                ])
                            </div>
                            @error('arrival_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">仕入先</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="supplier_code"
                                       id="supplier_code"
                                       data-field-name="仕入先"
                                       data-error-messsage-container="#supplier_code_error"
                                       data-validate-exist-model="supplier"
                                       data-validate-exist-column="customer_code"
                                       data-inputautosearch-model="supplier"
                                       data-inputautosearch-column="customer_code"
                                       data-inputautosearch-return="supplier_name_abbreviation"
                                       data-inputautosearch-reference="supplier_name"
                                       class="text-left searchOnInput Supplier w-100c mr-10c"
                                       minlength="6"
                                       maxlength="6"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       value= "{{ request()->get('supplier_code') ?? ''}}"
                                       required
                                    >
                                <input type="text" readonly
                                       name="supplier_name"
                                       id="supplier_name"
                                       value= "{{ request()->get('supplier_name') ?? ''}}"
                                       class="middle-name text-left w-290c mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="supplier_code_error"></div>
                        </div>
                    </div>
            
                    <div class="mb-4">
                        <div class="mr-3">
                            <label class="form-label dotted indented">機番</label>
                            <div class="d-flex">
                                <input type="text" name="machine_number" id="machine_number"
                                    data-field-name="機番"
                                    data-error-messsage-container="#machine_number_error"
                                    data-validate-exist-model="MachineNumber" 
                                    data-validate-exist-column="machine_number"
                                    data-inputautosearch-model="MachineNumber" 
                                    data-inputautosearch-column="machine_number"
                                    data-inputautosearch-return="machine_number_name" 
                                    data-inputautosearch-reference="machine_number_name"
                                    class="text-left w-100c mr-10c" 
                                    maxlength="5"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value= "{{ request()->get('machine_number') ?? ''}}"
                                >
                                <input type="text" name="machine_number2" id="machine_number2" class="text-left mr-10c"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    maxlength="1"
                                    style="width: 35px;"
                                    value= "{{ request()->get('machine_branch_number') ?? ''}}"
                                >
                                <input type="text" readonly name="machine_number_name" id="machine_number_name"
                                    value= "{{ request()->get('machine_number_name') ?? ''}}"
                                    class="middle-name text-left w-290c mr-10c">
                                <button type="button" class="btnSubmitCustomMachineNumber js-modal-open"
                                    data-target="searchMachineNumberModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="machine_number_error"></div>
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                <input type="text" name="department_code"
                                       id="department_code"
                                       data-field-name="部門"
                                        data-error-messsage-container="#department_code_error"
                                       data-validate-exist-model="Department"
                                       data-validate-exist-column="code"
                                       data-inputautosearch-model="Department"
                                       data-inputautosearch-column="code"
                                       data-inputautosearch-return="name"
                                       data-inputautosearch-reference="department_name"
                                       class="text-left w-100c mr-10c"
                                       minlength="6"
                                       maxlength="6"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       value= "{{ request()->get('department_code') ?? ''}}"
                                >
                                <input type="text" readonly
                                       name="department_name"
                                       id="department_name"
                                       value= "{{ request()->get('department_name') ?? ''}}"
                                       class="middle-name text-left w-290c mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="department_code_error"></div>
                        </div>
                    </div>
            
                    <div class="mb-4">
                        <label class="form-label dotted indented">ライン</label>
                        <div class="d-flex">
                            <input type="text" name="line_code"
                                    data-field-name="ライン"
                                    data-error-messsage-container="#line_code_error"
                                    data-validate-exist-model="Line"
                                    data-validate-exist-column="line_code"
                                    data-inputautosearch-model="line"
                                    data-inputautosearch-column="line_code"
                                    data-inputautosearch-return="line_name"
                                    data-inputautosearch-reference="line_name"
                                    id="line_code"
                                    class="text-left w-150c mr-10c"
                                    minlength="3"
                                    maxlength="3"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value= "{{ request()->get('line_code') ?? ''}}"
                            >
                            <input type="text" readonly
                                    name="line_name"
                                    id="line_name"
                                    value= "{{ request()->get('line_name') ?? ''}}"
                                    class="middle-name text-left w-290c mr-10c">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchLineModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div id="line_code_error"></div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">費目</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="item_code" id="item_code" class="text-left mr-10c"
                                    data-field-name="費目"
                                    data-error-messsage-container="#item_code_error"
                                    data-validate-exist-model="Item" 
                                    data-validate-exist-column="expense_item"
                                    data-inputautosearch-model="Item" 
                                    data-inputautosearch-column="expense_item"
                                    data-inputautosearch-return="item_name" 
                                    data-inputautosearch-reference="item_name"
                                    class="text-left" 
                                    maxlength="3"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value= "{{ request()->get('item_code') ?? ''}}"
                                    style="width: 80px" required>
                                <input type="text" readonly name="item_name" id="item_name"
                                    value= "{{ request()->get('item_name') ?? ''}}"
                                    class="middle-name text-left mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchItemModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="item_code_error"></div> 
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">品番</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" id="part_number" 
                                    name="part_number" 
                                    data-field-name="品番"
                                    data-error-messsage-container="#part_number_error"
                                    style="width: 100%;" 
                                    maxlength="100"
                                    value= "{{ request()->get('part_number') ?? ''}}"
                                    required>
                            </div>
                            <div id="part_number_error"></div> 
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">品名</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="product_name"
                                    id="product_name"
                                     data-field-name="品名"
                                    data-error-messsage-container="#product_name_error"
                                    class="text-left w-190c"
                                    value= "{{ request()->get('product_name') ?? ''}}"
                                    required>
                            </div>
                            <div id="product_name_error"></div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" name="standard"
                                    id="standard"
                                    data-field-name="規格"
                                    data-error-messsage-container="#standard_error"
                                    class="text-left w-190c"
                                    value= "{{ request()->get('standard') ?? ''}}"
                                >
                            </div>
                            <div id="standard_error"></div>
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">使用先</label>
                            <div class="d-flex">
                                <input type="text" name="where_to_use_code" id="where_to_use_code"
                                    data-field-name="使用先"
                                    data-error-messsage-container="#where_to_use_code_error"
                                    data-validate-exist-model="Customer" 
                                    data-validate-exist-column="customer_code"
                                    data-inputautosearch-model="Customer" 
                                    data-inputautosearch-column="customer_code"
                                    data-inputautosearch-return="customer_name" 
                                    data-inputautosearch-reference="where_to_use_name"
                                    class="text-left mr-10c w-100c" 
                                    style="margin-right: 15px;"
                                    maxlength="6"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ request()->get('where_to_use_code') ?? ''}}">
                                <input type="text" readonly name="where_to_use_name" id="where_to_use_name"
                                    value="{{ request()->get('where_to_use_name') ?? ''}}"
                                    class="middle-name text-left mr-10c"
                                    style="width: 300px;">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchCustomerModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="where_to_use_code_error"></div>
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">数量</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="number" 
                                    data-field-name="数量"
                                    data-error-messsage-container="#quantity_error"
                                    name="quantity"
                                    id="quantity"
                                    class="text-left w-100c mr-10c"
                                    min="1"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ request()->get('quantity') ?? ''}}"
                                    required>
                            </div>
                            <div id="quantity_error"></div>
                        </div>
            
                        <div class="mr-3">
                           <label class="form-label dotted indented">単位</label>
                           <div class="d-flex">
                               <select class="" name="unit_code" id="unit_code" style="width: 100%; height: 40px"
                                    data-field-name="単位"
                                    data-error-messsage-container="#unit_code_error">
                                   @foreach ($codes as $code)
                                       @if ($code->code == request()->get('unit_code'))
                                           <option value="{{ $code->code }}" selected>{{ $code->name }}</option>
                                       @else
                                           <option value="{{ $code->code }}">{{ $code->name }}</option>
                                       @endif
                                   @endforeach
                               </select>
                           </div>
                           <div id="unit_code_error"></div>
                       </div>
                   </div>
            
                   <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">単価</label> <span class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" 
                                name="unit_price" 
                                data-field-name="単価"
                                data-error-messsage-container="#unit_price_error"
                                id="unit_price" 
                                class="text-right" 
                                style="width: 120px;"
                                value="{{ request()->get('unit_price') ?? '' }}"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                required>
                        </div>
                        <div id="unit_price_error"></div>
                    </div>
                
                    <div class="mr-3">
                        <label class="form-label dotted indented">金額</label>
                        <div class="d-flex">
                            <input type="text" 
                                readonly 
                                data-field-name="金額"
                                data-error-messsage-container="#amount_of_money_error"
                                name="amount_of_money" 
                                id="amount_of_money"
                                value="{{ request()->get('amount_of_money') ?? '' }}"
                                class="middle-name text-right w-150c mr-10c">
                        </div>
                        <div id="amount_of_money_error"></div>
                    </div>
                </div>
            
                   <div class="mb-4 d-flex">
                       <div class="mr-3">
                           <label class="form-label dotted indented">課税区分</label> <span class="others-frame btn-orange badge">必須</span>
                           <div class="d-flex">
                               <p class="formPack radioSale">
                                   <label class="radioBasic">
                                       <input type="radio" 
                                        name="tax_classification" 
                                        data-field-name="課税区分"
                                        data-error-messsage-container="#tax_classification_error"
                                        value="1" {{ (request()->get('tax_classification') ?? 1) == 1 ? 'checked' : '' }} required>
                                       <span>課税</span>
                                   </label>
                               </p>
                               <p class="formPack radioSale">
                                   <label class="radioBasic">
                                       <input type="radio" 
                                        name="tax_classification" 
                                        data-field-name="課税区分"
                                        data-error-messsage-container="#tax_classification_error"
                                        value="2" {{ (request()->get('tax_classification') ?? 1) == 2 ? 'checked' : '' }}>
                                       <span>非課税</span>
                                   </label>
                               </p>
                           </div>
                           <div id="tax_classification_error"></div>
                       </div>
                   </div>
            
                   <div class="mb-4 d-flex">
                       <div class="mr-3">
                           <label class="form-label dotted indented">伝票No.</label>
                           <div class="d-flex">
                               <input type="text" name="slip_code"
                                      id="slip_code"
                                      data-field-name="伝票No"
                                        data-error-messsage-container="#slip_code_error"
                                      class="text-left mr-10c"
                                      value="{{ request()->get('slip_code') }}">
                           </div>
                           <div id="slip_code_error"></div>
                       </div>
                   </div>
            
                   <div class="mb-4">
                       <label class="form-label dotted indented">プロジェクトNo.</label>
                       <div class="d-flex">
                           <input type="text" name="project_code" id="project_code" class="text-left mr-10c"
                                data-field-name="プロジェクトNo."
                                data-error-messsage-container="#project_number_error"
                               data-validate-exist-model="Project" 
                               data-validate-exist-column="project_number"
                               data-inputautosearch-model="Project" 
                               data-inputautosearch-column="project_number"
                               data-inputautosearch-return="project_name" 
                               data-inputautosearch-reference="project_name"
                               class="text-left" 
                               maxlength="8"
                               style="width: 120px;" value="{{ request()->get('project_code') }}">
                           <input type="text" readonly name="project_name" id="project_name"
                               value="{{ request()->get('project_name') }}"
                               class="middle-name text-left mr-10c">
                           <button type="button" class="btnSubmitCustom js-modal-open"
                               data-target="searchProjectModal">
                               <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                   alt="magnifying_glass.svg">
                           </button>
                       </div>
                       <div id="project_number_error"></div>
                   </div>
            
                   <div class="mb-2 d-flex">
                       <div class="mr-3">
                           <label class="form-label dotted indented">備考</label>
                           <div class="d-flex">
                               <textarea rows="5" cols="100" 
                                    type="text" 
                                    data-field-name="備考"
                                data-error-messsage-container="#remarks_error"
                                    name="remarks" id="remarks" value="" class=""
                                   placeholder="">{{request()->get('remarks')}}</textarea>
                           </div>
                           <div id="remarks_error"></div>
                       </div>
                   </div>
            
                   <div class="error_msg"></div>
               </div>
            
               <div class="btnListContainer">
                   <div class="btnContainerMain justify-content-flex-end">
                       <div class="btnContainerMainRight">
                           <a type="button" class="btn btn-blue" href="{{ route('purchase.purchaseActual.index',[
                                'arrival_date_start' => now()->startOfMonth()->format('Ymd'),
                                'arrival_date_end' => now()->endOfMonth()->format('Ymd'),
                                'voucher_class' => 0,
                                'category' => 2,
                           ]) }}">
                               一覧に戻る
                           </a>
                           <a type="button" id="clear_form" class="btn btn-blue">
                               クリア
                           </a>
                           <button type="submit" class="btn btn-green" id="submit_button">
                               この内容で更新する
                           </button>
                       </div>
                   </div>
               </div>
            </form>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.machine_numbers._search', [
        'modalId' => 'searchMachineNumberModal',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_number',
        'resultNameElementId' => 'machine_number_name',
        'resultBranchNumberEle' => 'machine_number2',
        'model' => 'MachineNumber',
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
        'resultNameElementId' => 'project_name',
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
    @vite('resources/js/purchase/actual/edit.js')
@endpush