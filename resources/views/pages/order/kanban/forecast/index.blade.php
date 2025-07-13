@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/order/style.css')
@endpush

@section('title', 'かんばん品内示情報入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                かんばん品内示情報入力
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

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form accept-charset="utf-8" id="form" class="overlayedSubmitForm with-js-validation" data-disregard-empty="true">
                    <div class="box mb-3">
                         <div class="mb-3 d-flex">
                            <div class="w-80">
                                <label class="form-label dotted indented">年月</label>
                                <div class="w-20 d-flex">
                                    @include('partials._date_picker_year_month', [
                                        'inputName' => 'year_month', 
                                        'attributes' => 'data-error-messsage-container=#date_error data-field-name=年月',
                                        'maxlength'=>'6', 'minlength'=>'6'])
                                </div>
                                <div id="date_error"></div>
                            </div>
                            <div class="w-20">
                                <a href="{{ route('order.data.acquisition') }}" class="float-right">CSVファイルでの取込はこちら</a>
                            </div>
                        </div>
                        <div class="mb-3 d-flex">
                            <div class="mr-5">
                                <label class="form-label dotted indented">納入先</label>

                                <div class="d-flex">
                                    <input type="text" id="supplier_code" 
                                                data-field-name="納入先"
                                                data-error-messsage-container="#supplier_code-error"
                                                data-validate-exist-model="supplier"
                                                data-validate-exist-column="customer_code"
                                                data-inputautosearch-model="supplier"
                                                data-inputautosearch-column="customer_code"
                                                data-inputautosearch-return="supplier_name_abbreviation"
                                                data-inputautosearch-reference="supplier_name"
                                                name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ request()->get('supplier_code') }}">
                                    <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ request()->get('supplier_name') }}" style="margin-right: 10px;">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchSupplierModal"
                                            data-query-field="">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="supplier_code-error"></div>
                                
                            </div>
                            <div class="w-40">
                                <label class="form-label dotted indented">受入</label>
                                <div>
                                    <input type="text" name="acceptance" value="{{ Request::get("acceptance") }}" style="width:50px" maxlength="2">
                                </div>
                            </div>
                        </div>
                        <div class="mb-5 d-flex">
                            <div class="w-80">
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
                                            value="{{ $product_code }}"
                                            class="w-130c mr-2">

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
                        </div>

                        <div class="text-center">
                            <button type="button" class="btn btn-primary btn-wide" data-clear-inputs data-clear-form-target="#form">検索条件をクリア</button>
                            <button type="submit" class="btn btn-primary btn-wide">検索 </button>
                        </div>
                    </div>
                    
                </form>
            </div>

            <form action="{{ route('order.kanbanForecast.store') }}" class="overlayedSubmitForm with-js-validation"  method="POST" 
            data-confirmation-message="かんばん品内示情報を登録します、よろしいでしょうか？">
                @csrf
                <div class="section">
                    <h1 class="form-label bar indented">検索結果</h1>
                    <div class="box">
                        <ul class="headerList">
                            {{ $results->total() }}件中、{{ $results->firstItem() }}件～{{ $results->lastItem() }} 件を表示してます
                        </ul>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>製品品番</th>
                                    <th>品名</th>
                                    @foreach($yearMonths as $yearMonth)
                                        <th>{{ $yearMonth->format('Y') }}年{{ $yearMonth->format('m') }}月</th>
                                    @endforeach
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($results as $result)
                                <tr>
                                    <td>
                                        <input type="hidden" name="ids[]" value="{{ $result->id }}">
                                        {{ $result->part_number }}
                                    </td>
                                    <td>{{ $result->product_name }}</td>
                                    @foreach($yearMonths as $index => $yearMonth)
                                        <td>
                                            <input type="text" class="full-width" name="value_{{ $result->id }}_{{ $monthIndexColumn[$index] }}" value="{{ $result->{$monthIndexColumn[$index]} }}">
                                        </td>
                                    @endforeach
                                    <td>
                                        <button type="button" onclick="if(confirm('内示情報を削除します、よろしいでしょうか？')){ $(this).parents('tr').find('input:not([type=hidden]').val(0);}" class="btn btn-block btn-orange">削　除</button>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="{{ count($yearMonths) + 3 }}">検索結果はありません</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $results
                                ->appends([
                                    'year_month' => Request::get("year_month"),
                                    'customer_code' => Request::get("customer_code"),
                                    'customer_name' => Request::get("customer_name"),
                                    'acceptance' => Request::get("acceptance"),
                                    'part_number_first' => Request::get("part_number_first"),
                                    'product_name_first' => Request::get("product_name_first"),
                                ])
                                ->links() }}
                    </div>
                </div>
                <div class="float-right">
                    <button type="submit" class="btn btn-green {{ empty($results[0]) ? 'btn-disabled' : '' }}"> この内容で登録する </button>
                </div>

                
            </form>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
@endsection
