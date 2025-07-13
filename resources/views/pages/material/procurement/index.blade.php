








@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
@endpush
@php
    $yearMonth = request()->input('year_month');
    $dateInput = $yearMonth ? \Carbon\Carbon::createFromFormat('Ym', $yearMonth)->startOfMonth() : \Carbon\Carbon::now();
    $firstDateOfMonth = $dateInput->copy()->startOfMonth();
    $lastDateOfMonth = $dateInput->copy()->endOfMonth();
@endphp
@section('content')
    <div class="content suppliList">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>材料調達計画表一覧</span></h1>
            </div>
            @if(session('success'))
                <div class="tableWrap borderLesstable">
                    <div class="success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="tableWrap borderLesstable">
                    <div class="error">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form action="{{ route('material.procurement.index') }}" 
                class="overlayedSubmitForm with-js-validation" 
                data-disregard-empty="false"
                id="purchaseRecordForm">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic w-100">
                        <tbody>
                            <tr class="d-block">
                                <!-- 年月 -->
                                <td class="mr-half">
                                    <dl class="formsetBox ">
                                        <dt class="requiredForm">年月</dt>
                                        <dd style="flex-wrap: wrap; max-width: 175px;">
                                            <div class="d-flex mr-20c" >
                                                @php
                                                    $date = (Request::get('year_month')) ? Request::get('year_month') : now()->format('Ym');
                                                @endphp
                                                @include('partials._date_picker_year_month', [
                                                    'inputName' => 'year_month', 
                                                    'attributes' => 'data-error-messsage-container=#date_error_message', 
                                                    'dateFormat' => 'YYYYMM', 
                                                    'required' => false, 
                                                    'minlength'=>'6', 'maxlength'=>'6', 
                                                    'inputClass' => 'text-left datepicker-disabled-dates w-100c', 
                                                    'disableDates' => true,
                                                    'value' => $date, 
                                                    'requried' => true])

                                            </div>
                                            <div id="date_error_message" style="width: 100%;"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <!-- 材料メーカー -->
                                <td style="margin-right: 20px !important;">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">材料メーカー</dt>
                                        <dd class="mr-20c" style="flex-wrap: wrap;">
                                            <div>
                                                <div class="d-flex">
                                                    <div class="formPack mr-10c">
                                                        <input type="text" name="process_code"
                                                            data-field-name="材料メーカー"
                                                            data-error-messsage-containers="#process_code_error_message"
                                                            data-validate-exist-model="Process" 
                                                            data-validate-exist-column="process_code"
                                                            data-inputautosearch-model="Process" 
                                                            data-inputautosearch-column="process_code"
                                                            data-inputautosearch-return="abbreviation_process_name" 
                                                            data-inputautosearch-reference="process_name"
                                                            data-custom-required-error-message="材料メーカーを入力してください"
                                                            maxlength="4"
                                                            id="process_code" class="searchOnInput Process w-100c"
                                                            value="{{ request()->get('process_code') }}"
                                                            required>
                                                    </div>
                                                    <div class="formPack fixedWidth box-middle-name mr-10c  w-200c">
                                                        <input type="text" readonly
                                                            name="process_name"
                                                            id="process_name"
                                                            value="{{ request()->get('process_name') }}"
                                                            class="middle-name text-left">
                                                    </div>
                                                    <div class="formPack fixedWidth w-20c">
                                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                                data-target="searchProcessModal"
                                                                data-query-field="inside_and_outside_division=2">
                                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                        </button>
                                                    </div>
                                                </div>
                                                <div data-error-container="process_code" id="process_code_error_message"></div>
                                            </div>
                                        </dd>
                                    </dl>
                                </td>
                                <!-- グループ -->
                                <td style="max-width: 80px;">
                                    <dl class="formsetBox">
                                        <dt>グループ</dt>
                                        <dd>
                                            <p class="formPack">
                                                <input type="text" name="supplied_group"
                                                       class="text-left w-100c"
                                                       value="{{ request()->get('supplied_group') }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                                <!-- 対象 -->
                                <td>
                                    <dl class="formsetBox">
                                        <dt>対象</dt>
                                        <dd>
                                            <p class="formPack">
                                                <label class="radioBasic">
                                                    <input type="radio" name="part_classification" value="0" {{ request()->get('part_classification') == 0 ? 'checked' : '' }}>
                                                    <span>すべて</span>
                                                </label>
                                            </p>
                                            <p class="formPack">
                                                <label class="radioBasic">
                                                    <input type="radio" name="part_classification" value="1" {{ request()->get('part_classification') == 1 ? 'checked' : '' }}>
                                                    <span>かんばん品</span>
                                                </label>
                                            </p>
                                            <p class="formPack">
                                                <label class="radioBasic">
                                                    <input type="radio" name="part_classification" value="2" {{ request()->get('part_classification') == 2 ? 'checked' : '' }} 
                                                    @if (request()->get('part_classification') == '') checked @endif >
                                                    <span>指示品</span>
                                                </label>
                                            </p>
                                            
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="btnListContainer">
                        <div class="btnContainerMain">
                            <div class="btnContainerMainLeft">
                                <button type="button" 
                                    data-clear-inputs 
                                    data-clear-form-target="#purchaseRecordForm"
                                    class="buttonBasic bColor-ok btn btn-primary">検索条件をクリア</button>
                                <button type="submit" value=""
                                       class="buttonBasic bColor-ok btn btn-primary w-150c">検索</button>
                            </div>
                            <div class="btnContainerMainRight">
                                <button type="button" class="btn btn-green {{ count($results) == 0 ? 'btn-disabled' : '' }}" id="excel-export-button">検索結果をEXCEL出力</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
                <div class="tableWrap bordertable clear-both">
                    <div class="supplyPlanList">
                        @if($results && $results?->total() > 0 )
                            <div>{{ $results?->total() }}件中、{{ $results?->firstItem() }}件～{{ $results?->lastItem() }} 件を表示してます</div>
                        @endif

                        <table class="tableBasic list-table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="min-width: 120px">材料品番</th>
                                    <th class="text-center" style="width: 100px">グループ</th>
                                    <th class="text-center" style="width: 70px">計画</th>
                                    <th class="text-center" style="width: 70px">計画数</th>
                                    <th class="text-center"  style="width: 70px">区分</th>
                                    @php
                                        $dates = [];
                                        for ($date = $firstDateOfMonth; $date <= $lastDateOfMonth; $date->modify('+1 day')) {
                                            $isWeekend = in_array($date->format('w'), [0, 6]);
                                            $dates[] = [
                                                'date' => $date->format('Y-m-d'),
                                                'day' => (int)$date->format('d'),
                                                'isWeekend' => $isWeekend
                                            ];
                                        }
                                    @endphp
                                    @foreach($dates as $date)
                                        <th class="text-center" style="width: 2%; color: {{ $date['isWeekend'] ? 'red' : 'black' }}">
                                            {{ $date['day'] }}
                                        </th>
                                    @endforeach
                                    <th class="text-center" style="width: 100px">翌月内示</th>
                                    <th class="text-center" style="width: 130px">翌々月内示</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($results as $key => $result)
                                    @php
                                        $orders = $result->supplyMaterialOrders->keyBy(fn($order) => $order->instruction_date->format('Y-m-d'));
                                        $arrivals = $result->supplyMaterialArrivalManufacturers->keyBy(fn($arrival) => $arrival->arrival_day->format('Y-m-d'));
                                        $current_month = $result->current_month;
                                        $instruction_no = $result?->supplyMaterialOrders?->sum('instruction_number');
                                        
                                        if($result->current_month == 0){
                                            $current_month = ($result->kanban_status == 'shiji') ? 0 : '';
                                        }
                                        if($instruction_no == 0){
                                            $instruction_no = ($result->kanban_status == 'shiji') ? 0 : '';
                                        }
                                    @endphp

                                    {{-- Plan Row --}}
                                    <tr data-kanban-status="{{ $result->kanban_status }} " data-part-number=" {{ $result->part_number }}">
                                        <td class="text-left" rowspan="2">
                                            @if($result->kanban_status == 'shiji')
                                                <a href="{{ route('material.procurement.create', request()->query() + ['part_number' => $result->edited_part_number]) }}">
                                                    {{ $result->edited_part_number }}
                                                </a>
                                            @else
                                                {{ $result->edited_part_number }}
                                            @endif

                                            @if ($result->edited_part_number)
                                            <br />{{ $result->product_name }}
                                            <!-- <br />{{ $result->process_code }} -->
                                            @endif
                                        </td>
                                        <td class="text-center" rowspan="2">
                                            <a href="" class="js-modal-open" data-target="modal-Group-{{ $result->id }}">
                                                {{ $result->group?->supply_material_group ?? '__' }}
                                            </a>
                                        </td>
                                        <td class="text-right" rowspan="2">
                                            {{ $current_month }}
                                        </td>
                                        <td class="text-right" rowspan="2">
                                            {{$instruction_no}}
                                        </td>
                                        <td>計画</td>
                                        @php $counter = 0 @endphp
                                        @foreach($dates as $date)
                                            <td class="text-right">
                                                @if($result->kanban_status == 'shiji')
                                                    @php $counter++; @endphp
                                                    {{ $result['day_'.$counter] }}
                                                    {{--  
                                                    {{ $orders[$date['date']]->instruction_number ?? '' }}
                                                    --}}
                                                @endif
                                                
                                            </td>
                                        @endforeach
                                        <td class="text-right" rowspan="2">
                                            {{ $result->next_month ?? 0}}
                                            
                                        </td>
                                        <td class="text-right" rowspan="2">
                                            {{ $result->two_months_later ?? 0}}
                                        </td>
                                    </tr>

                                    {{-- Arrival Row --}}
                                    <tr>
                                        <td>入荷</td>
                                       
                                        @foreach($dates as $key => $date)
                                        
                                            <td class="text-right">
                                                @if($result->kanban_status == 'shiji')
                                                    {{ $arrivals[$date['date']]->arrival_quantity ?? '' }}
                                                @endif
                                        </td>
                                        @endforeach
                                    </tr>

                                    {{-- Modal Group --}}

                                <div class="delete-form">
                                    <form action="{{ route('material.settingGroup.destroy',  $result?->group?->id ?? "") }}" 
                                                method="POST" 
                                                id="manufacturerSettingForm-{{ $result->id }}"
                                                class="with-js-validation"
                                                accept-charset="utf-8"
                                                data-confirmation-message=" このグループを削除してもよろしいでしょうか？">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                </div>

                                @include('pages.material.setting.group.modal', [
                                    'result' => $result,
                                ])
                                    
                                @empty
                                    <tr>
                                        <td colspan="{{ 7 + count($dates) }}" class="text-center">検索結果はありません</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($results && $results?->total() > 0)
                        {{ $results->appends(request()->all())->links() }}
                    @endif
                </div>
            <form action="{{route('material.pdf.export')}}" accept-charset="utf-8" 
                class="overlayedSubmitForm with-js-validation" 
                data-disregard-empty="true" id="suppliedListForm"
                data-confirmation-message="「発注書を発行します、よろしいでしょうか？」"
                data-disabled-overlay="true">

                <input type="hidden" name="part_classification" value="{{ Request::get('part_classification') ?? '0' }}">
                @if( Request::get('year_month'))
                    <input type="hidden" name="year_month" value="{{ Request::get('year_month') ??  now()->format('Ym') }}">
                @endif
                @if( Request::get('process_code'))
                    <input type="hidden" name="process_code" value="{{ Request::get('process_code') ??  '' }}">
                @endif
                @if( Request::get('supplied_group'))
                    <input type="hidden" name="supplied_group" value="{{ Request::get('supplied_group') ??  '' }}">
                @endif

                <div class="float-right mt-3">
                    <button type="button"
                            data-target="manufacturer_modal_info"
                            class="btn btn-primary js-modal-open" style="width: 200px;">
                            メーカー情報設定
                    </button>
                    <button type="submit"
                            class="btn btn-success" style="width: 200px;">
                            発注書発行
                    </button>
                </div>
            </form>
        </div>

      
    </div>
               
    @include('pages.material.setting.manufacturer.modal', [
        'manufacturerInfo' => $manufacturerInfo,
    ])
   
    <form action="{{route('material.excel.export')}}" id="suppliedListExcelForm" data-disregard-empty="true">
        @csrf
        <input type="hidden" name="part_classification" value="{{ Request::get('part_classification') ?? '0' }}">
        @if( Request::get('year_month'))
            <input type="hidden" name="year_month" value="{{ Request::get('year_month') ??  now()->format('Ym') }}">
        @endif
        @if( Request::get('process_code'))
            <input type="hidden" name="process_code" value="{{ Request::get('process_code') ??  '' }}">
        @endif
        @if( Request::get('supplied_group'))
            <input type="hidden" name="supplied_group" value="{{ Request::get('supplied_group') ??  '' }}">
        @endif
    </form>


    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '材料メーカー',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'process_name',
        'model' => 'Process',
        'queryByField' => 'inside_and_outside_division=2',
    ])
    @php
        $dataConfigs['Process'] = [
            'model' => 'Process',
            'reference' => 'process_name',
            'test' => 'test',
        ];
    @endphp

<x-search-on-input :dataConfigs="$dataConfigs" />
@endsection
@push('scripts')
@vite('resources/js/material/procurement/index.js')
@endpush