@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '品番マスタ一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>品番マスタ一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('master.products.index')  }}" method="GET" accept-charset="utf-8" class="overlayedSubmitForm">
                <div class="tableWrap borderLesstable inputFormArea">
                    <div class="row-content">
                        <!-- 品番 -->
                        <div class="flex-row">
                            <label for="part_number" class="label_for">品番</label>
                            <div class="search-group">
                                <input type="text" class="row-input searchOnInput ProductMaterial" id="product_number" name="part_number" value="{{ Request::get('part_number') }}">
                                <button type="button" class="btnSubmitCustom p-2 js-modal-open"
                                        data-part-number="" 
                                        data-part-name="" 
                                        data-target="productMaterialHierarchyModal">
                                        <span class="fa fa-solid fa-bars-staggered" style="font-size: 17px;"></span>
                                    </button>
                            </div>
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
                        <!-- 仕入先 -->
                        <div class="flex-row">
                            <label for="supplier_code" class="label_for">仕入先</label>
                            <div class="search-group">
                                <input type="text" id="supplier_code" name="supplier_code" value="{{ Request::get('supplier_code') }}" class="" style="width: 100px">
                                <input type="text" readonly
                                        id="supplier_name"
                                        name="supplier_name"
                                        value="{{ Request::get('supplier_name') }}"
                                        class="middle-name"
                                        style="width: 170px">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row-group-content" style="gap: 30px">
                        <!-- 取引先 -->
                        <div class="flex-row">
                            <label for="product_category" class="label_for">製品区分</label>
                            <select name="product_category" id="product_category" class="classic" style="width: 130px">
                                <option value="">すべて</option>
                                @foreach ($productCategory as $key => $category)
                                    <option value="{{ $key }}" {{ Request::get('product_category') && Request::get('product_category') == $key ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- 仕入先 -->
                        <div class="flex-row">
                            <label for="delete_flag" class="label_for">有効/無効</label>
                            <select name="delete_flag" id="delete_flag" class="classic">
                                <option value="2" {{ Request::get('delete_flag') == 2 ? 'selected' : '' }}>すべて</option>
                                <option value="0" {{ Request::get('delete_flag') == 0 ? 'selected' : '' }}>有効</option>
                                <option value="1" {{ Request::get('delete_flag') == 1 ? 'selected' : '' }}>無効</option>
                            </select>
                        </div>
                        <!-- 仕入先 -->
                        <div class="flex-row">
                            <label for="production_division" class="label_for">生産区分</label>
                            <select name="production_division" id="production_division" class="classic">
                                <option value="">すべて</option>
                                @foreach ($productionDivision as $key => $division)
                                    <option value="{{ $key }}" {{ Request::get('production_division') && Request::get('production_division') == $key ? 'selected' : '' }}>{{ $division }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <a id="export_csv_1" 
                            href="{{ route('master.products.exportCsv', array_merge(Request::query())) }}" 
                            class="btn btn-success btn-wide float-right">
                            検索結果をEXCEL出力
                        </a>
                        <div class="text-center">
                            <a href="{{ route("master.products.index") }}" class="btn btn-primary btn-wide js-btn-reset-reload">検索条件をクリア</a>
                            <button class="btn btn-primary btn-wide" type="submit">検索</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    @if (count($products) > 0)
                        <li>{{ $count }}件中、{{ $products->firstItem() }}件～{{ $products->lastItem() }} 件を表示してます</li>
                    @else
                        <li></li>
                    @endif
                    <li>
                        <a href="{{ route('master.products.create') }}" class="buttonBasic bColor-ok">
                            新規登録
                        </a>
                    </li>
                </ul>
                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th>品番</th>
                        <th>品名</th>
                        <th>ライン</th>
                        <th>部門</th>
                        <th>得意先</th>
                        <th>仕入先</th>
                        <th>製品区分</th>
                        <th style="width: 100px">操作</th>
                    </tr>
                    @if (count($products) <= 0)
                        @include('partials._no_record', ['colspan' => 8])
                    @else
                        @foreach ($products as $product)
                            <tr>
                                <td class="tA-le">{{ $product->part_number }}</td>
                                <td class="tA-le">{{ $product->product_name }}</td>
                                <td class="tA-le">{{ isset($product->line) ? $product->line?->line_name : '' }}</td>
                                <td class="tA-le">{{ isset($product->department) ? $product->department?->name : '' }}</td>
                                <td class="tA-le">{{ isset($product->customer) ? $product->customer?->customer_name : '' }}</td>
                                <td class="tA-le">{{ isset($product->supplier) ? $product->supplier?->supplier_name_abbreviation : '' }}</td>
                                <td class="tA-le" style="text-align: center">{{ $product->product_category }}</td>
                                <td class="tA-cn" style="display: flex; gap: 4px;">
                                    <a href="{{ route('master.products.edit', ['id' => $product->id]) }}" class="buttonBasic bColor-ok" style="width: 100px">
                                        編集
                                    </a>
                                    <a href="{{ route('master.products.productionSimulation') }}" class="btnExport buttonBasic">計画CSVのDL</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    </tbody>
                </table>
                @if (count($products) > 0)
                    {{ $products->links() }}
                @endif
            </div>
            {{-- @include('partials._pagination') --}}
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
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '材料',
        'resultValueElementId' => 'product_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

@php
    $dataConfigs['ProductMaterial'] = [
        'model' => 'ProductNumber',
        'reference' => 'product_name'
    ];
@endphp

<x-search-on-input :dataConfigs="$dataConfigs" />
<x-product-material-hierarchy-modal modalId="productMaterialHierarchyModal" />

@endsection
@push('scripts')
@vite(['resources/js/master/products/index.js'])
@endpush