@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '指定納品書発行')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                指定納品書発行
            </div>
            @if(session('success'))
                <div class="tableWrap borderLesstable">
                    <div class="success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="tableWrap borderLesstable">
                    <div class="error">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            <form action="{{ route('outsource.pdf.export', request()->all()) }}" 
                    id="forms" class="with-js-validation"
                        data-confirmation-message="指定納品書を出力します、よろしいでしょうか？"
                        data-disabled-overlay="true"
                        >
                <div class="section">
                    <h1 class="form-label bar indented">指定納品書発行</h1>
                    <div class="box mb-3">
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">仕入先 </label> <span
                                    class="others-frame btn-orange badge">必須</span>
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
                                        minlength="6"
                                        maxlength="6"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ old('supplier_code', Request::get('supplier_code') ?? '') }}" 
                                        required>
                                    <input type="text" readonly
                                        name="supplier_name"
                                        id="supplier_name"
                                        value="{{ old('supplier_name', Request::get('supplier_name') ?? '') }}"
                                        class="middle-name text-left w-290c mr-10c">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchSupplierModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div data-error-container="supplier_code"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">製品品番</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-half">
                                        <input 
                                            type="text" 
                                            id="product_code" 
                                            class="w-150c"
                                            data-field-name="製品品番"
                                            data-validate-exist-model="ProductNumber"
                                            data-validate-exist-column="part_number"
                                            data-inputautosearch-model="ProductNumber"
                                            data-inputautosearch-column="part_number"
                                            data-inputautosearch-return="product_name"
                                            data-inputautosearch-reference="product_name"
                                            name="product_code" 
                                            value="{{ old('product_code', Request::get('product_code') ?? '') }}" 
                                            required>
                                    </p>
                                    <p class="formPack fixedWidth fpfw50 box-middle-name mr-half">
                                        <input type="text" readonly
                                                id="product_name"
                                                name="product_name"
                                                value="{{ old('product_name', Request::get('product_name') ?? '') }}"
                                                class="middle-name w-200c">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProductNumberModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                </div>
                                <div data-error-container="product_code"></div></p>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">発行枚数</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" id="" style="width: 100px" 
                                        data-field-name="発行枚数"
                                        value="{{ old('number_of_copies', Request::get('number_of_copies') ?? '') }}" 
                                        class="acceptNumericOnly"
                                        name="number_of_copies" required>
                                </div>
                                <div data-error-container="number_of_copies"></div></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button type="submit" class="float-right btn btn-success btn-wide">指定納品用発行</button>
                </div>
            </form>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductMaterial'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier',
        'query'=> "searchProductNumberModal",
        'reference' => "supplier_code"
    ])
@endsection