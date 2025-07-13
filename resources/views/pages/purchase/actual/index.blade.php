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

@section('title', '購入実績検索・一覧')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>購入実績一覧</span></h1>
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>
            <form accept-charset="utf-8" id="form_request" class="overlayedSubmitForm with-js-validation" data-disregard-empty="true">
                <div class="box mb-3">
                    <div class="mb-3 d-flex">
                        <div class="mr-5 w-35">
                            <label class="form-label dotted indented">入荷日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                    'inputName' => 'start_date',
                                    'inputClass' =>'wi-150c',
                                    'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=入荷日',
                                    'value' => $start_date
                                ])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', [
                                    'inputName' => 'end_date', 
                                    'inputClass' =>'wi-150c',
                                    'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=依頼者',
                                    'value' => $end_date
                                ])
                            </div>
                            <div id="request_error_message"></div>
                         </div>
                        <div class="mt-2 w-25">
                            <label class="form-label dotted indented">伝票区分</label>
                            <div class="d-flex">
                                @php
                                    $type = request()->voucher_class ?? 1;
                                @endphp
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="0" {{ $type == 0 ? 'checked' : '' }}>
                                        <span>すべて</span>
                                    </label>
                                </p>
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="1" {{ $type == 1 ? 'checked' : '' }}>
                                        <span>購入</span>
                                    </label>
                                </p>
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="6" {{ $type == 6 ? 'checked' : '' }}>
                                        <span>修正・返品</span>
                                    </label>
                                </p>
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="9" {{ $type == 9 ? 'checked' : '' }}>
                                        <span>値引</span>
                                    </label>
                                </p>
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">仕入先</label>
                            <div class="d-flex">
                                <input type="text" name="supplier_code"
                                       id="supplier_code"
                                       data-field-name="仕入先"
                                       data-validate-exist-model="supplier"
                                       data-validate-exist-column="customer_code"
                                       data-inputautosearch-model="supplier"
                                       data-inputautosearch-column="customer_code"
                                       data-inputautosearch-return="supplier_name_abbreviation"
                                       data-inputautosearch-reference="supplier_name"
                                       class="text-left searchOnInput Supplier  w-100c mr-10c"
                                       minlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                       maxlength="6"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       value="{{ request()->get('supplier_code') }}">
                                <input type="text" readonly
                                       name="supplier_name"
                                       id="supplier_name"
                                       value="{{ request()->get('supplier_name') }}"
                                       class="middle-name text-left w-290c mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div data-error-container="supplier_code"></div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        <div class="mr-4 w-50">
                            <label class="form-label dotted indented">機番</label>
                            <div class="d-flex">
                                <div class="d-flex" style="width:49%">
                                    <input type="text" id="machine_number_start" 
                                        name="machine_number_start" 
                                        data-field-name="機番"
                                        data-error-messsage-container = "#machine_code-error"
                                        data-validate-exist-model="MachineNumber" 
                                        data-validate-exist-column="machine_number"
                                        data-inputautosearch-model="MachineNumber" 
                                        data-inputautosearch-column="machine_number"
                                        data-inputautosearch-return="machine_number_name" 
                                        data-inputautosearch-reference="machine_name1"
                                        maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        class="mr-1 w-25"
                                        value="{{ request()->get('machine_number_start') }}">
                                    <input type="text" readonly id="machine_name1" name="machine_name1" class="w-70 mr-1" value="{{ request()->get('machine_name1') }}" >
                                    <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                            data-target="searchMachineNumberModal1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <span style="font-size:24px; padding:5px 3px;">
                                    ~
                                </span>
                                <div class="d-flex" style="width:49%">
                                    <input type="text" id="machine_number_end" 
                                        name="machine_number_end" 
                                        data-field-name="機番"
                                        data-error-messsage-container = "#machine_code-error"
                                        data-validate-exist-model="MachineNumber" 
                                        data-validate-exist-column="machine_number"
                                        data-inputautosearch-model="MachineNumber" 
                                        data-inputautosearch-column="machine_number"
                                        data-inputautosearch-return="machine_number_name" 
                                        data-inputautosearch-reference="machine_name2"
                                        maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        class="mr-1 w-25"
                                        value="{{ request()->get('machine_number_end') }}">
                                    <input type="text" readonly id="machine_name2" name="machine_name2" class="w-70 mr-1" value="{{ request()->get('machine_name2') }}">
                                    <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                            data-target="searchMachineNumberModal2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div id="machine_code-error"></div>
                        </div>
                        <div class="mr-4 w-25">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                <input type="text" id="department_code_start" name="department_code_start" 
                                    data-field-name="部門"
                                    data-error-messsage-container="#department_code"
                                    data-validate-exist-model="Department"
                                    data-validate-exist-column="code"
                                    data-inputautosearch-model="Department"
                                    data-inputautosearch-column="code"
                                    data-inputautosearch-return="name"
                                    data-inputautosearch-reference="department_name"
                                    minlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    maxlength="6"
                                    class="w-45 mr-1 text-left"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ request()->get('department_code_start') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchDepartmentModal1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                                <span style="font-size:24px; padding:5px 3px;">
                                    ~
                                </span>
                                    <input type="text" id="department_code_end" name="department_code_end" 
                                        data-field-name="部門"
                                        data-error-messsage-container="#department_code"
                                        data-validate-exist-model="Department"
                                        data-validate-exist-column="code"
                                        data-inputautosearch-model="Department"
                                        data-inputautosearch-column="code"
                                        data-inputautosearch-return="name"
                                        data-inputautosearch-reference="department_name"
                                        minlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                        maxlength="6"
                                        class="w-45 mr-1 text-left"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ request()->get('department_code_end') }}">
                                    <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                            data-target="searchDepartmentModal2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button>
                                </div>
                                <div id="department_code"></div>
                        </div>
                        <div class="w-20">
                            <label class="form-label dotted indented">ライン</label>
                            <div class="d-flex">
                                <input type="text" id="line_code_start" 
                                        name="line_code_start" 
                                        data-field-name="ライン"
                                        data-error-messsage-container="#line_code_error"
                                        data-validate-exist-model="Line"
                                        data-validate-exist-column="line_code"
                                        data-inputautosearch-model="line"
                                        data-inputautosearch-column="line_code"
                                        data-inputautosearch-return="line_name"
                                        data-inputautosearch-reference="line_name"
                                        minlength="3"
                                        maxlength="3"
                                        class="w-45 mr-1 text-left"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ request()->get('line_code_start') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchLineModal1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                                <span style="font-size:24px; padding:5px 3px;">
                                    ~
                                </span>
                                <input type="text" id="line_code_end" 
                                        name="line_code_end" 
                                        data-field-name="ライン"
                                        data-error-messsage-container="#line_code_error"
                                        data-validate-exist-model="Line"
                                        data-validate-exist-column="line_code"
                                        data-inputautosearch-model="line"
                                        data-inputautosearch-column="line_code"
                                        data-inputautosearch-return="line_name"
                                        data-inputautosearch-reference="line_name"
                                        minlength="3"
                                        maxlength="3"
                                        class="w-45 mr-1 text-left"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ request()->get('line_code_end') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchLineModal2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="line_code_error"></div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        <div class="mr-5" style="width:26.9%">
                            <label class="form-label dotted indented">費目</label>
                            <div class="d-flex">
                                <input type="text" name="expense_item1" id="expense_item_start"
                                    data-error-messsage-container="#expense_item"
                                    data-field-name="費目"
                                    data-validate-exist-model="Item" 
                                    data-validate-exist-column="expense_item"
                                    data-inputautosearch-model="Item" 
                                    data-inputautosearch-column="expense_item"
                                    data-inputautosearch-return="item_name" 
                                    data-inputautosearch-reference="item_name"
                                    class="w-45 mr-1 text-left"
                                    maxlength="3"
                                    minlength="3"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ request()->get('expense_item1') }}" required>
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchItemModal1">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                                <span style="font-size:24px; padding:5px 3px;">
                                    ~
                                </span>
                                <input type="text" name="expense_item2" id="expense_item_end"
                                    data-error-messsage-container="#expense_item"
                                    data-field-name="費目"
                                    data-validate-exist-model="Item" 
                                    data-validate-exist-column="expense_item"
                                    data-inputautosearch-model="Item" 
                                    data-inputautosearch-column="expense_item"
                                    data-inputautosearch-return="item_name" 
                                    data-inputautosearch-reference="item_name"
                                    class="w-45 mr-1 text-left"
                                    maxlength="3"
                                    minlength="3"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ request()->get('expense_item2') }}" required>
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchItemModal2">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="expense_item"></div>
                        </div>
                        <div class="mr-3 mt-2 w-15">
                            <label class="form-label dotted indented">購入区分</label>
                            <div class="d-flex mr-10c">
                                @php
                                    $category = request()->category ?? 2;
                                @endphp
                                <p class="formPack mr-2">
                                    <label class="radioBasic">
                                        <input type="radio" name="category" value="2" {{ $category == 2 ? 'checked' : '' }}>
                                        <span>購買品</span>
                                    </label>
                                </p>
                                <p class="formPack">
                                    <label class="radioBasic">
                                        <input type="radio" name="category" value="1" {{ $category == 1 ? 'checked' : '' }}>
                                        <span>生産品</span>
                                    </label>
                                </p>
                            </div>
                        </div>
                        <div class="w-65">
                            <label class="form-label dotted indented">品番</label>
                            <div class="d-flex">
                                <input type="text" id="part_number_start" 
                                    name="part_number_start" 
                                    data-field-name="品番"
                                    data-error-messsage-container="#part_numbers"
                                    data-validate-exist-model="ProductNumber"
                                    data-validate-exist-column="part_number"
                                    data-inputautosearch-model="ProductNumber"
                                    data-inputautosearch-column="part_number"
                                    data-inputautosearch-return="name_abbreviation"
                                    data-inputautosearch-reference="product_name"
                                    class="w-25 mr-1" value="{{ request()->get('part_number_start') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchPartNumberModal1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                                <span style="font-size:24px; padding:5px 3px;">
                                    ~
                                </span>
                                <input type="text" id="part_number_end" 
                                    name="part_number_end" 
                                    data-field-name="品番"
                                    data-error-messsage-container="#part_numbers"
                                    data-validate-exist-model="ProductNumber"
                                    data-validate-exist-column="part_number"
                                    data-inputautosearch-model="ProductNumber"
                                    data-inputautosearch-column="part_number"
                                    data-inputautosearch-return="name_abbreviation"
                                    data-inputautosearch-reference="product_name"
                                    class="w-25 mr-1" value="{{ request()->get('part_number_end') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchPartNumberModal2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="part_numbers"></div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        <div class="mr-4">
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">
                                <input type="text" id="product_name" 
                                    name="product_name"
                                    class="w-220c" value="{{ request()->get('product_name') }}">
                            </div>
                        </div>
                        <div class="mr-4">
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" id="standard" 
                                    name="standard" class="w-190c" 
                                    value="{{ request()->get('standard') }}">
                            </div>
                        </div>
                        <div class="mr-4">
                            <label class="form-label dotted indented">伝票No.</label>
                            <div class="d-flex">
                                <input type="text" id="slip_no" name="slip_no" class="w-190c mr-10c" value="{{ request()->get('slip_no') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="mr-4">
                            <label class="form-label dotted indented">プロジェクトNo.</label>
                            <div class="d-flex">
                                <input type="text" id="project_code" 
                                    name="project_code" 
                                    data-field-name="プロジェクトNo"
                                    data-validate-exist-model="Project" 
                                    data-validate-exist-column="project_number"
                                    data-inputautosearch-model="Project" 
                                    data-inputautosearch-column="project_number"
                                    data-inputautosearch-return="project_name" 
                                    data-inputautosearch-reference="project_name"
                                    maxlength="8"
                                    class="w-100c mr-10c" 
                                    value="{{ request()->get('project_code') }}">
                                <input type="text" 
                                    id="project_name" 
                                    name="project_name" 
                                    readonly 
                                    value="{{ request()->get('project_name') }}" 
                                    class="w-290c mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchProjectModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div data-error-container="project_code"></div>
                        </div>
                        <div class="mr-4">
                            <label class="form-label dotted indented">金額</label>
                            <div class="d-flex">
                                <input type="text" id="amount1" name="amount1" class="w-200c" value="{{ request()->get('amount1') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                <input type="text" id="amount2" name="amount2" class="w-200c mr-10c" value="{{ request()->get('amount2') }}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        <div class="mr-4">
                            <label class="form-label dotted indented">入力担当者</label>
                            <div class="d-flex">
                                <input type="text" id="employee_code" 
                                    name="employee_code" 
                                    data-field-name="入力担当者"
                                    data-validate-exist-model="employee" 
                                    data-validate-exist-column="employee_code" 
                                    data-inputautosearch-model="employee" 
                                    data-inputautosearch-column="employee_code" 
                                    data-inputautosearch-return="employee_name" 
                                    data-inputautosearch-reference="employee_name"
                                    maxlength="3"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    class="w-100c mr-10c" 
                                    value="{{ request()->get('employee_code') }}">
                                <input type="text" 
                                    id="employee_name" 
                                    name="employee_name" 
                                    readonly
                                    value="{{ request()->get('employee_name') }}" 
                                    class="w-290c mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchEmployeeModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div data-error-container="employee_code"></div>
                        </div>
                        <div class="mr-4">
                            <label class="form-label dotted indented">入力日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', ['inputName' => 'input_date_start', 'inputClass' =>'wi-150c', 'attributes' => 'data-error-messsage-container=#request_error_message1 data-field-name=入力日'])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', ['inputName' => 'input_date_end', 'inputClass' =>'wi-150c', 'attributes' => 'data-error-messsage-container=#request_error_message1 data-field-name=入力日'])
                            </div>
                            <div id="request_error_message1"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center  position-relative mt-5">
                        <div class="text-center">
                            <a class="btn btn-blue" style="min-width: 200px" id="resetForm">検索条件をクリア</a>
                            <button type="submit" class="btn btn-blue" name="search" style="min-width: 200px">検索</button>
                        </div>
                        <a href="{{ route('purchase.purchaseActual.export', request()->all()) }}" class="position-absolute right-0 btn btn-success">検索結果をEXCEL出力</a>
                    </div>
                    
                </div>
            </form>
            <div class="pagettlWrap mt-2">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <div class="mb-2">
                    @if ($datas && $datas->total() > 0)
                        {{ $datas->total()  }}件中、{{ $datas->firstItem()  }}件～{{ $datas->lastItem()  }}件を表示してます
                    @endif
                    <table class="table table-bordered text-center table-striped-custom">
                        <thead>
                        <tr>
                            <th rowspan="2" width="7%">入荷日</th>
                            <th rowspan="2" width="13%">仕入先名</th>
                            <th rowspan="2" width="14%">製品品番</th>
                            <th rowspan="2" width="16%">品名</th>
                            <th rowspan="2" width="5%">数量</th>
                            <th rowspan="2" width="5%">単位</th>
                            <th rowspan="2" width="7%">単価</th>
                            <th rowspan="2" width="7%">金額</th>
                            <th rowspan="2" width="10%">伝票種類</th>
                            <th rowspan="2" width="5%">伝票No.</th>
                            <th rowspan="2">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse ($datas as $data)
                            <tr data-purchase-record="{{ $data->purchase_record_no}}">
                                <td class="tA-cn" style="vertical-align: middle">{{ $data->arrival_date?->format('Y/m/d') }}</td>
                                <td class="tA-le" style="vertical-align: middle">{{ $data->supplier?->customer_name }}</td>
                                <td class="tA-le" style="vertical-align: middle">{{ $data->part_number }}</td>
                                <td class="tA-le" style="vertical-align: middle">{{ $data->product_name }}</td>
                                <td class="tA-ri" style="vertical-align: middle">{{ number_format($data->quantity, 0, '.', ',') }}</td>
                                <td class="tA-cn" style="vertical-align: middle">{{ $data->unit_name ?? '' }}</td>
                                <td class="tA-ri" style="vertical-align: middle">{{ number_format($data->unit_price, 0, '.', ',') }}</td>
                                <td class="tA-ri" style="vertical-align: middle">{{ number_format($data->amount_of_money, 0, '.', ',') }}</td>
                                <td class="tA-ce" style="vertical-align: middle">{{ (($data->slip_type == 1) ? '納入伝票' : (($data->slip_type == 2) ? '外注加工伝票' : '購入材伝票')) }}</td>
                                <td class="tA-le" style="vertical-align: middle">{{ $data->slip_no }}</td>
                                <td class="tA-cn">
                                    @if (in_array($data->purchase_category, [1, 2]))
                                        @php
                                            $route = $data->purchase_category == 1
                                                ? route('purchase.purchaseProduction.edit', ['id' => $data->id])
                                                : route('purchase.purchaseItem.edit', ['id' => $data->id]);

                                            $duplicate_route = $data->purchase_category == 1
                                                ? route('purchase.purchaseProduction.duplicate', $data->id)
                                                : route('purchase.purchaseItem.duplicate', $data->id);

                                        @endphp
                                        <a href="{{ $route }}" class="buttonBasic bColor-ok me-2">
                                            編集
                                        </a>
                                        <a href="{{$duplicate_route}}" class="buttonBasic bColor-ok me-2">
                                            複写
                                        </a>
                                    @endif
                                 </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="tA-le text-center" colspan='11'>検索結果はありません</td>
                            </tr>
                            @endforelse
                        </tbody>                                            
                    </table>
                    @if ($datas)
                        {{ $datas->appends(request()->query())->links() }}
                    @endif
                </div>
            </div>
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
        'modalId' => 'searchMachineNumberModal1',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_number_start',
        'resultNameElementId' => 'machine_name1',
        'resultBranchNumberEle' => 'machine_name1',
        'model' => 'MachineNumber',
    ])
    @include('partials.modals.machine_numbers._search', [
        'modalId' => 'searchMachineNumberModal2',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_number_end',
        'resultNameElementId' => 'machine_name2',
        'resultBranchNumberEle' => 'machine_name2',
        'model' => 'MachineNumber',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal1',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_start',
        'resultNameElementId' => 'department_name1',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal2',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_end',
        'resultNameElementId' => 'department_name2',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal1',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_start',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal2',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_end',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal1',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number_start',
        'resultNameElementId' => 'part_name',
        'model' => 'ProductNumber',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal2',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number_end',
        'resultNameElementId' => 'part_name',
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
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '入力担当者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal1',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_start',
        'resultNameElementId' => 'item_name',
        'model' => 'Item',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal2',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_end',
        'resultNameElementId' => 'item_name',
        'model' => 'Item',
    ])
@endsection
@push('scripts')
@vite(['resources/js/purchase/actual/index.js'])
@endpush