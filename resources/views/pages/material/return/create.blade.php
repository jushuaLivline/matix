@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/index.css')
    @vite('resources/css/material/return/create.css')

    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
   @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
@endpush

@section('title', '返品実績入力')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>返品実績入力</span></h1>
            </div>
            
            @if(session('success'))
                <div class="tableWrap borderLesstable text-left">
                    <div class="success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
    
            @if(session('error'))
                <div class="tableWrap borderLesstable text-left">
                    <div class="error">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            
            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('material.returnCreate.create') }}" 
                accept-charset="utf-8" id="supplyMaterialReturnedForm" 
                data-disregard-empty="true" class="with-js-validation">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                            <!-- 得意先 -->
                            <td>
                            <dl class="formsetBox">
                                    <dt class="requiredForm">製品品番</dt>
                                    <dd>
                                        @php
                                           $productMaterialNo = (isset($requestData['arrivalId']) && $requestData['arrivalId']) 
                                                ? ( $supplyMaterialArrival?->material_no  ?? '')
                                                : ($requestData['part_number'] ?? "");

                                            $productMaterialName = (isset($requestData['arrivalId']) && $requestData['arrivalId']) 
                                                ? ($supplyMaterialArrival->product->product_name ?? '') 
                                                : ($requestData['product_name'] ?? "");

                                        @endphp
                                        
                                        <div class="d-flex">
                                            <p class="formPack fixedWidth fpfw25p mr-1">
                                                <input type="text" class="row-input searchOnInput ProductMaterial text-left" 
                                                id="product_code"
                                                name="part_number" 
                                                data-field-name="製品品番"
                                                data-error-messsage-container="#product_code_error"
                                                data-validate-exist-model="ProductNumber"
                                                data-validate-exist-column="part_number"
                                                data-inputautosearch-model="ProductNumber"
                                                data-inputautosearch-column="part_number"
                                                data-inputautosearch-return="name_abbreviation"
                                                data-inputautosearch-reference="product_name"
                                                value="{{ $productMaterialNo ?? '' }}"
                                                required>
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name mr-1">
                                                <input type="text" readonly value="{{ $productMaterialName ?? '' }}"
                                                    class="middle-name text-left" 
                                                    name="product_name" 
                                                    id="product_name_input"
                                                    input-product-number >
                                            </p>
                                            <button type="button" class="btnSubmitCustom js-modal-open mr-5c ml-10c"        
                                                    data-target="searchProductModal"
                                                    data-query-field="product_category=1"
                                                    >
                                                <img src="/images/icons/magnifying_glass.svg" alt="magnifying_glass.svg">
                                            </button>
                                            {{-- 
                                            <button type="button" class="btnSubmitCustom p-2 js-modal-open"
                                                data-part-number="" 
                                                data-part-name="" 
                                                data-target="productMaterialHierarchyModal"
                                                data-open-material-hierarchy-modal>
                                                <span class="fa fa-solid fa-bars-staggered" style="font-size: 17px;"></span>
                                            </button>
                                            --}}
                                        </div> 
                                        <!-- <div id="product_code_error"></div> -->
                                        <div id="product_code_error"></div>
                                        
                                    </dd>
                                </dl>
                            </td>
                        </tbody>
                    </table>
                    
                    <div class="text-center">
                        <button type="button"
                            class="btn btn-blue" style="min-width: 200px"
                            data-clear-inputs
                            data-clear-form-target="#supplyMaterialReturnedForm">検索条件をクリア</button>
                        <button type="submit" class="btn btn-blue" style="min-width: 200px">検索</button>
                    </div>
                    
                </div>
            </form>
            
            @if($errors->any())
                <div class="tableWrap borderLesstable message">
                    <div class="error">
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                </div>
            @endif
        
            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            @php
                $action = isset($requestData['arrivalId']) ? route('material.returnCreate.update', $requestData['arrivalId']) : route('material.returnCreate.store');
            @endphp
            <form action="{{ $action }}" 
                accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" method="POST" 
                data-confirmation-message="返品情報を更新します、よろしいでしょうか？">
                @csrf
                @if (isset($requestData['arrivalId']))
                    @method('PUT')
                    <input type="hidden" name="updator" value="{{ Auth::user()->id }}">
                    <input type="hidden" name="arrivalId" value="{{ $supplyMaterialArrival->id ?? ''}}">
                @endif
                <div class="tableWrap bordertable" style="clear: both;">
                    <table class="tableBasic list-table">
                        <thead>
                            <tr>
                                <th>材料品番</th>
                                <th>品名</th>
                                <th width="15%">区分</th>
                                <th width="8%">数量</th>
                                <th width="8%">加工率(%)</th>
                                <th>伝票No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(Request::get('arrivalId') && $supplyMaterialArrival)
                                <tr>
                                    <td class="tA-cn">
                                        {{ $supplyMaterialArrival->material_no ?? '' }}
                                    </td>
                                    <td class="tA-le">{{ $supplyMaterialArrival->product?->product_name ?? '' }}</td>
                                    <td class="tA-cn">{{ $supplyMaterialArrival?->configuration?->material_classification ?? '' }}</td>
                                    <td class="tA-cn">
                                        <input type="number" min="1" name="arrival_quantity"
                                            value="{{ $supplyMaterialArrival->arrival_quantity?? '' }}" 
                                            class="detail-number-input arrivalQuantity acceptNumericOnly"
                                            data-accept-zero="true"
                                            maxlength="9">
                                    </td>
                                    <td class="tA-cn">
                                    <input type="text" min="1" name="processing_rate"
                                            value="{{ $supplyMaterialArrival->processing_rate?? '' }}" 
                                            class="detail-number-input arrivalQuantity acceptNumericOnly"
                                            data-accept-zero="true"
                                            maxlength="9">
                                        <!-- <select name="processing_rate">
                                            @for ($value = 10; $value <= 100; $value += 10)
                                                <option @if($value == ($supplyMaterialArrival?->processing_rate ?? ''))selected @endif value="{{ $value }}">{{ $value }}</option>
                                            @endfor
                                        </select> -->
                                    </td>
                                    <td class="tA-cn">
                                        <input type="text" name="delivery_no"
                                            value="{{$supplyMaterialArrival->delivery_no ?? ''}}" class="detail-number-input deliveryNo">
                                    </td>
                                    
                                </tr>
                            @else
                                @forelse ($productMaterials as $index => $material)
                                @php
                                    $counter = ($index == 0) ? 1 : $index
                                @endphp
                                    <tr>
                                        <td class="tA-cn">
                                            {{ $material->part_number }}
                                        </td>
                                        <td class="tA-le">{{ $material->product_name }}</td>
                                        <td class="tA-cn">{{ $material?->material_classification }}</td>
                                        <td class="tA-cn">
                                            <input type="number" name="arrival_quantity[]"
                                                value="" class="detail-number-input arrivalQuantity acceptNumericOnly"
                                                data-accept-zero="true"
                                                maxlength="9">
                                        </td>
                                        <td class="tA-cn">
                                        <input type="text" min="1" name="processing_rate[]"
                                            value="{{ $supplyMaterialArrival->processing_rate?? '' }}" 
                                            class="detail-number-input arrivalQuantity acceptNumericOnly"
                                            data-accept-zero="true"
                                            maxlength="3">
                                            <!-- <select name="processing_rate[]">
                                                @for ($value = 10; $value <= 100; $value += 10)
                                                    <option value="{{ $value }}">{{ $value }}</option>
                                                @endfor
                                            </select> -->
                                        </td>
                                        <td class="tA-cn">
                                            <input type="text" name="delivery_no[]"
                                                value="" class="detail-number-input deliveryNo"
                                                maxlength="20">
                                        </td>
                                        <input type="hidden" name="voucher_class[]" value="3">
                                        <input type="hidden" name="flight_no[]" value="">
                                        <input type="hidden" name="creator[]" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="created_at[]" value="{{ now() }}">
                                        <input type="hidden" name="dataId[]" value="{{ $material->id }}">
                                        <input type="hidden" name="supplier_code[]" value="{{ $material->supplier_code }}">
                                        <input type="hidden" name="department_code[]" value="{{ $material->department_code }}">
                                        <input type="hidden" name="line_code[]" value="{{ $material->line_code }}">
                                        <input type="hidden" name="product_number[]" value="{{ $material->part_number }}">
                                        <input type="hidden" name="material_manufacturer_code[]" value="{{ $material->material_manufacturer_code }}">
                                        <input type="hidden" name="material_no[]" value="{{ $material->part_number }}">
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            検索結果はありません
                                        </td>
                                    </tr>
                                @endforelse
                            @endif
            
                        </tbody>
                    </table>
                </div>
                <div class="pagettlWrap">
                    <h1><span>返品入力</span></h1>
                </div>
            
            
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                            <!-- 返却日 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt class="requiredForm">返却日</dt>
                                    <dd>
                                        <p class="formPack fixedWidth w-50">
                                            <input type="text" 
                                            name="arrival_day" 
                                            id="arrival_day" 
                                            data-field-name="返却日"
                                            data-format="YYYYMMDD"
                                            data-validate-date-format="YYYYMMDD"
                                            data-error-messsage-container="#request_error_message"
                                            class="arrival_day"
                                            @if(isset($requestData['arrivalId']))
                                            value="{{ $supplyMaterialArrival?->arrival_day?->format('Ymd') }}"
                                            @endif
                                            minlength="8"
                                            maxlength="8"
                                            pattern="\d*" 
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            style="text-align: center" 
                                            required
                                            >
                                            
                                        </p>
                                        <p class="formPack fixedWidth fixedWidth fpfw25p">
                                            <button type="button" class="btnSubmitCustom buttonPickerJS" 
                                                    data-target="arrival_day"
                                                    data-format="YYYYMMDD">
                                                <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                            </button>
                                        </p>
                                        <p style="color: #dd0000; font-weight: bold" id="request_error_message"></p>
                                    </dd>
                                </dl>
                            </td>
                        </tbody>
                    </table>
                </div>

                
            
                
                <div class="buttonDetail">
                    <button type="submit" data-save-button class="btn btn-block btnSubmitGreen ml-half">この内容で登録する</button>
                </div>
                <div class="buttonDetail">
                    @if(isset($requestData['arrivalId']))
                        <button type="button" 
                            data-delete-button 
                            data-supply-material-order-id="{{ $supplyMaterialArrival->id ?? ''}}" 
                            data-redirect-url="{{ route('material.returnCreate.create') }}"
                            class="btn btn-block btn-orange" id="delete">削除</button>
                    @endif
                </div>

            </form>

     @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
    
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name_input',
        'model' => 'ProductNumber'
    ])

@endsection
@php
    $dataConfigs['ProductMaterial'] = [
        'model' => 'ProductNumber',
        'reference' => 'product_name',
    ];
@endphp

{{-- 
<x-search-on-input :dataConfigs="$dataConfigs" />
<div id="materialHierarchyComponent" style="display: none;">
<x-product-material-hierarchy-modal modalId="productMaterialHierarchyModal" />
</div>
--}}


@push('scripts')
    @vite('resources/js/material/return/create.js');
@endpush