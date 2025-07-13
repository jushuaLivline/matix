@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '未入荷一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                未入荷一覧
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form data-disregard-empty="true" class="overlayedSubmitForm with-js-validation"
                      id="outOfStockForm"
                      action="{{ route('outsource.arrival.pending.index') }}" method="GET">
                    <div class="box mb-3">
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">指示日</label>
                                <div class="d-flex">
                                @php
                                    $dateStart = request()->has('date_start') ? \Carbon\Carbon::parse(request('date_start'))->format(format: 'Ymd') : "";
                                    $dateEnd = request()->has('date_end') ? \Carbon\Carbon::parse(request('date_end'))->format('Ymd') : "";
                                @endphp
                                    
                                    @include('partials._date_picker', [
                                        'inputName' => 'instruction_date_from', 
                                        'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=指示日',
                                        'inputClass' => 'w-130c',
                                        'value' => $dateStart])
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    @include('partials._date_picker', [
                                        'inputName' => 'instruction_date_to', 
                                        'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=指示日',
                                        'inputClass' => 'w-130c',
                                        'value' => $dateEnd])
                                   
                                </div>
                                <div id="date_error_message"></div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">便No</label>
                                <div class="d-flex">
                                    <input type="text"
                                           id=""
                                           class="tA-le acceptNumericOnly"
                                           style="width: 60px"
                                           minlength="1"
                                            maxlength="2"
                                           value="{{ request()->get('incoming_flight_number_start') }}"
                                           name="incoming_flight_number_start"
                                           onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                    <span style="font-size:24px; padding:5px 10px;">
                                        ~
                                    </span>
                                    <input type="text"
                                           id=""
                                           class="tA-le acceptNumericOnly"
                                           style="width: 60px"
                                           minlength="1"
                                            maxlength="2"
                                           value="{{ request()->get('incoming_flight_number_end') }}"
                                           name="incoming_flight_number_end"
                                           onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                </div>
                            </div>
    
                            <div class="mr-3">
                                <label class="form-label dotted indented">仕入先</label>
                                <div class="d-flex">
                                    @php
                                        $supplier_code  =  request()->get('supplier_code') ?? '';
                                        $supplier_name  =  ($supplier_code) ? request()->get('supplier_name')  : '';
                                    @endphp
                                        <input type="text" id="supplier_code" 
                                                    data-field-name="仕入先"
                                                    data-error-messsage-container="#supplier_code_error"
                                                    data-validate-exist-model="supplier"
                                                    data-validate-exist-column="customer_code"
                                                    data-inputautosearch-model="supplier"
                                                    data-inputautosearch-column="customer_code"
                                                    data-inputautosearch-return="supplier_name_abbreviation"
                                                    data-inputautosearch-reference="supplier_name"
                                                    name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ $supplier_code  }}">
                                        <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ $supplier_name }}" style="margin-right: 10px;">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchSupplierModal"
                                                data-query-field="">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                 alt="magnifying_glass.svg">
                                        </button>
                                </div>
                                <div id="supplier_code_error"></div>
                            </div>
                        </div>
                        <br/>
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">発注No.</label>
                                <div class="d-flex">
                                    <input type="text"
                                           id=""
                                           class="tA-le acceptNumericOnly"
                                           style="width: 100%"
                                           name="order_no" value="{{  request()->get('order_no') }}">
                                </div>
                            </div>
    
                            <div class="mr-3">
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
                                            class="w-130c mr-2">

                                        <input type="text" readonly 
                                            value="{{ $product_name  }}"
                                            maxLength="20"
                                            class="middle-name mr-2" name="product_name" id="product_name">

                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchProductNumberModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                 alt="magnifying_glass.svg">
                                        </button>
                                </div>
                                <div id="product_code_error"></div>
                            </div>
                        </div>
    
                        {{-- <div class="text-center">
                            <div class="btnListContainer">
                                <div class="btnContainerMain">
                                    <div class="btnContainerMainLeft">
                                        <a href="{{ route('outsources.non.arrival.search') }}" value="検索条件をクリア"
                                            class="btn btn-primary btn-wide js-btn-reset">
                                            検索条件をクリア
                                        </a>
                                        <button class="btn-primary btn btn-wide">
                                            検索
                                        </button>
                                    </div>
                                    <div class="btnContainerMainRight">
                                        <a href="{{ route('outsources.non.arrival.export', Request::all()) }}" class="btn btn-success btn-wide">
                                            検索結果をEXCEL出力
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <br/>
                        <br/>
                        <div class="text-center">
                     
                            <button type="button" class="btn btn-primary btn-wide"
                            data-clear-inputs
                            data-clear-form-target="#outOfStockForm">検索条件をクリア</button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        </div>
                        <a href="{{ route('outsource.arrival.pending.export.csv', Request::all()) }}" type="button" 
                                    class="float-right btn btn-success btn-wide {{ $searchResults->total() == 0 ? 'btn-disabled' : '' }}" 
                                    id="exportBtn"
                                    style="margin-top:-40px;">検索結果をEXCEL出力</a>
                    </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    <div class="mb-2">
                        @if(count($searchResults) > 0)
                            {{ $searchResults->total() }}件中、{{ $searchResults->firstItem() }}件～{{ $searchResults->lastItem() }} 件を表示してます
                       
                        @endif
                        <table class="table table-bordered text-center table-striped-custom align-middle" style="width: 87%">
                            <thead>
                            <tr>
                                <th>発注No.</th>
                                <th>製品品番</th>
                                <th width="15%">品名</th>
                                <th width="15%">仕入先名</th>
                                <th>入荷日</th>
                                <th>指示日</th>
                                <th>便No.</th>
                                <th>指示数</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($searchResults as $searchResult)
                                    <tr>
                                        <td class="tA-le">{{ $searchResult->order_no ?? '' }}</td>
                                        <td class="tA-le">{{ $searchResult?->product_code ?? '' }}</td>
                                        <td class="tA-le">{{ $searchResult?->product?->product_name ?? '' }}</td>
                                        <td class="tA-le">{{ $searchResult?->supplier?->supplier_name_abbreviation ?? '' }}</td>
                                        <td class="tA-cn">{{ !empty($searchResult->arrival_day) ? \Carbon\Carbon::parse($searchResult->arrival_day)->format('Ymd') : '' }}
                                        </td>
                                        <td class="tA-cn">{{ !empty($searchResult->instruction_date) ? Carbon\Carbon::parse($searchResult->instruction_date)->format('Ymd') : '' }}</td>
                                        <td class="tA-cn">{{ $searchResult->incoming_flight_number ?? '' }}</td>
                                        <td class="tA-cn">{{ $searchResult->instruction_number ?? '' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">検索結果はありません</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(Request::all())
                        {{ $searchResults->appends(request()->all())->links() }}
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
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])
@endsection
