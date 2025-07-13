@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '日々生産管理表')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>日々生産管理表</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('daily.reference.list') }}" accept-charset="utf-8" class="overlayedSubmitForm">
                <div class="tableWrap borderLesstable inputFormArea">
                    <div class="row-content">
                        {{-- year_month --}}
                        <div class="flex-row">
                            <label for="year_month" class="label_for">年月</label>
                            <input type="text" class="row-input" id="year_month" name="year_month" value="{{ Request::get('year_month') ?? $yearMonth }}" placeholder="YYYYMM">
                        </div>
                    </div>
                    <div class="row-group-content">
                        {{-- line_code --}}
                        <div class="flex-row">
                            <label for="line_code" class="label_for">ライン</label>
                            <div class="search-group">
                                <input type="text" id="line_code" name="line_code" value="{{ Request::get('line_code') }}" class="" style="width: 80px">
                                <input type="text" readonly
                                        id="line_name"
                                        name="line_name"
                                        value="{{ Request::get('line_name') }}"
                                        {{-- class="middle-name" --}}
                                        style="width: 205px">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchLineModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>

                        {{-- part_number --}}
                        <div class="flex-row">
                            <label for="part_number" class="label_for">品番</label>
                            <div class="formPack" style="display: flex; align-items: center;">
                                <input type="text" id="part_number" name="part_number" value="{{ Request::get('part_number') }}" class="" style="max-width: 150px">
                                <input type="text" readonly
                                    id="product_name"
                                    name="product_name"
                                    value="{{ Request::get('product_name') }}"
                                    class="middle-name"
                                    style="max-width: 250px; margin-left: 10px;">
                                
                                <button type="button" class="btnSubmitCustom js-modal-open" style="margin-left: 10px;"
                                                data-target="searchPartNumberModal">
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
                    
                    <ul class="buttonlistWrap" style="width: 0;">
                        <li>
                            <div class="parent">
                                <div>
                                    <a href="{{ route("daily.reference.list") }}"
                                       class="buttonBasic btn-reset bColor-ok js-btn-reset-reload" style="width: 250px!important">検索条件をクリア</a>
                                </div>
                                <div>
                                    <input type="submit" value="検索"
                                        class="buttonBasic bColor-ok" style="width: 250px !important">
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
                    @if (count($daily_prod) > 0)
                        <li>{{ $daily_prod->total() }}件中、{{ $daily_prod->firstItem() }}件～{{ $daily_prod->lastItem() }} 件を表示してます</li>
                    @endif
                </ul>
                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th>年月</th>  <!-- year_month -->
                        <th>ラインコード</th> <!-- line_code -->
                        <th>ライン名</th> <!-- line name -->
                        <th>品番</th> <!-- product code -->
                        <th>品名</th> <!-- product name -->
                        <th>部門名</th> <!-- department name -->
                        <th style="width: 100px">操作</th> <!-- action -->
                    </tr>
                    @if (count($daily_prod) <= 0)
                    <tr>
                        <td colspan="8" style="text-align: center;">検索結果はありません</td>
                    </tr>
                    @else
                        @foreach ($daily_prod as $daily)
                            <tr>
                                <td class="tA-le">{{ $daily->year . $daily->month }}</td>
                                <td class="tA-le">{{ $daily->line_code }}</td>
                                <td class="tA-le">{{ $daily->line_name }}</td>
                                <td class="tA-le">{{ $daily->part_number }}</td>
                                <td class="tA-le">{{ $daily->product_name }}</td>
                                <td class="tA-le">{{ $daily->department_name }}</td>
                                <td class="tA-cn">
                                    <a href="{{ route('daily.reference', ['id' => $daily->id]) }}" class="buttonBasic bColor-ok" style="width: 100px">
                                        参照
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
                @if (count($daily_prod) > 0)
                    {{ $daily_prod->links() }}
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
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
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
    @vite(['resources/js/master/products/index.js'])
@endpush