@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/master/part/part.css')
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

        <form id="search-form" accept-charset="utf-8" class="overlayedSubmitForm">
            <div class="tableWrap borderLesstable">
                <div class="d-flex mb-4">
                    <div class="flex-row p-0 mr-4">
                        <label for="part_number" class="label_for">品番</label>
                        <div class="search-group">
                            <input type="text" id="part_number" name="part_number"
                                value="{{ Request::get('part_number') }}"
                            >
                        </div>
                    </div>

                    <div class="flex-row">
                        <label for="product_name" class="label_for">品名</label>
                        <input type="text" class="row-input" id="product_name" name="product_name"
                            value="{{ Request::get('product_name') }}"
                        >
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="flex-row p-0 mr-4">
                        <label for="line_code" class="label_for">ライン</label>
                        <div class="search-group">
                            <input type="text" id="line_code" name="line_code"
                                value="{{ Request::get('line_code') }}"
                                maxlength="3"
                                class="acceptNumericOnly w-100px fetchQueryName"
                                data-model="Line"
                                data-query="line_code"
                                data-query-get="line_name"
                                data-reference="line_name"
                                >
                            <input type="text" readonly
                                id="line_name"
                                name="line_name"
                                value="{{ Request::get('line_name') }}"
                                class="middle-name"
                            >
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchLineModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>

                    <div class="flex-row">
                        <label for="department_code" class="label_for">部門</label>
                        <div class="search-group">
                            <input type="text" id="department_code" name="department_code"
                                value="{{ Request::get('department_code') }}"
                                class="acceptNumericOnly w-100px fetchQueryName"
                                data-model="Department"
                                data-query="code"
                                data-query-get="department_name"
                                data-reference="department_name"
                                maxlength="6"
                                >
                            <input type="text" readonly
                                    id="department_name"
                                    name="department_name"
                                    value="{{ Request::get('department_name') }}"
                                    class="middle-name"
                                    >
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchDepartmentModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="flex-row p-0 mr-4">
                        <label for="customer_code" class="label_for">取引先</label>
                        <div class="search-group">
                            <input type="text" id="customer_code" name="customer_code"
                                value="{{ Request::get('customer_code') }}"
                                class="acceptNumericOnly w-100px fetchQueryName"
                                data-model="Customer"
                                data-query="customer_code"
                                data-query-get="supplier_name_abbreviation"
                                data-reference="customer_name"
                                maxlength="6"
                                >
                            <input type="text" readonly
                                    id="customer_name"
                                    name="customer_name"
                                    value="{{ Request::get('customer_name') }}"
                                    class="middle-name"
                                    >
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchCustomerModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>

                    <div class="flex-row">
                        <label for="supplier_code" class="label_for">仕入先</label>
                        <div class="search-group">
                            <input type="text" id="supplier_code" name="supplier_code"
                                value="{{ Request::get('supplier_code') }}"
                                class="acceptNumericOnly w-100px fetchQueryName"
                                data-model="Customer"
                                data-query="customer_code"
                                data-query-get="supplier_name_abbreviation"
                                data-reference="supplier_name"
                                maxlength="6"
                                >
                            <input type="text" readonly
                                    id="supplier_name"
                                    name="supplier_name"
                                    value="{{ Request::get('supplier_name') }}"
                                    class="middle-name"
                                    >
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchSupplierModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="flex-row p-0 mr-4">
                        <label for="product_category" class="label_for">製品区分</label> 
                        <select name="product_category" id="product_category"
                            class="classic w-100px">
                            <option value="" {{ Request::get('product_category') == '' ? 'selected' : '' }}>すべて</option>
                            <option value="0" {{ Request::get('product_category') == '0' ? 'selected' : '' }}>材料</option>
                            <option value="1" {{ Request::get('product_category') == '1' ? 'selected' : '' }}>製品</option>
                            <option value="2" {{ Request::get('product_category') == '2' ? 'selected' : '' }}>試作品</option>
                            <option value="3" {{ Request::get('product_category') == '3' ? 'selected' : '' }}>購入材</option>
                            <option value="4" {{ Request::get('product_category') == '4' ? 'selected' : '' }}>仕掛品</option>
                        </select>
                    </div>

                    <div class="flex-row">
                        <label for="delete_flag" class="label_for">有効/無効</label>
                        <select name="delete_flag" id="delete_flag"
                            class="classic w-100px"
                            >
                            <option value="0" {{ Request::get('delete_flag') == 0 ? 'selected' : '' }}>有効</option>
                            <option value="1" {{ Request::get('delete_flag') == 1 ? 'selected' : '' }}>無効</option>
                        </select>
                    </div>
                </div>

                <div class="text-center sc relative button-div">
                    <button type="reset" class="btn btn-primary btn-wide">検索条件をクリア</button>
                    <button type="submit" class="btn btn-primary btn-wide">検索</button>
                    <a 
                        href="{{ route('master.part.excel_export', request()->query()) }}" 
                        id="export-excel"
                        class="btn btn-success absolute-right {{ $part_number_records->total() == 0 ? 'btn-disabled' : '' }}"
                        {{ count($part_number_records) == 0 ? 'onclick="return false;" style=pointer-events:none; opacity:0.5; cursor:not-allowed;"' : '' }}
                        {{ $part_number_records->total() == 0 ? 'disabled' : '' }}
                    >
                        検索結果をEXCEL出力
                    </a>
                </div>
            </div>
        </form>

        <div class="pagettlWrap">
            <h1><span>検索結果</span></h1>
        </div>
        <div class="tableWrap bordertable" style="clear: both;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    @if($part_number_records && $part_number_records->total() > 0)
                        {{ $part_number_records->total() }}件中、{{ $part_number_records->firstItem() }}件～{{ $part_number_records->lastItem() }} 件を表示しています
                    @endif
                </div>
                <a href="{{ route('master.part.create') }}" class="btn btn-primary">新規登録</a>
            </div>
            <table class="tableBasic">
                <thead>
                    <tr>
                        <th class="text-left">品番</th>
                        <th>品名</th>
                        <th>ライン</th>
                        <th>部門</th>
                        <th>得意先</th>
                        <th>仕入先</th>
                        <th>製品区分</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($part_number_records as $record)
                        <tr>
                            <td>{{ $record->part_number }}</td>
                            <td class="text-center">{{ $record?->product_name }}</td>
                            <td class="text-center">{{ $record?->line?->line_name  }}</td>
                            <td class="text-center">{{ $record?->department?->department_name }}</td>
                            <td class="text-center">{{ $record?->customer?->supplier_name_abbreviation }}</td>
                            <td class="text-center">{{ $record?->supplier?->supplier_name_abbreviation }}</td>
                            <td class="text-center">{{ $record?->product_category }}</td>
                            <td class="text-center">
                                <a href="{{ route('master.part.edit', $record->id) }}" class="btn btn-primary">編集</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">検索結果はありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $part_number_records->appends(request()->all())->links() }}
        </div>
    </div>
</div>

@include('partials.modals.masters._search', [
    'modalId' => 'searchLineModal',
    'searchLabel' => 'ラインコード',
    'resultValueElementId' => 'line_code',
    'resultNameElementId' => 'line_name',
    'model' => 'Line'
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchDepartmentModal',
    'searchLabel' => '部門',
    'resultValueElementId' => 'department_code',
    'resultNameElementId' => 'department_name',
    'model' => 'Department_name'
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchCustomerModal',
    'searchLabel' => '取引先',
    'resultValueElementId' => 'customer_code',
    'resultNameElementId' => 'customer_name',
    'model' => 'Customer'
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '仕入先',
    'resultValueElementId' => 'supplier_code',
    'resultNameElementId' => 'supplier_name',
    'model' => 'Supplier'
])

@endsection

@push('scripts')
    @vite(['resources/js/master/part/part.js'])
@endpush