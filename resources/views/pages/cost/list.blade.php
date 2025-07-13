@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '原価表')
@section('content')
<style>
    .checkmark-index {
        position: absolute;
        top: 3px;
        left: 0;
        height: 15px;
        width: 15px;
        background-color: white;
        border-radius: 50%;
        border: 1px solid gray;
    }

    /* On mouse-over, add a grey background color */
    .container-radio:hover input ~ .checkmark-index {
        background-color: #ccc;
    }

    /* When the radio button is checked, add a blue background */
    .container-radio input:checked ~ .checkmark-index {
        background-color: white;
        border: 1px solid gray
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark-index:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .container-radio input:checked ~ .checkmark-index:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .container-radio .checkmark-index:after {
        top: 3px;
        left: 3px;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: gray;
    }

    .required {
        border: 1px transparent;
        padding: 4px 15px;
        background-color: #ed7d32;
        color: white;
    }
</style>
<div class="content">
    <div class="contentInner">
        <div class="accordion">
            <h1><span>原価表</span></h1>
        </div>

        <div class="pagettlWrap">
            <h1><span>検索</span></h1>
        </div>

        <form action="{{ route('cost.index')  }}" method="GET" accept-charset="utf-8" id="createReqFrm" class="overlayedSubmitForm">
            <div class="tableWrap borderLesstable inputFormArea">
                <div class="row-content">
                    <div class="flex-row">
                        <label for="year_month" class="label_for" style="padding-bottom: 5px;">
                            年月
                            <span class="required">必須</span>
                        </label>

                        <input type="text" style="width: 150px;" id="year_month" name="year_month" placeholder="YYYYMM" value="{{ Request::get('year_month') }}">
                        <div class="error_msg"></div>
                    </div>
                    <div class="flex-row">
                        <label for="breakdown" class="label_for">内訳</label>
                        <div class="input-row flex-radio" style="gap: 15px;">
                            <label class="mr-1">
                                <input type="radio" name="breakdown" value="課別" id="section" {{ Request::get('breakdown') == '' || Request::get('breakdown') == '課別' ? 'checked' : '' }}> 課別
                            </label>
                            <label class="mr-1">
                                <input type="radio" name="breakdown" value="組別" id="group" {{ Request::get('breakdown') == '組別' ? 'checked' : '' }}> 組別
                            </label>
                            <label class="mr-1">
                                <input type="radio" name="breakdown" value="ライン別" id="line" {{ Request::get('breakdown') == 'ライン別' ? 'checked' : '' }}> ライン別
                            </label>
                            <label class="mr-1">
                                <input type="radio" name="breakdown" value="得意先別" id="customer" {{ Request::get('breakdown') == '得意先別' ? 'checked' : '' }}> 得意先別
                            </label>
                        </div>
                    </div>
                    <div class="flex-row">
                        <label for="department_code" class="label_for" style="padding-bottom: 5px;">部門</label>
                        <div class="d-flex">
                            <p class="formPack fixedWidth fpfw25p">
                                <input type="text" id="department_code" name="department_code" value="{{ Request::get('department_code') }}" class="mr-25" style="width: 100px">
                            </p>
                            <p class="formPack fixedWidth fpfw25p">
                                <input type="text" readonly
                                id="department_name"
                                name="department_name"
                                value="{{ Request::get('department_name') }}"
                                class="middle-name mr-25"
                                style="width: 170px">
                            </p>
                            <p class="formPack fixedWidth fpfw25p">
                                <button type="button" class="btnSubmitCustom btnSubmitCustom--size js-modal-open"
                                                data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-center sc relative">
                    <a href="#" id="export_csv" class="btn btn-success btn-wide float-right">検索結果をEXCEL出力</a>
                    @php
                        $currentYear = now()->year;
                        $currentMonth = now()->format('m');
                    @endphp
                    <a href="{{ route("cost.listSearch") }}?year_month={{ $currentYear.$currentMonth }}&breakdown=課別"
                        class="btn btn-primary btn-wide" id="search">検索条件をクリア</a>

                    <button type="submit" class="btn btn-primary btn-wide">検索</button>
                    <div>
                        {{-- <a href="{{ route("estimate.index") }}"
                        class="buttonBasic btn-reset bColor-ok js-btn-reset-reload" style="width: 250px!important; background-color: green;">検索結果をEXCEL出力 --}}
                    </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="pagettlWrap">
            <h1><span>検索結果</span></h1>
        </div>
        <div class="tableWrap bordertable" style="clear: both;">
            <ul class="headerList">
                @if (count($lists) > 0)
                    <li>{{ $lists->count() }}件中、{{ $lists->firstItem() }}件～{{ $lists->lastItem() }} 件を表示してます</li>
                @else
                    <li></li>
                @endif
                <li></li>
            </ul>
            <table class="tableBasic list-table">
                <thead>
                    @if (Request::get('breakdown') == '課別' || Request::get('breakdown') == '組別')
                        <tr>
                            <th>部門コード</th>
                            <th>部門名</th>
                            <th>購入材料費</th>
                            <th>設備材料費</th>
                            <th>外注費</th>
                            <th>設備外注費</th>
                            <th>外注設計費</th>
                            <th>外注工事費</th>
                            <th>刃具費（その他）</th>
                            <th>接待交際費</th>
                            <th>修繕費</th>
                            <th>消耗品費</th>
                            <th>事務用品費</th>
                            <th>会議費</th>
                            <th>雑費</th>
                            <th>合計</th>
                        </tr>
                    @elseif(Request::get('breakdown') == 'ライン別')
                        <tr>
                            <th>部門コード</th>
                            <th>部門名</th>
                            <th>ラインコード</th>
                            <th>ライン名</th>
                            <th>購入材料費</th>
                            <th>設備材料費</th>
                            <th>外注費</th>
                            <th>設備外注費</th>
                            <th>外注設計費</th>
                            <th>外注工事費</th>
                            <th>刃具費（その他）</th>
                            <th>接待交際費</th>
                            <th>修繕費</th>
                            <th>消耗品費</th>
                            <th>事務用品費</th>
                            <th>会議費</th>
                            <th>雑費</th>
                            <th>合計</th>
                        </tr>
                    @elseif(Request::get('breakdown') == '得意先別')
                    <tr>
                        <th>得意先コード</th>
                        <th>得意先名</th>
                        <th>購入材料費</th>
                        <th>設備材料費</th>
                        <th>外注費</th>
                        <th>設備外注費</th>
                        <th>外注設計費</th>
                        <th>外注工事費</th>
                        <th>刃具費（その他）</th>
                        <th>接待交際費</th>
                        <th>修繕費</th>
                        <th>消耗品費</th>
                        <th>事務用品費</th>
                        <th>会議費</th>
                        <th>雑費</th>
                        <th>合計</th>
                    </tr>
                    @endif
                </thead>
                <tbody>
                    @php
                        $breakdown = Request::get('breakdown');
                        $yearMonth = Request::get('year_month');
                    @endphp
                
                    @if ($lists->isEmpty())
                        @include('partials._no_record', ['colspan' => ($breakdown === 'ライン別' ? 18 : 16)])
                    @else
                        @foreach ($lists as $item)
                            <tr>
                                {{-- Display columns based on breakdown type --}}
                                @if ($breakdown === 'ライン別')
                                    <td>{{ $item->department_code ?? 'その他' }}</td>
                                    <td>{{ $item->department?->name }}</td>
                                    <td>{{ $item->line_code }}</td>
                                    <td>{{ $item->line?->line_name }}</td>
                                @elseif ($breakdown === '得意先別')
                                    <td>{{ $item->customer_code ?? 'その他'}}</td>
                                    <td>{{ $item->customer?->customer_name }}</td>
                                @else
                                    <td>{{ $item->grouped_department_code ?? $item->department_code ?? 'その他' }}</td>
                                    <td>{{ $item->department?->name }}</td>
                                @endif
                
                                {{-- Initialize a variable to hold the total for the current row --}}
                                @php
                                    $rowTotal = 0; // Initialize row total
                                @endphp
                
                                {{-- Iterate through all expense fields and display data --}}
                                @foreach ([
                                    ['011', '購入材料費', 'purchased_material_cost'],
                                    ['012', '設備材料費', 'equipment_processing_cost'],
                                    ['021', '外注費', 'outsourcing_cost'],
                                    ['022', '設備外注費', 'equipment_outsourcing_cost'],
                                    ['023', '外注設計費', 'outsourced_design_cost'],
                                    ['024', '外注工事費', 'outsourcing_construction_cost'],
                                    ['031', '刃具費（その他）', 'cutting_tools_cost'],
                                    ['331', '接待交際費', 'entertainment_fee'],
                                    ['341', '修繕費', 'repair_cost'],
                                    ['361', '消耗品費', 'supplies_expense'],
                                    ['371', '事務用品費', 'office_supplies'],
                                    ['381', '会議費', 'conference_cost'],
                                    ['391', '雑費', 'misc_expenses'],
                                ] as $expense)
                                    @php
                                        [$expenseItem, $itemName, $costField] = $expense;
                                        $costValue = $item->$costField; // Get the cost value
                                        $rowTotal += $costValue; // Sum up the cost value for this row
                                    @endphp
                                    <td style="text-align: right;">
                                        <a href="{{ route('cost.purchaseData', [
                                            'year_month' => $yearMonth,
                                            'expense_item' => $expenseItem,
                                            'item_name' => $itemName,
                                            'department_code' => $item->department_code,
                                            'department_name' => $item->department,
                                            'line_code' => $item->line_code ?? null,
                                            'line_name' => $item->line ?? null,
                                            'customer_code' => $item->customer_code ?? null,
                                            'customer_name' => $item->customer ?? null
                                        ]) }}">
                                            {{ number_format($costValue) }}
                                        </a>
                                    </td>
                                @endforeach
                
                                {{-- Display the total cost for this row --}}
                                <td style="text-align: right;">{{ number_format($rowTotal) }}</td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        {{-- Display the footer totals based on the breakdown type --}}
                        @if ($breakdown === 'ライン別')
                            <td colspan="3" style="border-bottom: none;"></td>
                            <td style="background-color: #d9e1f2; text-align: center;">合計</td>
                        @else
                            <td colspan="1" style="border-bottom: none;"></td>
                            <td style="background-color: #d9e1f2; text-align: center;">合計</td>
                        @endif
                
                        @php
                            $rowTotal = 0; // Initialize row total
                        @endphp
                        
                        {{-- Iterate through the totals array and display each value, summing up the row total --}}
                        @foreach ([
                            'totalPurchasedMaterialCost',
                            'totalEquipmentProcessingCost',
                            'totalOutsourcingCost',
                            'totalEquipmentOutsourcingCost',
                            'totalOutsourcedDesignCost',
                            'totalOutsourcingConstructionCost',
                            'totalCuttingToolsCost',
                            'totalEntertainmentFee',
                            'totalRepairCost',
                            'totalSuppliesExpense',
                            'totalOfficeSupplies',
                            'totalConferenceCost',
                            'totalMiscExpenses',
                        ] as $totalField)
                            @php
                                // Add the current total value to the row total
                                $rowTotal += $totals[$totalField] ?? 0;
                            @endphp
                            <td style="text-align: right;">
                                {{-- Display the formatted value of each expense item --}}
                                {{ number_format($totals[$totalField] ?? 0) }}
                            </td>
                        @endforeach
                        
                        {{-- Display the total cost across all categories --}}
                        <td style="text-align: right; font-weight: bold;">
                            {{ number_format($rowTotal) }}
                        </td>
                    
                    </tr>
                </tfoot>                        
            </table>
            @if ($lists->count() > 0)
                {{ $lists->links('vendor.pagination.simple-default') }}
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
@endsection
@push('scripts')
    @vite(['resources/js/cost/list/data-form.js'])
@endpush