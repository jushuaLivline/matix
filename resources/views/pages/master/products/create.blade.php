@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_form.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('content')
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

                @csrf
                <input type="hidden" id="product_id" value="{{ isset($product) ? $product->id : '' }}">
                <div class="">
                    {{-- 品番 --}}
                    {{-- part_number --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                品番
                            </span>
                            <div class="required-label">
                                必須
                            </div>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-mid" id="part_number" name="part_number" value="{{ $product->part_number ?? '' }}">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 品番編集形式 --}}
                    {{-- part_number_editing_format --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                品番編集形式
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" id="part_number_editing_format" name="part_number_editing_format" style="width: 90px" value="{{ $product->part_number_editing_format ?? '' }}">
                                <input type="text" readonly
                                        name="edited_part_number"
                                        id="edited_part_number"
                                        value="{{ $product->edited_part_number ?? '' }}"
                                        class="middle-name"
                                        style="width: 210px">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 品名 --}}
                    {{-- product_name --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                品名
                            </span>
                            <div class="required-label">
                                必須
                            </div>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-long" id="product_name" name="product_name" value="{{ $product->product_name ?? '' }}">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 品名略 --}}
                    {{-- name_abbreviation --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                品名略
                            </span>
                            <div class="required-label">
                                必須
                            </div>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-short" id="name_abbreviation" name="name_abbreviation" value="{{ $product->name_abbreviation ?? '' }}">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 製品区分 --}}
                    {{-- product_category --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                製品区分
                            </span>
                        </div>
                        <div class="input-row flex-radio">
                            @foreach ($productCategory as $key => $category)
                                <label class="radioBasic">
                                    <input type="radio" name="product_category" value="{{ $key }}" id="{{ $key }}" {{ isset($product) && $product->product_category ? 'checked' : '' }}>
                                    <span>{{ $category }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 生産区分 --}}
                    {{-- production_division --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                生産区分
                            </span>
                        </div>
                        <div class="input-row flex-radio">
                            @foreach ($productionDivision as $key => $item)
                                <label class="radioBasic">
                                    <input type="radio" name="production_division" value="{{ $key }}" id="{{ $key }}" {{ isset($product) && $product->production_division ? 'checked' : '' }}>
                                    <span>{{ $item }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 指示区分 --}}
                    {{-- instruction_class --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                指示区分
                            </span>
                        </div>
                        <div class="input-row flex-radio">
                            @foreach ($instructionClass as $key => $item)
                                <label class="radioBasic">
                                    <input type="radio" name="instruction_class" value="{{ $key }}" id="{{ $key }}" {{ isset($product) && $product->instruction_class ? 'checked' : '' }}>
                                    <span>{{ $item }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 得意先 --}}
                    {{-- customer_code --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                得意先
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" id="customer_code" name="customer_code" value="{{ $product->customer_code ?? '' }}" class="short-search">
                            <input type="text" readonly
                                        name="customer_name"
                                        id="customer_name"
                                        value="{{ $product->customer?->customer_name ?? '' }}"
                                        class="middle-name"
                                        style="width: 210px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchCustomerModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 仕入先 --}}
                    {{-- supplier_code --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                仕入先
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" id="supplier_code" name="supplier_code" value="{{ $product->supplier_code ?? '' }}" class="short-search">
                            <input type="text" readonly
                                        name="supplier_name"
                                        id="supplier_name"
                                        value="{{ $product->supplier?->supplier_name_abbreviation ?? '' }}"
                                        class="middle-name"
                                        style="width: 210px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 部門 --}}
                    {{-- department_code --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                部門
                            </span>
                            <div class="required-label">
                                必須
                            </div>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" id="department_code" name="department_code" value="{{ $product->department_code ?? '' }}" class="short-search">
                            <input type="text" readonly
                                        name="department_name"
                                        id="department_name"
                                        value="{{ $product->department ?? '' }}"
                                        class="middle-name"
                                        style="width: 210px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchDepartmentModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- ラインコード --}}
                    {{-- line_code --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                ラインコード
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" id="line_code" name="line_code" value="{{ $product->line_code ?? '' }}" class="short-search">
                            <input type="text" readonly
                                        name="line_name"
                                        id="line_name"
                                        value="{{ $product->line ?? '' }}"
                                        class="middle-name"
                                        style="width: 210px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchLineModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 規格 --}}
                    {{-- standard --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                規格
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-long" id="standard" name="standard" value="{{ $product->standard ?? '' }}">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 材料メーカー --}}
                    {{-- material_manufacturer_code --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                材料メーカー
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" id="material_manufacturer_code" name="material_manufacturer_code" value="{{ $product->material_manufacturer_code ?? '' }}" class="short-search">
                            <input type="text" readonly
                                        name="person_in_charge"
                                        id="person_in_charge"
                                        value=""
                                        class="middle-name"
                                        style="width: 210px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchManufacturerModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 単位 --}}
                    {{-- unit_code --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                単位
                            </span>
                        </div>
                        <div class="input-row">
                            <select name="unit_code" id="unit_code" class="classic" style="width: 130px">
                                @foreach ($units as $unit)
                                    <option value="{{ $unit->id }}" {{ isset($product) && $product->unit_code == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 得意先品番 --}}
                    {{-- customer_part_number --}}
                    <div class="row-field">
                        <div class="label-row">
                            <span class="input-label">
                                得意先品番
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="text" class="row-input-mid" id="customer_part_number" name="customer_part_number" value="{{ $product->customer_part_number ?? '' }}">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 得意先品番編集形式 --}}
                    {{-- customer_part_number_edit_format --}}
                    <div class="row-field {{ isset($product->id) ? '' : 'last-row-field' }}">
                        <div class="label-row">
                            <span class="input-label">
                                得意先品番編集形式
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input type="text" id="customer_part_number_edit_format" name="customer_part_number_edit_format" value="{{ $product->customer_part_number_edit_format ?? '' }}" class="short-search">
                            <input type="text" readonly
                                        name="customer_edited_product_number"
                                        id="customer_edited_product_number"
                                        value="{{ $product->customer_edited_product_number ?? '' }}"
                                        class="middle-name"
                                        style="width: 210px">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    {{-- 無効にする --}}
                    {{-- instruction_class --}}
                    <div class="row-field last-row-field {{ isset($product->id) ? '' : 'dis-none' }}">
                        <div class="label-row">
                            <span class="input-label">
                                無効にする
                            </span>
                        </div>
                        <div class="input-row-checkbox flex-radio">
                            <label class="container-checkbox">
                                <input type="checkbox">
                                <span class="checkmark-checkbox"></span>
                            </label>
                        </div>
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="buttonlistWrap">
                    <li>
                        <div class="parent-create">
                            <div>
                                <button type="button" id="hard_delete_product" class="btn btn-orange btn-wide {{ !isset($product) ? 'isNew' : '' }}">
                                    削除
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary pr-4 pl-4 button-modal js-modal-open {{ !isset($product) ? 'isNew' : '' }}"
                                        data-target="unitPriceSettingModal" id="unitPriceSetting">
                                        品番単価設定
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary pr-4 pl-4 button-modal js-modal-open {{ !isset($product) ? 'isNew' : '' }}"
                                        data-target="configurationSettingsModal" id="openConfigModal">
                                        構成設定
                                </button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-primary pr-4 pl-4 button-modal js-modal-open {{ !isset($product) ? 'isNew' : '' }}"
                                        data-target="sequenceSettingModal" id="sequenceSetting">
                                        工程順序設定
                                </button>
                            </div>
                            <div>
                                <button type="button" id="btn-copy" class="buttonCreate btn-blue button-product btn-copy {{ !empty($last_input) ? '' : 'isNew' }}">
                                        複写入力
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-wide btn-success">
                                    この内容で登録する
                                </button>
                                {{-- <input type="submit" value="登録する"
                                    class="buttonCreate btn-save buttonBasic bColor-ok"> --}}
                            </div>
                        </div>
                    </li>
                </div>
            </form>
        </div>
    </div>
    <script>
        var assetUrl = "{{ asset('images/icons/magnifying_glass.svg') }}";
    </script>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '得意先',
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
        'modalId' => 'searchManufacturerModal',
        'searchLabel' => '材料メーカー',
        'resultValueElementId' => 'material_manufacturer_code',
        'resultNameElementId' => 'person_in_charge',
        'model' => 'ManufacturerInfo'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ラインコード',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @if (isset($product))
        @include('pages.master.part.modal.part_number_unit_price_setting_modal', [
            'modalId' => 'unitPriceSettingModal',
            'modalLabel' => '品番単価設定',
            'productId' => isset($product) ? $product->id : '',
            'partNumber' => isset($product) ? $product->part_number : '',
            'productName' => isset($product) ? $product->product_name : '',
            'productPrices' => isset($productPrices) ? $productPrices : [],
            'insideProcess' => isset($insideProcess) ? $insideProcess['processing_unit_price'] : 0,
            'outsideProcess' => isset($outsideProcess) ? $outsideProcess['processing_unit_price'] : 0,
        ])
        @include('pages.master.part.modal.sequence_setting_modal', [
            'modalId' => 'sequenceSettingModal',
            'modalLabel' => '工程順序設定',
            'productId' => isset($product) ? $product->id : '',
            'partNumber' => isset($product) ? $product->part_number : '',
            'productName' => isset($product) ? $product->product_name : '',
        ])
        {{-- @include('partials.modals.masters.products.process_setting_modal', [
            'modalId' => 'processSettingModal',
            'modalLabel' => '工程順序設定',
        ]) --}}
        @include('pages.master.part.modal.configuration_settings_modal', [
            'modalId' => 'configurationSettingsModal',
            'modalLabel' => '構成マスタメンテ',
            'product' => $product ?? null,
            'configurations' => $configurations ?? null,
        ])
    @endif
@endsection
@push('scripts')
    @vite(['resources/js/master/products/data-form.js'])
@endpush
