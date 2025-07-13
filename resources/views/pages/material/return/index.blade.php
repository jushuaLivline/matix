@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/data_list.css')

    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '返品実績一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>返品実績一覧</span></h1>
            </div>
            @if(session('success'))
                <div class="tableWrap borderLesstable">
                    <div class="success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('material.returnCreate.index') }}" accept-charset="utf-8" 
            class="overlayedSubmitForm with-js-validation" data-disregard-empty="true"
            id="returnRecordsForm">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                        <tr>
                            <!-- 見積依頼日 -->
                            <td width="360px">
                                <dl class="formsetBox">
                                    <dt class="">返却日</dt>
                                    <dd>


                                    <div class="d-flex" style="width: 360px">
                                            @include('partials._date_picker', [
                                                'inputName' => 'arrival_day_from',
                                                //'value' => Request::get('instruction_date_start', date('Ym01')),
                                                'value' => request('arrival_day_from') ?: '',
                                                'attributes' => 'data-error-messsage-container=#date_error_message', 
                                                'dateFormat' => 'YYYYMMDD', 
                                            ])
                                            <span style="font-size:24px; padding:5px 10px;">
                                                ~
                                            </span>
                                            @include('partials._date_picker', [
                                                'inputName' => 'arrival_day_to',
                                                //'value' => Request::get('instruction_date_end', date('Ymt')),
                                                'value' => request('arrival_day_to') ?: '',
                                                'attributes' => 'data-error-messsage-container=#date_error_message', 
                                                'dateFormat' => 'YYYYMMDD', 
                                            ])
                                            </button>
                                        </div>
                                        <div id ="date_error_message"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 得意先 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>伝票No.</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" name="delivery_no" value="{{ Request::get('delivery_no') }}" class="">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>

                            <!-- 見積回答種別 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>伝票区分</dt>
                                    <dd>
                                        <div style="display: inline-flex; width: 350px;">
                                            <input class="radioBasic" type="radio" id="voucher_class1" style="margin-left: 0px;" name="voucher_class" value="1"
                                            {{ (old('voucher_class') === '1' || Request::get('voucher_class') === '1') ? 'checked' : 'checked' }}>
                                            <label for="voucher_class1" style="min-width: 50px; text-align: left">すべて</label>
                                        
                                            <input class="radioBasic" type="radio" id="voucher_class2" name="voucher_class" value="2"
                                            {{ (old('voucher_class') === '2' || Request::get('voucher_class') === '2') ? 'checked' : '' }}>
                                            <label for="voucher_class2" style="min-width: 50px; text-align: left">既受返品</label>

                                            <input class="radioBasic" type="radio" id="voucher_class3" name="voucher_class" value="3"
                                            {{ (old('voucher_class') === '3' || Request::get('voucher_class') === '3') ? 'checked' : '' }}>
                                            <label for="voucher_class3" style="min-width: 50px; text-align: left">材不返品</label>
                                        </div>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <!-- 得意先 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>材料メーカー</dt>
                                    <dd>
                                    <div class="d-flex">
                                        <div class="formPack mr-10c">
                                            <input type="text" name="material_manufacturer_code" value="{{ Request::get('material_manufacturer_code') }}"/>
                                        </div>
                                    </div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 得意先 -->
                            <td width="600px;">
                                <dl class="formsetBox">
                                    <dt>材料品番</dt>
                                    <dd>
                                        <p class="formPack  mr-1">
                                            <input type="text" name="product_code" id="product_code" 
                                                data-field-name="材料品番"
                                                data-error-messsage-container=#product_code_error_message
                                                data-validate-exist-model="ProductNumber"
                                                data-validate-exist-column="part_number"
                                                data-inputautosearch-model="ProductNumber"
                                                data-inputautosearch-column="part_number"
                                                data-inputautosearch-return="product_name"
                                                data-inputautosearch-reference="product_name"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                value="{{ Request::get('product_code') }}">
                                        </p>
                                        <p class="formPack fixedWidth fpfw50p box-middle-name mr-1">
                                            <input type="text" readonly value="{{ Request::get('product_name') }}"
                                                class="middle-name" name="product_name" id="product_name">
                                        </p>
                                        <p class="formPack fixedWidth fpfw25p">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProductNumberModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </p>
                                        <div id ="product_code_error_message"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <a href="{{ route('material.returnExcelExport', Request::all()) }}" class="float-right btn btn-green {{ $supplyArrivals->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                    <ul class="buttonlistWrap">
                        <li>
                            <button type="button" class="btn btn-blue" type="submit" style="min-width: 300px"
                             data-clear-inputs
                             data-clear-form-target="#returnRecordsForm">
                                検索条件をクリア
                            </button>
                        </li>
                        <li>
                            <button type="submit" class="btn btn-blue" type="submit" style="min-width: 300px">
                                検索
                            </button>
                        </li>
                    </ul>
                    {{-- <div class="btnListContainer">
                        <div class="btnContainerMain">
                            <div class="btnContainerMainLeft">
                                <a type="button" href="{{ route('material.returnCreate.index') }}"
                                       class="btn-reset buttonBasic bColor-ok js-btn-reset">検索条件をクリア</a>
                                <input type="submit" value="検索"
                                       class="buttonBasic bColor-ok">
                            </div>
                            <div class="btnContainerMainRight">
                                <a href="{{ route('material.returnExcelExport', Request::all()) }}"
                                       class="btnExport">検索結果をEXCEL出力</a>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </form>
            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            @if(isset($supplyArrivals))
                <div class="tableWrap bordertable" style="clear: both;">
                    @if($supplyArrivals && $supplyArrivals->total() > 0)
                        <ul class="headerList">
                            {{ $supplyArrivals->total() }}件中、{{ $supplyArrivals->firstItem() }}件～{{ $supplyArrivals->lastItem() }} 件を表示してます
                        </ul>
                    @endif
                    <table class="tableBasic list-table">
                        <tbody>
                        <tr>
                            <th>返却日</th>
                            <th>伝票No.</th>
                            <th>材料メーカーコード</th>
                            <th>品番</th>
                            <th>品名</th>
                            <th>数量</th>
                            <th>加工率</th>
                            <th>伝票区分</th>
                            <th>操作</th>
                        </tr>
                        @forelse ($supplyArrivals as $supplyMaterialArrival )
                            <tr>
                                <td class="tA-cn">{{ $supplyMaterialArrival->arrival_day->format('Y-m-d') }}</td>
                                <td class="tA-cn">{{ $supplyMaterialArrival->delivery_no }}</td>
                                <td class="tA-cn">{{ $supplyMaterialArrival->material_manufacturer_code }}</td>
                                <td class="tA-cn">{{ $supplyMaterialArrival->material_no}}</td>
                                <td class="tA-le">{{ $supplyMaterialArrival->product?->product_name }}</td>
                                <td class="tA-cn">{{ $supplyMaterialArrival->arrival_quantity }}</td>
                                <td class="tA-cn">{{ $supplyMaterialArrival->processing_rate }}</td>
                                <td class="tA-cn">
                                    {{-- {{ $supplyMaterialArrival->voucher_class }} --}}
                                    @if($supplyMaterialArrival->voucher_class == 0)
                                        すべて
                                    @elseif($supplyMaterialArrival->voucher_class == 2)
                                        既受返品
                                    @elseif($supplyMaterialArrival->voucher_class == 3)
                                        材不返品
                                    @endif
                                </td>
                                <td class="tA-cn">
                                    <a href="{{ route('material.returnCreate.create', ['arrivalId' => $supplyMaterialArrival->id]) }}" class="buttonBasic bColor-ok js-btn-reset">編集</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    検索結果はありません
                                </td>
                            </tr>
                        @endforelse
                    
                        </tbody>
                    </table>
                </div>
                {{ $supplyArrivals->appends(request()->all())->links() }}
            @else
                <div class="tableWrap bordertable" style="clear: both;">
                    <table class="tableBasic list-table">
                        <tbody>
                        <tr>
                            <th>返却日</th>
                            <th>伝票No.</th>
                            <th>材料メーカーコード</th>
                            <th>品番</th>
                            <th>品名</th>
                            <th>数量</th>
                            <th>加工率</th>
                            <th>伝票区分</th>
                            <th>操作</th>
                        </tr>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '材料品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber',
        'hint' => 'prod_cat_zero'
    ]) 
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '材料メーカー',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'process_name',
        'model' => 'Process',
        'queryByField' => 'inside_and_outside_division=2',
    ]) 
@endsection
@push('scripts')
@vite('resources/js/material/return/index.js')
@endpush