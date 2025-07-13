@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '製品在庫検索・一覧')
@section('content')
<div class="content">
    <div class="contentInner">
        <div class="accordion">
            <h1><span>製品在庫検索・一覧</span></h1>
        </div>
        <div class="pagettlWrap">
            <h1><span>検索</span></h1>
        </div>
        <form  accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
            <div class="tableWrap borderLesstable inputFormArea">
                <div class="row-content">
                    <!-- 品番 -->
                    <div class="flex-row">
                        <label for="part_number" class="label_for">品番</label>
                        <input type="text" class="row-input" id="part_number" name="part_number" value="{{ Request::get('part_number') }}">
                    </div>
                    <!-- 品名 -->
                    <div class="flex-row">
                        <label for="product_name" class="label_for">品名</label>
                        <input type="text" class="row-input" id="product_name" name="product_name" value="{{ Request::get('product_name') }}">
                    </div>
                </div>
                <div class="row-group-content">
                    <!-- ライン -->
                    <div class="flex-row">
                        <label for="line_code" class="label_for">ライン</label>
                        <div class="search-group">
                            <input type="text" id="line_code" name="line_code" value="{{ Request::get('line_code') }}" class="" style="width: 80px">
                            <input type="text" readonly
                                    id="line_name"
                                    name="line_name"
                                    value="{{ Request::get('line_name') }}"
                                    class="middle-name"
                                    style="width: 135px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchLineModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>
                    <!-- 部門 -->
                    <div class="flex-row">
                        <label for="department_code" class="label_for">部門</label>
                        <div class="search-group">
                            <input type="text" id="department_code" name="department_code" value="{{ Request::get('department_code') }}" class="" style="width: 100px">
                            <input type="text" readonly
                                    id="department_name"
                                    name="department_name"
                                    value="{{ Request::get('department_name') }}"
                                    class="middle-name"
                                    style="width: 170px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchDepartmentModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row-group-content">
                    <!-- 取引先 -->
                    <div class="flex-row">
                        <label for="customer_code" class="label_for">取引先</label>
                        <div class="search-group">
                            <input type="text" id="customer_code" name="customer_code" value="{{ Request::get('customer_code') }}" class="" style="width: 100px">
                            <input type="text" readonly
                                    id="customer_name"
                                    name="customer_name"
                                    value="{{ Request::get('customer_name') }}"
                                    class="middle-name"
                                    style="width: 170px">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchCustomerModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row-group-content" style="gap: 30px">
                    <!-- 有効/無効 -->
                    <div class="flex-row">
                        <label for="delete_flag" class="label_for">有効/無効</label>
                        <select name="delete_flag" id="delete_flag" class="classic">
                            <option value="2" {{ Request::get('delete_flag') == 2 ? 'selected' : '' }}>すべて</option>
                            <option value="0" {{ Request::get('delete_flag') == 0 ? 'selected' : '' }}>有効</option>
                            <option value="1" {{ Request::get('delete_flag') == 1 ? 'selected' : '' }}>無効</option>
                        </select>
                    </div>

                </div>
                <ul class="buttonlistWrap" style="width: 0;">
                    <li>
                        <div class="parent">
                            <div>
                                <a href="{{ route("master.products.index") }}"
                                    class="buttonBasic btn-reset bColor-ok js-btn-reset-reload" style="width: 250px!important">検索条件をクリア</a>
                            </div>
                            <div>
                                <input type="submit" value="検索"
                                    class="buttonBasic bColor-ok" style="width: 250px!important">
                            </div>
                            <div>
                                <a href="#" id="export_csv" class="btnExport buttonBasic">検索結果をEXCEL出力</a>
                                {{-- <a href="{{ route("estimate.index") }}"
                                class="buttonBasic btn-reset bColor-ok js-btn-reset-reload" style="width: 250px!important; background-color: green;">検索結果をEXCEL出力 --}}
                            </a>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </form>
        <div class="pagettlWrap">
            <h1><span>検索結果</span></h1>
        </div>
        <div class="tableWrap bordertable" style="clear: both;">
            <ul class="headerList">
                @if (count($products) > 0)
                    <li>{{ $count }}件中、{{ $products->firstItem() }}件～{{ $products->lastItem() }} 件を表示してます</li>
                @endif
            </ul>
            <table class="tableBasic list-table">
                <tbody>
                    <tr>
                        <th>品番</th>
                        <th>品名</th>
                        <th>ライン</th>
                        <th style="width: 450px;">部門</th>
                        <th>在庫数</th>
                    </tr>
                    @if (count($products) <= 0)
                        @include('partials._no_record', ['colspan' => 8])
                    @else
                        @foreach ($products as $product)
                            <tr>
                                <td class="tA-le">{{ $product->part_number }}</td>
                                <td class="tA-le">{{ $product->product_name }}</td>
                                <td class="tA-le">{{ isset($product->line) ? $product->line : '' }}</td>
                                <td class="tA-le">{{ isset($product->department) ? $product->department : '' }}</td>
                                <td class="tA-le">{{ $product->stock }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            @if (count($products) > 0)
                {{ $products->links() }}
            @endif
        </div>
    </div>
</div>
@include('partials.modals.masters._search', [
    'modalId' => 'searchDepartmentModal',
    'searchLabel' => '部門',
    'resultValueElementId' => 'department_code',
    'resultNameElementId' => 'department_name',
    'model' => 'Department'
])
@include('partials.modals.masters._search', [
    'modalId' => 'searchCustomerModal',
    'searchLabel' => '取引先',
    'resultValueElementId' => 'customer_code',
    'resultNameElementId' => 'customer_name',
    'model' => 'NotSupplier'
])
@include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '仕入先',
    'resultValueElementId' => 'supplier_code',
    'resultNameElementId' => 'supplier_name',
    'model' => 'Supplier'
])
@include('partials.modals.masters._search', [
    'modalId' => 'searchLineModal',
    'searchLabel' => 'ラインコード',
    'resultValueElementId' => 'line_code',
    'resultNameElementId' => 'line_name',
    'model' => 'Line'
])
@endsection
@push('scripts')
    @vite(['resources/js/stock-inventory/data-form.js'])
@endpush