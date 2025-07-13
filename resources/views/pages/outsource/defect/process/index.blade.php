@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
@endpush

@section('title', '加工不良実績一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                加工不良実績一覧
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form class="overlayedSubmitForm with-js-validation"  data-disregard-empty="true" id="form_request">
                    <div class="box mb-3">
                        <div class="mb-3 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">廃却日</label>
                                <div class="d-flex">
                                    @include('partials._date_picker', ['inputName' => 'disposal_date_from',
                                        'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=廃却日', 
                                                'value' => Request::get('disposal_date_from', date('Ym01') ?? '')])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', ['inputName' => 'disposal_date_to',
                                    'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=廃却日', 
                                                'value' => Request::get('disposal_data_to', date('Ymt'))])
                                </div>
                                <div id="date_error_message"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">入力日</label>
                                <div class="d-flex">
                                    @include('partials._date_picker', ['inputName' => 'input_date_from',
                                     'attributes' => 'data-error-messsage-container=#input_date_error_message data-field-name=入力日', 
                                    'value' => Request::get('input_date_from', date('Ym01'))])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', ['inputName' => 'input_date_to',
                                     'attributes' => 'data-error-messsage-container=#input_date_error_message data-field-name=入力日', 
                                                'value' => Request::get('input_date_to', date('Ymt'))])
                                </div>
                                <div id="input_date_error_message"></div>
                            </div>
                        </div>
                        <br/>
                        <div class="mb-4 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">工程</label>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-half">
                                        <input type="text" id="process_code" name="process_code"
                                            data-field-name="工程"
                                            data-error-messsage-container="#process_code_error"
                                            data-validate-exist-model="Process" 
                                            data-validate-exist-column="process_code"
                                            data-inputautosearch-model="Process"
                                            data-inputautosearch-column="process_code"
                                            data-inputautosearch-return="process_name"
                                            data-inputautosearch-reference="process_name" maxlength="6"
                                            value="{{ old('process_code', Request::get('process_code') ?? '') }}"
                                            style="width: 100px;">
                                    </p>
                                    <p class="formPack fixedWidth fpfw50 box-middle-name mr-half">
                                        <input type="text" readonly id="process_name" name="process_name"
                                            value="{{ old('process_name', Request::get('process_name') ?? '') }}"
                                            class="middle-name" style="width: 230px">
                                    </p>
                                    <div class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProcessModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <div id="process_code_error"></div>
                            </div>
                            <div class="mr-3">
                                <label class="form-label dotted indented">製品品番</label>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-half">
                                        <input type="text" id="product_code" name="product_code"
                                            data-field-name="製品品番"
                                            data-error-messsage-container="#product_code_error"
                                            data-validate-exist-model="ProductNumber" 
                                            data-validate-exist-column="part_number"
                                            data-inputautosearch-model="ProductNumber"
                                            data-inputautosearch-column="part_number"
                                            data-inputautosearch-return="product_name"
                                            data-inputautosearch-reference="product_name"
                                            value="{{ old('product_code', Request::get('product_code') ?? '') }}"
                                            style="width: 200px;">
                                    </p>
                                    <p class="formPack fixedWidth fpfw50 box-middle-name mr-half">
                                        <input type="text" readonly id="product_name" name="product_name"
                                            value="{{ old('product_name', Request::get('product_name') ?? '') }}"
                                            class="middle-name" style="width: 230px">
                                    </p>
                                    <div class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <div id="product_code_error"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">伝票No</label>
                                <div class="d-flex">
                                    <input type="text" value="{{ Request::get('slip_no') ?? '' }}" name="slip_no"
                                    maxLength="20">
                                </div>
                            </div>
                        </div>
                        <br/>
                        <br/>
                        <div class="text-center">
                            <a class="btn btn-primary" style="min-width: 200px" id="resetForm">検索条件をクリア</a>
                            <button type="submit" class="btn btn-primary" style="min-width: 200px">検索</button>
                        </div>
                        <a href="{{ route('outsource.process.defect.export', Request::all()) }}" type="button" 
                                    class="float-right btn btn-success {{ $items->total() == 0 ? 'btn-disabled' : '' }}" 
                                    id="exportBtn"
                                    style="margin-top:-40px;">検索結果をEXCEL出力</a>
                    </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    <div class="mb-2">
                        @if(Request::get('disposal_date_from'))
                            {{ $items->total() }}件中、{{ $items->firstItem() }}件～{{ $items->lastItem() }} 件を表示しています
                        @endif
                        <table class="table table-bordered text-center table-striped">
                            <thead>
                            <tr>
                                <th>廃却日</th>
                                <th>仕入先名</th>
                                <th style="width: 17%;">製品品番</th>
                                <th style="width: 20%;">品名</th>
                                <th style="width: 10%;">数量</th>
                                <th style="width: 8%;">単価</th>
                                <th style="width: 8%;">金額</th>
                                <th style="width: 10%;">伝票No</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr data-id="{{ $item['id'] }}" id="row-{{ $item['id'] }}">
                                        <td style="vertical-align: middle;">{{ $item->disposal_date->format('Ymd') }}</td>
                                        <td style="vertical-align: middle;">
                                            {{ $item->product?->customer?->supplier_name_abbreviation }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: left;">
                                            {{ $item->part_number }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            {{ $item->product?->product_name }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            {{ $item->quantity }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            {{ number_format($item->product?->latestProductPrice?->unit_price) }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: center !important;">
                                            {{ number_format($item->quantity * $item->product?->latestProductPrice?->unit_price) }}
                                        </td>
                                        <td style="vertical-align: middle; text-align: center;">
                                            {{ $item->slip_no }}
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div class="center" id="EditDelete">
                                                <a type="button" href="{{ route('outsource.defect.process.edit', array_merge(['id' => $item['id']], request()->all())) }}" class="btn btn-block btn-blue" id="edit">編集</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">検索結果はありません</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{ $items->appends(request()->all())->links() }}
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '工程',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'process_name',
        'model' => 'Process'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
    @foreach ($items as $item)
        @include('partials.modals.masters._search', [
            'modalId' => 'searchProductModal-'.$item->id,
            'searchLabel' => '製品品番',
            'resultValueElementId' => 'product_code'.$item->id,
            'resultNameElementId' => 'product_name'.$item->id,
            'model' => 'ProductNumber'
        ])
    @endforeach
    
    @push('scripts')
        @vite(['resources/js/outsource/defect/process/index.js'])
    @endpush
@endsection


