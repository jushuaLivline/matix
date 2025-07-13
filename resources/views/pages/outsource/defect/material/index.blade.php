@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '材料不良実績一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                材料不良実績一覧
            </div>

            <form 
                action="{{  route('outsource.defect.material.index') }}"
                class="overlayedSubmitForm with-js-validation"  data-disregard-empty="true"
                id="defectMaterialForm">
                <div class="section">
                    <h1 class="form-label bar indented">検索</h1>
                    <div class="box mb-3">
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">返却日</label>
                                @php
                                    $date_start = now()->startOfMonth()->format('Ymd');
                                    $date_start = now()->endOfMonth()->format('Ymd');
                                @endphp
                                <div class="d-flex">
                                    @include('partials._date_picker', ['inputName' => 'return_date_from', 
                                            'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=返却日',  
                                             'value' => request()->has('return_date_from') ? request('return_date_from') : $date_start])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', ['inputName' => 'return_date_to',
                                                'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=返却日',  
                                             'value' => request()->has('return_date_to') ? request('return_date_to') : $date_start])
                                </div>
                                <div id="date_error_message"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">入力日</label>
                                <div class="d-flex">
                                    @include('partials._date_picker', ['inputName' => 'created_at_from',
                                        'attributes' => 'data-error-messsage-container=#created_error_message data-field-name=入力日',  
                                            'value' => request()->has('created_at_from') ? request('created_at_from') : $date_start])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', ['inputName' => 'created_at_to',
                                                'attributes' => 'data-error-messsage-container=#created_error_message data-field-name=入力日',  
                                             'value' => request()->has('created_at_to') ? request('created_at_to') : $date_start])
                                </div>
                                <div id="created_error_message"></div>
                            </div>
                        </div>
                        <br/>
                        <div class="mb-3 d-flex">
                            <div class="mr-4">
                                <label class="form-label dotted indented">製品品番</label>
                                <div class="d-flex">
                                    @php
                                        $product_code  =  request()->get('product_code') ?? '';
                                        $product_name  =  ($product_code) ? request()->get('product_name')  : '';
                                    @endphp
                                        <input type="text" name="product_code" id="product_code" 
                                            data-field-name="製品品番"
                                            data-error-messsage-container="#product_code_error"
                                            data-validate-exist-model="ProductNumber"
                                            data-validate-exist-column="part_number"
                                            data-inputautosearch-model="ProductNumber"
                                            data-inputautosearch-column="part_number"
                                            data-inputautosearch-return="product_name"
                                            data-inputautosearch-reference="product_name"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            value="{{ $product_code }}"
                                            class="w-150c mr-2">

                                        <input type="text" readonly 
                                            value="{{ $product_name  }}"
                                            class="middle-name mr-2" name="product_name" id="product_name">

                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProductModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                </div>
                                <div id="product_code_error"></div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">材料仕入先</label>
                                <div class="d-flex">
                                    <input type="text" id="supplier_code" 
                                                data-field-name="材料仕入先"
                                                data-error-messsage-container="#supplier_code_error"
                                                data-validate-exist-model="supplier"
                                                data-validate-exist-column="customer_code"
                                                data-inputautosearch-model="supplier"
                                                data-inputautosearch-column="customer_code"
                                                data-inputautosearch-return="supplier_name_abbreviation"
                                                data-inputautosearch-reference="supplier_name"
                                                name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ request()->get('supplier_code') }}">
                                    <input type="text" id="supplier_name" name="supplier_name" 
                                        readonly value="{{ request()->get('supplier_name') }}" style="margin-right: 5px;">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchSupplierModal"
                                            data-query-field="">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="supplier_code_error"></div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">工程</label>
                                <div class="d-flex">
                                    <div class="formPack mr-10c">
                                        <input type="text" name="process_code"
                                            data-field-name="工程"
                                            data-error-messsage-container="#rprocess_code_message"
                                            data-validate-exist-model="Process" 
                                            data-validate-exist-column="process_code"
                                            data-inputautosearch-model="Process" 
                                            data-inputautosearch-column="process_code"
                                            data-inputautosearch-return="abbreviation_process_name" 
                                            data-inputautosearch-reference="process_name"
                                            data-custom-required-error-message="材料メーカーを入力してください"
                                            maxlength="4"
                                            id="process_code" class="searchOnInput Process w-100c"
                                            value="{{ request()->get('process_code') ?? '' }}">
                                    </div>
                                    <div class="formPack fixedWidth box-middle-name mr-4  w-200c">
                                        <input type="text" readonly
                                            name="process_name"
                                            id="process_name"
                                            value="{{ request()->get('process_name') }}"
                                            class="middle-name text-left">
                                    </div>
                                    <div class="formPack fixedWidth w-20c ml-2">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProcessModal"
                                                data-query-field="inside_and_outside_division=2">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <div id="rprocess_code_message"></div>
                            </div>
                        </div>
    
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">伝票No</label>
                                <div class="d-flex">
                                    <input type="text" id="" value="{{ request()->get('slip_no') }}" name="slip_no"
                                        maxLength="20">
                                </div>
                            </div>
                        </div>
    
                        {{-- <a href="{{ route('outsources.material.defect.export', Request::all()) }}" class="float-right btn btn-success btn-wide">検索結果をEXCEL出力</a>
                        <div class="text-center">
                            <a href="{{ route('outsources.material.defect.list') }}" class="btn btn-primary btn-wide"> 検索条件をクリア</a>
                            <button class="btn btn-primary btn-wide" type="submit">検索</button>
                        </div> --}}
                        <div class="text-center">
                            <button type="button"
                                    class="btn btn-primary btn-wide"
                                    data-clear-inputs
                                    data-clear-form-target="#defectMaterialForm">
                                    検索条件をクリア
                            </button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        </div>
                        <a href="{{ route('outsource.defect.material.export.csv', Request::all()) }}" type="button" 
                                    class="float-right btn btn-success btn-wide {{ count($items) == 0 ? 'btn-disabled' : '' }}" 
                                    id="exportBtn"
                                    style="margin-top:-40px;">検索結果をEXCEL出力</a>
                    </div>
                </div>
            </form>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    <div class="mb-2">
                        @if(count($items) > 0)
                            {{ $items->total() }}件中、{{ $items->firstItem() }}件～{{ $items->lastItem() }} 件を表示しています
                        @endif
                        <table class="table table-bordered text-center table-striped-custom align-middle">
                            <thead>
                            <tr>
                                <th>返却日</th>
                                <th>製品品番</th>
                                <th>品名</th>
                                <th>材料仕入先</th>
                                <th>工程名</th>
                                <th>理由</th>
                                <th style="width: 8%;">数量</th>
                                <th style="width: 8%;">加工単価</th>
                                <th style="width: 5%;">加工率</th>
                                <th>金額</th>
                                <th>伝票No</th>
                                <th style="width: 150px;">操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($items as $item)
                                <tr data-id="{{ $item['id'] }}" id="row-{{ $item['id'] }}">
                                    <td>{{ $item->return_date->format('Ymd') }}</td>
                                    <td>{{ $item->product_number }}</td>
                                    <td>{{ $item?->product?->product_name }}</td>
                                    <td>{{ $item?->supplier_code }}</td>
                                    <td>{{ $item?->process?->abbreviation_process_name }}</td>
                                    <td>
                                        <select id="reason_code" style="width: 100%; height: 40px"  
                                                data-old-value="{{ $item['reason_code'] }}"
                                                disabled>
                                            @foreach ($reasons as $reason)
                                                <option 
                                                    value="{{ $reason->code }}"
                                                   
                                                    @if($item['reason_code'] == $reason->code) selected @endif>
                                                    {{ $reason->name }}
                                              </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input id="quantity" 
                                            value="{{ $item->quantity }}" 
                                            data-old-value="{{ $item->quantity }}" 
                                            maxlength="9"
                                            class="acceptNineDigitTwoDecimal"
                                            disabled>
                                    </td>
                                    <td>
                                        {{ $item?->product?->latestProductPrice?->unit_price ?? 0 }}
                                    </td>
                                    <td>
                                        <select style="width: 100%; height: 40px" id="processRate" data-old-value="{{ number_format($item->processing_rate, 0) }}" disabled>
                                            @for ($i = 0; $i <= 100; $i += 10)
                                                <option
                                                    @if(round($item->processing_rate) == $i) selected @endif>{{ $i }}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        {{ round($item->quantity * $item?->product?->latestProductPrice?->unit_price * ($item->processing_rate / 100)) }}
                                    </td>
                                    <td>{{ $item->slip_no }}</td>
                                    <td>
                                        <div class="center" id="EditDelete">
                                            {{--  Enable this button to enable update using ajax
                                    
                                            <button type="button" 
                                                class="btn btn-block btn-blue" id="edit"
                                                data-input-enable>編集</button>
                                                --}}
                                            
                                                {{-- Redirect to OUTSOURCE-52 to update the record  --}}
                                            <a href="{{ route('outsource.defect.material.edit', array_merge(['id' => $item['id']], request()->all())) }}"
                                                    class="btn btn-block btn-blue">編集</a>
                                        </div>
                                        
                                        <div class="center" id="UdpateUndo" style="display: none;">
                                            <button type="button"abbra
                                                data-item-id="{{ $item['id'] }}" 
                                                class="btn btn-block btn-green" 
                                                id="update"
                                                data-input-update>更新</button>
                                                <button  class="btn btn-block btn-gray" style="margin-left: 1px" id="undo" data-cancel-button>取消</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">検索結果はありません</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        @if(Request::get('return_date_from'))
                            {{ $items->appends(request()->all())->links() }}
                        @endif
                    </div>
                </div>
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
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
@endsection

@push('scripts')
    @vite(['resources/js/outsource/defect/material/index.js'])
@endpush


