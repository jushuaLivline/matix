@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/master/part/edit.css')
    @vite('resources/css/master/product.css')
@endpush

@section('title', '品番マスタ登録・編集')

@section('content')

{{-- CREATE/UPDATE PRODUCT NUMBERS RECORDS --}}
<div class="content">
    <div class="contentInner">

        <div class="accordion">
            <h1><span>品番マスタ登録・編集</span></h1>
        </div>

        @if(session('success'))
            <div id="card" style="background-color: #fff; margin-top:20px; margin-bottom: 2rem; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <div style="text-align: left;">
                    <p style="font-size: 18px; color: #0d9c38;">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif
        <form id="edit-form" data-action='{{ isset($data) ? $data->id : 'store' }}' accept-charset="utf-8" 
            class="overlayedSubmitForm with-js-validation"
            data-confirmation-message="品番マスタ情報を更新します、よろしいでしょうか？">
            @csrf
            <div class="bg-white">
                <div class="row">
                    <div class="col-2 label-div">
                        品番 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                    </div>
                    <div class="col-10">
                        <input type="text" name="part_number" id="part_number"
                            value="{{ isset($data) ? $data->part_number : Request::get('part_number') }}"
                            required
                        >
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        品番編集形式
                    </div>
                    <div class="col-10">
                        <input type="text" name="part_number_editing_format" id="part_number_editing_format"
                            value="{{ isset($data) ? $data->part_number_editing_format : Request::get('part_number_editing_format') }}"
                            class="w-75px acceptNumericOnly format-number"
                            maxlength="2"
                            data-format="part_number"
                        >

                        <input type="text" name="edited_part_number" id="edited_part_number"
                            value="{{ isset($data) ? $data->edited_part_number : Request::get('edited_part_number') }}"
                            readonly
                        >
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        品名 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                    </div>
                    <div class="col-10">
                        <input type="text" name="product_name" id="product_name"
                            value="{{ isset($data) ? $data->product_name : Request::get('product_name') }}"
                            required
                        >
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        製品区分 
                    </div>
                    <div class="col-10 d-flex align-items-center">
                        <input type="radio" name="product_category" id="product_category_0" value="0"
                            {{ (isset($data) && $data->product_category == "材料") || Request::get('product_category') === 0 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="product_category_0" class="mr-4">材料</label>
                        <input type="radio" name="product_category" id="product_category_1" value="1"
                            {{ (isset($data) && $data->product_category == "製品") || Request::get('product_category') === 1 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="product_category_1" class="mr-4">製品</label>
                        <input type="radio" name="product_category" id="product_category_2" value="2"
                            {{ (isset($data) && $data->product_category == "試作品") || Request::get('product_category') === 2 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="product_category_2" class="mr-4">試作品</label>
                        <input type="radio" name="product_category" id="product_category_3" value="3"
                            {{ (isset($data) && $data->product_category == "購入材") || Request::get('product_category') === 3 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="product_category_3" class="mr-4">購入材</label>
                        <input type="radio" name="product_category" id="product_category_4" value="4"
                            {{ (isset($data) && $data->product_category == "仕掛品") || Request::get('product_category') === 4 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="product_category_4">仕掛品</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        生産区分
                    </div>
                    <div class="col-10 d-flex align-items-center">
                        <input type="radio" name="production_division" id="production_division_1" value="1"
                            {{ (isset($data) && $data->production_division === 1) || Request::get('production_division') === 1 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="production_division_1" class="mr-4">号口</label>
                        <input type="radio" name="production_division" id="production_division_2" value="2"
                            {{ (isset($data) && $data->production_division === 2) || Request::get('production_division') === 2 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="production_division_2">補給</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        指示区分
                    </div>
                    <div class="col-10 d-flex align-items-center">
                        <input type="radio" name="instruction_class" id="instruction_class_1" value="1"
                            {{ (isset($data) && $data->instruction_class === 1) || Request::get('instruction_class') === 1 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="instruction_class_1" class="mr-4">かんばん</label>
                        <input type="radio" name="instruction_class" id="instruction_class_2" value="2"
                            {{ (isset($data) && $data->instruction_class === 2) || Request::get('instruction_class') === 2 ? 'checked' : '' }}
                        >&nbsp;
                        <label for="instruction_class_2">指示</label>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        得意先
                    </div>
                    <div class="col-10 d-flex">
                        <input type="text" id="customer_code" name="customer_code"
                            value="{{ isset($data) ? $data->customer_code : Request::get('customer_code') }}"
                            class="acceptNumericOnly w-100px fetchQueryName mr-1"
                            data-model="Customer"
                            data-query="customer_code"
                            data-query-get="supplier_name_abbreviation"
                            data-reference="customer_name"
                            maxlength="6"
                            >
                        <input type="text" readonly
                            id="customer_name"
                            value="{{ isset($data) ? $data->customer?->supplier_name_abbreviation : Request::get('customer_name') }}"
                            class="middle-name mr-2"
                        >
                        <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                data-target="searchCustomerModal">
                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                alt="magnifying_glass.svg">
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        仕入先
                    </div>
                    <div class="col-10 d-flex">
                        <input type="text" id="supplier_code" name="supplier_code"
                            value="{{ isset($data) ? $data->supplier_code : Request::get('supplier_code') }}"
                            class="acceptNumericOnly w-100px fetchQueryName mr-1"
                            data-model="Customer"
                            data-query="customer_code"
                            data-query-get="supplier_name_abbreviation"
                            data-reference="supplier_name"
                            maxlength="6"
                            >
                        <input type="text" readonly
                            id="supplier_name"
                            value="{{ isset($data) ? $data->supplier?->supplier_name_abbreviation : Request::get('supplier_name') }}"
                            class="middle-name mr-2"
                        >
                        <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                data-target="searchSupplierModal">
                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                alt="magnifying_glass.svg">
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        部門 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                    </div>
                    <div class="col-10 d-flex">
                        <input type="text" id="department_code" name="department_code"
                            value="{{ isset($data) ? $data->department_code : Request::get('department_code') }}"
                            class="acceptNumericOnly w-100px fetchQueryName mr-1"
                            data-model="Department"
                            data-query="code"
                            data-query-get="name"
                            data-reference="department_name"
                            maxlength="6"
                            required
                            >
                        <input type="text" readonly
                            id="department_name"
                            value="{{ isset($data) ? $data->department?->name : Request::get('department_name') }}"
                            class="middle-name  mr-2"
                        >
                        <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                data-target="searchDepartmentModal">
                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                alt="magnifying_glass.svg">
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        ラインコード
                    </div>
                    <div class="col-10 d-flex">
                        <input type="text" id="line_code" name="line_code"
                            value="{{ isset($data) ? $data->line_code : Request::get('line_code') }}"
                            class="acceptNumericOnly w-100px fetchQueryName mr-1"
                            data-model="Line"
                            data-query="line_code"
                            data-query-get="line_name"
                            data-reference="line_name"
                            maxlength="3"
                            >
                        <input type="text" readonly
                            id="line_name"
                            value="{{ isset($data) ? $data->line?->line_name : Request::get('line_name') }}"
                            class="middle-name mr-2"
                        >
                        <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                data-target="searchLineModal">
                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                alt="magnifying_glass.svg">
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        規格
                    </div>
                    <div class="col-10">
                        <input type="text" id="standard" name="standard"
                            value="{{ isset($data) ? $data->standard : Request::get('standard') }}"
                        >
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        材料メーカー
                    </div>
                    <div class="col-10 d-flex">
                        <input type="text" id="material_manufacturer_code" name="material_manufacturer_code"
                            value="{{ isset($data) ? $data->material_manufacturer_code : Request::get('material_manufacturer_code') }}"
                            class="w-100px fetchQueryName mr-1"
                            data-model="ManufacturerInfo"
                            data-query="material_manufacturer_code"
                            data-query-get="person_in_charge"
                            data-reference="person_in_charge"
                            maxlength="4"
                        >
                        <input type="text" readonly
                            id="person_in_charge"
                            name="person_in_charge"
                            value="{{ isset($data) ? $data->manufacturer?->person_in_charge : Request::get('person_in_charge') }}"
                            class="middle-name mr-2"
                        >
                        <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                data-target="searchManufacturerModal">
                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                alt="magnifying_glass.svg">
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        単位
                    </div>
                    <div class="col-10">
                        <select name="unit_code" id="unit_code" class="w-100px">
                            @foreach ($codes as $code)
                                @if ((isset($data) && $code->code == $data->unit_code) || $code->code == Request::get('unit_code'))
                                    <option value="{{ $code->code }}" selected>{{ $code->name }}</option>
                                @else
                                    <option value="{{ $code->code }}">{{ $code->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        得意先品番
                    </div>
                    <div class="col-10">
                        <input type="text" name="customer_part_number" id="customer_part_number"
                            value="{{ isset($data) ? $data->customer_part_number : Request::get('customer_part_number') }}"
                        >
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        得意先品番編集形式
                    </div>
                    <div class="col-10">
                        <input type="text" name="customer_part_number_edit_format" id="customer_part_number_edit_format"
                            value="{{ isset($data) ? $data->customer_part_number_edit_format : Request::get('customer_part_number_edit_format') }}"
                            class="w-75px acceptNumericOnly format-number"
                            maxlength="2"
                            data-format="customer_part_number"
                        >

                        <input type="text" name="customer_edited_product_number" id="customer_edited_product_number"
                            value="{{ isset($data) ? $data->customer_edited_product_number : Request::get('customer_edited_product_number') }}"
                            readonly
                        >
                    </div>
                </div>

                <div class="row">
                    <div class="col-2 label-div">
                        無効にする
                    </div>
                    <div class="col-10">
                        <input type="hidden" name="delete_flag" value="0">
                        <input type="checkbox" name="delete_flag" id="delete_flag"
                            value="1"
                            {{ (isset($data) && $data->delete_flag === 1) || Request::get('delete_flag') === 1 ? 'checked' : '' }}
                        >
                    </div>
                </div>
            </div>
            <div class="w-full mt-4 d-flex justify-content-between">
                <div class="">
                    <button type="button" class="btn w-150px btn-delete-action {{ !isset($data) ? 'isNew' : '' }} @if(isset($data) && $data->delete_flag === 1) btn-disabled @else btn-orange @endif"
                            id="update-delete" data-action="{{ isset($data) ? $data->id : 'store' }}"
                            @if(isset($data) && $data->delete_flag === 1) disabled @endif>
                        削除
                    </button>
                </div>

                <div class=" d-flex justify-content-between">
                    <button type="button" class="btn btn-primary pr-4 pl-4 button-modal js-modal-open w-150c mr-2 {{ !isset($data) ? 'isNew' : '' }}"
                            data-target="unitPriceSettingModal" id="unitPriceSetting">
                            品番単価設定
                    </button>
                    <button type="button" class="btn btn-primary pr-4 pl-4 button-modal js-modal-open w-150c mr-2 {{ !isset($data) ? 'isNew' : '' }}"
                            data-target="configurationSettingsModal" id="openConfigModal">
                            構成設定
                    </button>
                    <button type="button" class="btn btn-primary pr-4 pl-4 button-modal js-modal-open w-150c {{ !isset($data) ? 'isNew' : '' }}"
                            data-target="sequenceSettingModal" id="sequenceSetting">
                            工程順序設定
                    </button>
                </div>

                <div class=" text-right">
                    {{-- <button type="button" class="btn btn-blue w-150px btn-disabled {{ !isset($data) ? 'isNew' : '' }}">
                        複写入力
                    </button> --}}
                    <button type="submit" class="btn btn-success w-150px btn-register-product-number">
                        登録する
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
        var assetUrl = "{{ asset('images/icons/magnifying_glass.svg') }}";
    </script>

@include('partials.modals.masters._search', [
    'modalId' => 'searchCustomerModal',
    'searchLabel' => '取引先',
    'resultValueElementId' => 'customer_code',
    'resultNameElementId' => 'customer_name',
    'model' => 'Customer'
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '仕入先',
    'resultValueElementId' => 'supplier_code',
    'resultNameElementId' => 'supplier_name',
    'model' => 'Supplier'
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
    'searchLabel' => 'ラインコード',
    'resultValueElementId' => 'line_code',
    'resultNameElementId' => 'line_name',
    'model' => 'Line'
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchManufacturerModal',
    'searchLabel' => '材料メーカー',
    'resultValueElementId' => 'material_manufacturer_code',
    'resultNameElementId' => 'person_in_charge',
    'model' => 'ManufacturerInfo'
])

@if (isset($data))

<!-- MODALS -->
@include('pages.master.part.modal.part_number_unit_price_setting_modal', [
    'modalId' => 'unitPriceSettingModal',
    'modalLabel' => '品番単価設定',
    'productId' => isset($data) ? $data->id : '',
    'partNumber' => isset($data) ? $data->part_number : '',
    'productName' => isset($data) ? $data->product_name : '',
    //'productPrices' => isset($productPrices) ? $productPrices : [],
    'insideProcess' => isset($insideProcess) ? $insideProcess['processing_unit_price'] : 0,
    'outsideProcess' => isset($outsideProcess) ? $outsideProcess['processing_unit_price'] : 0,
])
@include('pages.master.part.modal.sequence_setting_modal', [
    'modalId' => 'sequenceSettingModal',
    'modalLabel' => '工程順序設定',
    'productId' => isset($data) ? $data->id : '',
    'partNumber' => isset($data) ? $data->part_number : '',
    'productName' => isset($data) ? $data->product_name : '',
])
@include('pages.master.part.modal.configuration_settings_modal', [
    'modalId' => 'configurationSettingsModal',
    'modalLabel' => '構成マスタメンテ',
    'product' => $data ?? [],
    'configurations' => $configurations ?? [],
])

@endif
@endsection

@push('scripts')
    @vite(['resources/js/master/part/edit.js'])
@endpush