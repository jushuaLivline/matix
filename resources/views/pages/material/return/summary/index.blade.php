@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css') 
    @vite('resources/css/materials/supplied_item_return_list.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>返品実績集計</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('material.return.summary.index') }}" class="overlayedSubmitForm with-js-validation" accept-charset="utf-8" data-disregard-empty="true" id="returnRecordSummaryForm">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic w-100">
                        <tbody>
                        <tr class="d-block">
                            <!-- 集計単位 -->
                            <td class="w-25 pb-0">
                                <dl class="formsetBox mb-4">
                                    <dt class="requiredForm">集計単位</dt>
                                    <dd>
                                        <p class="formPack">
                                            <label class="radioBasic">
                                                <input type="radio" name="category" value="division" @if(Request::get('category') == 'division') checked @else checked @endif>
                                                <span>課</span>
                                            </label>
                                        </p>
                                        <p class="formPack">
                                            <label class="radioBasic">
                                                <input type="radio" name="category" value="department" @if(Request::get('category') == 'department') checked @endif>
                                                <span>組</span>
                                            </label>
                                        </p>
                                        <p class="formPack">
                                            <label class="radioBasic">
                                                <input type="radio" name="category" value="line" @if(Request::get('category') == 'line') checked @endif>
                                                <span>ライン</span>
                                            </label>
                                        </p>
                                        <p class="formPack">
                                            <label class="radioBasic">
                                                <input type="radio" name="category" value="product" @if(Request::get('category') == 'product') checked @endif>
                                                <span>品番</span>
                                            </label>
                                        </p>
                                    </dd>
                                </dl>
                                <dl class="formsetBox mb-3">
                                    <dt class="requiredForm">返却日</dt>
                                    <dd>
                                        <div class="d-flex">
                                            @php
                                                $stat_date = (Request::get('return_date_start')) ? Request::get('return_date_start', date('Ymd', strtotime('first day of this month'))) : '';
                                                $end_date = (Request::get('return_date_end')) ? Request::get('return_date_end', date('Ymd', strtotime('last day of this month'))) : '';

                                            @endphp
                                            @include('partials._date_picker', ['inputName' => 'return_date_start', 'value' => $stat_date])
                                            <span style="font-size:18px; padding:5px 10px;">
                                                ~
                                            </span>
                                            @include('partials._date_picker', ['inputName' => 'return_date_end', 'value' => $end_date])
                                        </div>
                                    </dd>
                                </dl>

                                <dl class="formsetBox mb-3">
                                    <dt>部門</dt>
                                    <div class="d-flex">
                                        <input type="text" name="department_code"
                                            id="department_code" style="margin-right: 10px; width: 100px; ime-mode: disabled"
                                            data-validate-exist-model="Department"
                                            data-validate-exist-column="code"
                                            data-inputautosearch-model="Department"
                                            data-inputautosearch-column="code"
                                            data-inputautosearch-return="name"
                                            data-inputautosearch-reference="department_name"
                                            class="text-left acceptNumericOnly"
                                            minlength="6"
                                            maxlength="6"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            value="{{ request()->get('department_code') }}" 
                                            >
                                        <input type="text" readonly
                                            name="department_name"
                                            id="department_name" style="margin-right: 10px; width: 290px;"
                                            value="{{ request()->get('department_name')}}"
                                            class="middle-name text-left">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchDepartmentModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </dl>

                                <dl class="formsetBox">
                                    <dt>ライン</dt>
                                    <dd>
                                        <div class="d-flex">
                                            <input type="text" name="line_code"
                                                    data-validate-exist-model="Line"
                                                data-validate-exist-column="line_code"
                                                data-inputautosearch-model="line"
                                                data-inputautosearch-column="line_code"
                                                data-inputautosearch-return="line_name"
                                                data-inputautosearch-reference="line_name"
                                                id="line_code" style="margin-right: 10px; width: 100px"
                                                class="text-left acceptNumericOnly"
                                                minlength="3"
                                                maxlength="3"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                value="{{ request()->get('line_code') }}" >
                                            <input type="text" readonly
                                                name="line_name"
                                                id="line_name" style="margin-right: 10px; width: 290px;"
                                                value="{{ request()->get('line_name') }}"
                                                class="middle-name text-left">
                                            <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchLineModal">
                                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                            </button>
                                        </div>
                                        <div data-error-container="line_code"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 返却日 -->
                            <td class="w-35 pb-0">
                                
                            </td>
                            <!-- 部門 -->
                            <td class="pb-0">
                                
                            </td>
                        </tr>
                        <tr class="d-block">
                            <!-- ライン -->
                            <td class="d-block pb-0">
                                
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <a href="{{ route('material.return.summary.excel_eport', Request::all()) }}" class="float-right btn btn-green {{ $results->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                    <ul class="buttonlistWrap">
                        <li>
                        <button type="button" class="btn btn-blue" type="submit" style="min-width: 300px"
                        data-clear-inputs
                        data-clear-form-target="#returnRecordSummaryForm">
                                検索条件をクリア
                            </button>
                        </li>
                        <li>
                            <button class="btn btn-blue" type="submit" style="min-width: 300px">
                                検索
                            </button>
                        </li>
                    </ul>
                    {{-- <div class="btnListContainer">
                        <div class="btnContainerMain">
                            <div class="btnContainerMainLeft">
                                <a href="{{ route('material.return.summary.index') }}"
                                       class="btn-reset buttonBasic bColor-ok js-btn-reset">集計条件をクリア</a>
                                <input type="submit" value="集計"
                                       class="buttonBasic bColor-ok">
                            </div>
                            <div class="btnContainerMainRight">
                                <a href="{{ route('materials.supplied.item.return.export', Request::all()) }}" class="btnExport">
                                    集計結果をEXCEL出力
                                </a>
                            </div>
                        </div>
                    </div> --}}
                </div>
            </form>

            <div class="section">
                <h1 class="form-label bar indented">集計結果</h1>
                <div class="box">
                    @if(isset($results))
                        @if(Request::get('category') == 'division' || Request::get('category') == 'department')
                            
                            <!-- 「集計結果」を「課」 -->
                            <div class="resultByDepartment">
                          
                                @if(isset($results) && $results->count() > 0)
                                    {{ $results->total() }}件中、{{ $results->firstItem() }}件～{{ $results->lastItem() }} 件を表示しています
                                @endif
                                <table class="tableBasic list-table has-border" style="width: 900px">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">部門CD</th>
                                        <th rowspan="2" class="text-center align-middle">部門名</th>
                                        <th colspan="4" class="text-center">集計</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">数量</th>
                                        <!-- <th class="text-right">材料費</th> -->
                                        <th class="text-center">加工費</th>
                                        <!-- <th class="text-right">金額</th> -->
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($results as $result)
                                        @php
                                            $processingRate = (Request::get('category') == 'department') 
                                                    ? $result['max_processing_rate'] 
                                                    : $result['max_processing_rate'];
                                        @endphp
                                            <tr>
                                                <td>{{ $result['department_code'] }}</td>
                                                <td>{{ $result['department_name'] }}</td>
                                                <td class="text-right" >{{ number_format($result['max_arrival_quantity']) }}</td>
                                                <!-- <td class="text-right" >{{ number_format($result['product_price']) }}</td> -->
                                                <td class="text-right" >{{ number_format($result['max_processing_rate']) }}</td>
                                                <!-- <td class="text-right" >{{ number_format($result['total_amount']) }}</td> -->
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">検索結果はありません</td>
                                            </tr>
                                        @endforelse
                                        <tr class="total-row">
                                            <td class="remove-cell"></td>
                                            <td class="text-center">合計</td>
                                            <td class="text-right">{{ number_format($totalArrivalQuantity) }}</td>
                                            <!-- <td class="text-right">{{ number_format($totalProductPrice) }}</td> -->
                                            <td class="text-right">{{ number_format($totalProcessingRate) }}</td>
                                            <!-- <td class="text-right">{{ number_format($grandTotal) }}</td> -->
                                        </tr>
                                    
                                    </tbody>
                                </table>
                            </div>
                        @elseif(Request::get('category') == 'line')
                            <!-- 「集計結果」を「ライン」 -->
                            <div class="resultByLine">
                                @if(isset($results) && $results->count() > 0)
                                    {{ $results->total() }}件中、{{ $results->firstItem() }}件～{{ $results->lastItem() }} 件を表示しています
                                @endif
                                <table class="tableBasic list-table has-border" style="width: 1200px">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">部門CD</th>
                                        <th rowspan="2" class="text-center align-middle">部門名</th>
                                        <th rowspan="2" class="text-center align-middle">ラインCD</th>
                                        <th rowspan="2" class="text-center align-middle">ライン名</th>
                                        <th colspan="4" class="text-center">集計</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">数量</th>
                                        <!-- <th class="text-right">材料費</th> -->
                                        <th class="text-center">加工費</th>
                                        <!-- <th class="text-right">金額</th> -->
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($results as $result)
                                            <tr>
                                                <td>{{ $result->department_code }}</td>
                                                <td>{{ $result->department_name }}</td>
                                                <td>{{ $result->line_code }}</td>
                                                <td>{{ $result->line_name }}</td>
                                                <td class="text-right" >{{ number_format($result->max_arrival_quantity) }}</td>
                                                <!-- <td class="text-right" >{{ number_format($result->product_price) }}</td> -->
                                                <td class="text-right" >{{ number_format($result['max_processing_rate']) }}</td>
                                                <!-- <td class="text-right" >{{ number_format($result->total_amount) }}</td> -->
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center">検索結果はありません</td>
                                            </tr>
                                        @endforelse
                                        <tr class="total-row">
                                            <td class="remove-cell" colspan="3"></td>
                                            <td class="text-center">合計</td>
                                            <td class="text-right">{{ number_format($totalArrivalQuantity) }}</td>
                                            <!-- <td class="text-right">{{ number_format($totalProductPrice) }}</td> -->
                                            <td class="text-right">{{ number_format($totalProcessingRate) }}</td>
                                            <!-- <td class="text-right">{{ number_format($grandTotal) }}</td> -->
                                        </tr>
                                    
                                    </tbody>
                                </table>
                            </div>
                        @elseif(Request::get('category') == 'product')
                            <!-- 「集計結果」を「品番」 -->
                            <div class="resultByPartNumber">
                                @if(isset($results) && $results->count() > 0)
                                    {{ $results->total() }}件中、{{ $results->firstItem() }}件～{{ $results->lastItem() }} 件を表示しています
                                @endif
                                <table class="tableBasic list-table has-border" style="width: 1500px;">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center align-middle">部門CD</th>
                                        <th rowspan="2" class="text-center align-middle">部門名</th>
                                        <th rowspan="2" class="text-center align-middle">ラインCD</th>
                                        <th rowspan="2" class="text-center align-middle">ライン名</th>
                                        <th rowspan="2" class="text-center align-middle">材料品番</th>
                                        <th rowspan="2" class="text-center align-middle">品名</th>
                                        <th colspan="4" class="text-center">集計</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">数量</th>
                                        <!-- <th class="text-right">材料費</th> -->
                                        <th class="text-center">加工費</th>
                                        <!-- <th class="text-right">金額</th> -->
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($results as $result)
                                            <tr>
                                                <td>{{ $result->department_code }}</td>
                                                <td>{{ $result->department_name }}</td>
                                                <td>{{ $result->line_code }}</td>
                                                <td>{{ $result->line_name }}</td>
                                                <td>{{ $result->edited_part_number }}</td>
                                                <td>{{ $result->product_name }}</td>
                                                <td class="text-right" >{{ number_format($result->max_arrival_quantity) }}</td>
                                                <!-- <td class="text-right" >{{ number_format($result->product_price) }}</td> -->
                                                <td class="text-right" >{{ number_format($result['max_processing_rate']) }}</td>
                                                <!-- <td class="text-right" >{{ number_format($result->total_amount) }}</td> -->
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">検索結果はありません</td>
                                            </tr>
                                        @endforelse
                                        <tr class="total-row">
                                            <td class="remove-cell" colspan="5"></td>
                                            <td class="text-center">合計</td>
                                            <td class="text-right">{{ number_format($totalArrivalQuantity) }}</td>
                                            <!-- <td class="text-right">{{ number_format($totalProductPrice) }}</td> -->
                                            <td class="text-right">{{ number_format($totalProcessingRate) }}</td>
                                            <!-- <td class="text-right">{{ number_format($grandTotal) }}</td> -->
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        {{ $results->appends(request()->all())->links() }}
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
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
@endsection
