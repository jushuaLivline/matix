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

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
                    <div class="box mb-3">
                        <a href="{{ route('order.data.acquisition') }}" class="float-right">CSVファイルでの取込はこちら</a>
                        <div class="mb-3">
                            <label class="form-label dotted indented">年月</label>
                            <div>
                                <input type="text" name="year_month" value="{{ Request::get("year_month") ?? date('Ym') }}">
                            </div>
                        </div>
                        <div class="mb-3" style="display:flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">納入先</label>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-25 mr-2">
                                        <input type="text" name="customer_code"  value="{{ Request::get('customer_code') }}" id="customer_code">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p mr-25 mr-2">
                                        <input type="text" readonly
                                        id="customer_name"
                                        value="{{ Request::get('customer_name') }}"
                                        name="customer_name" class="middle-name">
                                    </p>
                                    
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchCustomerModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                </div>
                            </div>
                            <div style="flex:1">
                                <label class="form-label dotted indented">受入</label>
                                <div>
                                    <input type="text" name="acceptance" value="{{ Request::get("acceptance") }}" style="width:50px">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label dotted indented">製品品番</label>
                            <div class="d-flex">
                                <p class="formPack fixedWidth fpfw25p mr-25 mr-2">
                                    <input type="text" 
                                        id="part_number_first" 
                                        name="part_number_first" 
                                        value="{{ Request::get("part_number_first") }}">
                                    </p>
                                <p class="formPack fixedWidth fpfw50 box-middle-name mr-25 mr-2">
                                <input type="text" readonly
                                    id="product_name_first"
                                    value="{{ Request::get("product_name_first") }}"
                                    name="product_name_first">
                                </p>
                                
                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchPartNumberModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                                <span style="font-size:24px; padding:0px 10px;">
                                    ~
                                </span>
                                <p class="formPack fixedWidth fpfw25p mr-25 mr-2">
                                    <input type="text" 
                                        id="part_number_second" 
                                        value="{{ Request::get("part_number_second") }}"
                                        name="part_number_second"
                                        >
                                </p>
                                <p class="formPack fixedWidth fpfw25p mr-25 mr-2">
                                    <input type="text" readonly
                                        id="product_name_second"
                                        value="{{ Request::get("product_name_second") }}"
                                        name="product_name_second">
                                </p>

                                <p class="formPack fixedWidth fpfw25p">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchPartNumberModalSecond">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                        </div>


                        <div class="text-center">
                            <a href="{{ route("order.kanban.input") }}"
                                class="btn btn-primary btn-wide">検索条件をクリア</a>
                            <button type="submit" class="btn btn-primary btn-wide">
                                検索 
                            </button>
                        </div>
                    </div>
                    
                </form>
            </div>
            @if (session('success'))
                <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: center;">
                        <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
                @php
                    session()->forget('success');
                @endphp
            @endif
            <form action="{{ route('order.kanban.input.save') }}" class="overlayedSubmitForm"  method="POST" >
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
                                        {{ $result->edited_part_number }}
                                    </td>
                                    <td>{{ $result->product_name }}</td>
                                    @foreach($yearMonths as $index => $yearMonth)
                                        <td>
                                            <input type="text" class="full-width" name="value_{{ $result->id }}_{{ $monthIndexColumn[$index] }}" value="{{ $result->{$monthIndexColumn[$index]} }}">
                                        </td>
                                    @endforeach
                                    <td>
                                        <button type="button" onclick="if(confirm('「内示情報を削除します、よろしいでしょうか？」')){ $(this).parents('tr').find('input').val(0);}" class="btn btn-block btn-orange">削　除</button>
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
                                    'part_number_second' => Request::get("part_number_second"),
                                    'product_name_second' => Request::get("product_name_second"),
                                ])
                                ->links() }}
                    </div>
                </div>
                <div class="float-right">
                    <button type="submit" class="btn btn-green"> この内容で登録する </button>
                </div>

                
            </form>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'part_number_first',
        'resultNameElementId' => 'product_name_first',
        'model' => 'Product'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModalSecond',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'part_number_second',
        'resultNameElementId' => 'product_name_second',
        'model' => 'Product'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer'
    ])
@endsection
