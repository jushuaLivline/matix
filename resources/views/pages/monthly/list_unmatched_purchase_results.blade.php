@extends('layouts.app')
@section("title", "購入実績アンマッチ検索・一覧")
@push('styles')
    @vite('resources/css/index.css')
    <style>
        table td {
            vertical-align: middle !important;
        }

        a {
            text-decoration: none;
        }
    </style>
@endpush

@section('title', '購入実績アンマッチ検索・一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                購入実績アンマッチ検索・一覧
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <div class="box mb-3 bg-white p-3">
                    <form id="form" class="overlayedSubmitForm" data-disregard-empty="true">
                        <div class="mb-3 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">仕入先</label>
                                <div class="d-flex">
                                    <input type="text" 
                                            value="{{ Request::get('supplier_code') }}" 
                                            name="supplier_code" id="supplier_code"
                                            class="mr-2"
                                            style="width: 100px;">
                                    <input type="text"
                                        name="supplier_name"
                                        id="supplier_name"
                                        value="{{ Request::get('supplier_name') }}"
                                        disabled
                                        class="mr-2"
                                        >
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal"
                                        data-query="searchProductNumberModal"
                                        data-reference="customer_code"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mr-3">
                                <label class="form-label dotted indented">表示形式</label>
                                <div>
                                    <label for="option-1">
                                        <input type="radio" name="option" id="option-1" value="1" class="option-radio" checked />
                                        すべて
                                    </label>
                                    <label for="option-2">
                                        <input type="radio" name="option" id="option-2" value="2" class="option-radio" />
                                        値違い
                                    </label>
                                    <label for="option-3">
                                        <input type="radio" name="option" id="option-3" value="3" class="option-radio" />
                                        自社のみ存在
                                    </label>
                                    <label for="option-4">
                                        <input type="radio" name="option" id="option-4" value="0" class="option-radio" />
                                        仕入先のみ存在
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mr-3">
                                <label class="form-label dotted indented">確認</label>
                                <div>
                                    <label for="option-5">
                                        <input type="radio" name="option-1" id="option-5" value="1" class="option-radio" checked />
                                        すべて
                                    </label>
                                    <label for="option-6">
                                        <input type="radio" name="option-1" id="option-6" value="2" class="option-radio" />
                                        未確認
                                    </label>
                                    <label for="option-7">
                                        <input type="radio" name="option-1" id="option-7" value="3" class="option-radio" />
                                        確認済
                                    </label>
                                </div>
                            </div>                            
                        </div>

                        <a href="#" class="float-right btn bg-success btn-wide">検索結果をEXCEL出力</a>
                        <div class="text-center">
                            <a href="{{ route('monthly.listUnmatchedPurchaseResults') }}" class="btn bg-primary btn-wide" > 検索条件をクリア</a>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    <div class="mb-2">
                        @if(($purchaseRecords ?? []) != [])
                            {{ $purchaseRecords->total() }}件中、{{ $purchaseRecords->firstItem() }}件～{{ $purchaseRecords->lastItem() }} 件を表示しています
                        @endif
                        <table class="table table-bordered text-center table-striped-custom mw-100">
                            <thead>
                            <tr>
                                <th>確認</th>
                                <th>伝票No.</th>
                                <th width="5%" rowspan="2">伝票 <br>区分</th>
                                <th>品番</th>
                                <th>品名</th>
                                <th>入荷日</th>
                                <th>数量</th>
                                <th>単価</th>
                                <th>金額</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseRecords as $purchaseRecord)
                                    <tr>
                                        <td rowspan="2">
                                            <label class='radioClassic'>
                                                <input type="checkbox">
                                            </label>
                                        </td>
                                        <td rowspan="2" class="text-right">{{ $purchaseRecord->slip_no }}</td>
                                        <td class="text-left">購入</td>
                                        <td class="text-right">{{ $purchaseRecord->part_number }}</td>
                                        <td class="text-left">{{ $purchaseRecord->product_name }}</td>
                                        <td>{{  date('Y-m-d', strtotime($purchaseRecord->arrival_date)) }}</td>
                                        <td class="text-right">{{ $purchaseRecord->quantity }}</td>
                                        <td class="text-right">{{ $purchaseRecord->unit_price }}</td>
                                        <td class="text-right">{{ $purchaseRecord->amount_of_money }}</td>
                                        <td rowspan="2">
                                            <button class="btn btn-blue" style="width: 100%;">編集</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-left"><a href="#">購入</a></td>
                                        <td class="text-right"><a href="#">999999-999999</a></td>
                                        <td class="text-left"><a href="#">ZZZZZZZZ</a></td>
                                        <td><a href="#">YY/MM/DD</a></td>
                                        <td class="text-right"><a href="#">999</a></td>
                                        <td class="text-right"><a href="#">999</a></td>
                                        <td class="text-right"><a href="#">999</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="10">検索結果はありません</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        @if(($purchaseRecords ?? []) != [])
                        {{ $purchaseRecords->links() }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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



