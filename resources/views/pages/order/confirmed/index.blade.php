@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/order/style.css')
    <style>
        .table-responsive {
            overflow-x: auto;
            max-width: 100%;
        }

    </style>
@endpush

@section('title', '確定受注検索・一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                確定受注検索・一覧
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form id="searchForm"  class="overlayedSubmitForm with-js-validation" data-disregard-empty="true">
                    <div class="box mb-3">
                        <div class="mb-3">
                            @if ($errors->any())
                                <div class="text-red">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="d-flex">
                                <div class='mr-3'>
                                    <label class="form-label dotted indented">受注日</label> <span class="btn-orange badge">必須</span>
                                    <div class="d-flex mr-20c" >
                                    @php
                                        $date = Request::get('order_date') ?? now()->format('Ymd');
                                    @endphp
                                    @include('partials._date_picker', [
                                        'inputName' => 'order_date', 
                                        'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=受注日', 
                                        'inputClass' => 'text-left datepicker-disabled-dates w-100c', 
                                        'value' => $date, 
                                        'required' => true])

                                    </div>
                                    <div id="date_error_message" style="width: 100%;"></div>

                                </div>
                                <div class="mr-4">
                                    <label class="form-label dotted indented">便No.</label>
                                    <div>
                                        <input type="text" name="delivery_no" 
                                            class="acceptNumericOnly"
                                            value="{{ request()->input('delivery_no', '') }}" 
                                            style="width:100px">
                                    </div>
                                </div>
                                <div class="mr-4">
                                    <label class="form-label dotted indented">納入先</label>
                                    <div class="d-flex">
                                        @php
                                            $customer_code  =  request()->get('customer_code') ?? '';
                                            $customer_name  =  ($customer_code) ? request()->get('customer_name')  : '';
                                        @endphp
                                        <input type="text" id="customer_code" 
                                                    data-field-name="納入先"
                                                    data-error-messsage-container="#supplier_code_error"
                                                    data-validate-exist-model="customer"
                                                    data-validate-exist-column="customer_code"
                                                    data-inputautosearch-model="customer"
                                                    data-inputautosearch-column="customer_code"
                                                    data-inputautosearch-return="customer_name"
                                                    data-inputautosearch-reference="customer_name"
                                                    name="customer_code" style="width:100px; margin-right: 10px;" 
                                                    value="{{ $customer_code }}">
                                        <input type="text" id="customer_name" name="customer_name" readonly 
                                                value="{{ $customer_name  }}" style="margin-right: 10px;">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchCustomerModal"
                                                data-query-field="">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                    <div id="supplier_code_error"></div>
                                </div>
                                <div class="mr-4">
                                    <label class="form-label dotted indented">工場</label>
                                    <div>
                                        <input type="text" name="plant" value="{{ request()->input('plant', '') }}" style="width:100px">
                                    </div>
                                </div>
                                <div class="mr-4">
                                    <label class="form-label dotted indented">受入</label>
                                    <div>
                                        <input type="text" name="acceptance" value="{{ request()->input('acceptance', '') }}" style="width:100px">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex">
                                <div class="mr-4">
                                    <label class="form-label dotted indented">部門</label>
                                    <div class="d-flex">
                                        @php
                                            $department_code  =  request()->get('department_code') ?? '';
                                            $department_name  =  ($department_code) ? request()->get('department_name')  : '';
                                        @endphp
                                        <input type="text" name="department_code"
                                            id="department_code" style="ime-mode: disabled"
                                            data-field-name="部門"
                                            data-error-messsage-container="#department_code_error"
                                            data-validate-exist-model="Department"
                                            data-validate-exist-column="code"
                                            data-inputautosearch-model="Department"
                                            data-inputautosearch-column="code"
                                            data-inputautosearch-return="name"
                                            data-inputautosearch-reference="department_name"
                                            class="text-left acceptNumericOnly w-100c mr-10c"
                                            minlength="6"
                                            maxlength="6"
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                            value="{{ $department_code }}" 
                                            >
                                        <input type="text" readonly
                                            name="department_name"
                                            id="department_name" style="margin-right: 10px; width: 290px;"
                                            value="{{ $department_name }}"
                                            class="middle-name text-left">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchDepartmentModal"
                                                data-query-field="">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                    <div id="department_code_error"></div>
                                </div>
                                <div class="mr-4">
                                    <label class="form-label dotted indented">ライン</label>
                                    <div class="d-flex">
                                        @php
                                            $line_code  =  request()->get('line_code') ?? '';
                                            $line_name  =  ($line_code) ? request()->get('line_name')  : '';
                                        @endphp
                                        <input type="text" name="line_code"
                                                data-field-name="ライン"
                                                data-error-messsage-container="#line_code_error"
                                                data-validate-exist-model="Line"
                                                data-validate-exist-column="line_code"
                                                data-inputautosearch-model="line"
                                                data-inputautosearch-column="line_code"
                                                data-inputautosearch-return="line_name"
                                                data-inputautosearch-reference="line_name"
                                                id="line_code"
                                                class="text-left w-75c mr-10c"
                                                minlength="3"
                                                maxlength="3"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                value="{{ $line_code  }}" >
                                        <input type="text" readonly
                                                name="line_name"
                                                id="line_name"
                                                value="{{ $line_name  }}"
                                                class="middle-name text-left w-290c mr-10c">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchLineModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                        </div>
                                        <div id="line_code_error"></div>
                                </div>
                                <div class="mr-4">
                                    <label class="form-label dotted indented">外注先</label>
                                    <div class="d-flex">
                                        <input type="text" id="supplier_code" 
                                                    data-field-name="外注先"
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
                            </div>
                        </div>
                        <div class="mb-4 d-flex">
                            <div class="mr-4">
                                <label class="form-label dotted indented">対象</label> <span class="btn-orange badge">必須</span>
                                <div>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="category" class="" value="" {{ (request()->input('category', '') == '') ? 'checked' : '' }}> 
                                        <span>
                                            すべて
                                        </span>
                                    </label>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="category" class="" value="2" {{ (request()->input('category', '') == '2') ? 'checked' : '' }}> 
                                        <span>
                                            指示
                                        </span>
                                    </label>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="category" class="" value="1" {{ (request()->input('category', '') == '1') ? 'checked' : '' }}> 
                                        <span>
                                            かんばん
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="mr-4">
                                <label class="form-label dotted indented">表示情報</label> <span class="btn-orange badge">必須</span>
                                <div>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="display_info" class="" value="order_and_performance" {{ (request()->input('display_info', '') == 'order_and_performance') ? 'checked' : 'checked' }}> 
                                        <span>
                                            注文情報と実績情報
                                        </span>
                                    </label>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="display_info" class=""  value="order_only" {{ (request()->input('display_info', '') == 'order_only') ? 'checked' : '' }}> 
                                        <span>
                                            注文情報のみ
                                        </span>
                                    </label>
                                    <label class='radioBasic mr-2'>
                                        <input type="radio" name="display_info" class=""  value="performance_only" {{ (request()->input('display_info', '') == 'performance_only') ? 'checked' : '' }}> 
                                        <span>
                                            実績情報のみ
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            @php
                                $queryParams = request()->query();
                                $queryString = http_build_query($queryParams);

                                $exportUrl = route('order.confirmed.export') . '?' . $queryString;
                            @endphp
                            <a href="{{ $exportUrl }}" class="btn btn-success btn-wide absolute-right" style="margin-right: 20px;" id="export-to-excel">
                                検索結果をEXCEL出力
                            </a>
                            <button class="btn btn-primary btn-wide" id="clear-form">検索条件をクリア</button>
                            <button class="btn btn-primary btn-wide" type="submit">検索</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    @if(Request::get('display_info') == 'order_and_performance')
                        <div class="mb-3">
                            @if(count($result) > 0)
                            <span>
                                {{ $result->total() }}件中、{{ $result->firstItem() ?? 0 }}件～{{ $result->lastItem() ?? 0 }}件を表示してます
                            </span>
                           
                            <span class="float-right">
                                上段：注文情報　下段：実績情報
                            </span>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">受入</th>
                                            <th rowspan="2">工場</th>
                                            <th rowspan="2">品番・品名</th>
                                            <th rowspan="2">背番号</th>
                                            <th rowspan="2">収容数</th>
                                            <th colspan="2" class="text-red f-bold">遅れ</th>
                                            @foreach($deliveryNos as $deliveryNo)
                                                <th colspan="2">{{ $deliveryNo }}便</th>
                                            @endforeach
                                            <th rowspan="2">かんばん枚合計</th>
                                            <th rowspan="2">指示数計</th>
                                            <th rowspan="2">個数計</th>
                                            <th rowspan="2">計画日量</th>
                                            <th rowspan="2">実績日量</th>
                                            <th rowspan="2">納品率(%)</th>
                                        </tr>
                                        <tr>
                                            <th class="text-red f-bold">枚</th>
                                            <th class="text-red f-bold">個</th>
                                            @foreach($deliveryNos as $deliveryNo)
                                                <th>枚</th>
                                                <th>個</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($result as $partNumber => $partData)
                                            
                                            @foreach ($partData as $item)
                                                <tr>
                                                    <td rowspan="2" class="valign-center text-center">{{ $item['acceptance'] }}</td>
                                                    <td rowspan="2" class="valign-center text-center">{{ $item['plant'] }}</td>
                                                    <td>{{ $item['part_number'] }}</td>
                                                    <td rowspan="2" class="valign-center">{{ $item['uniform_number'] }}</td>
                                                    <td rowspan="2" class="valign-center text-right">{{ $item['number_of_accommodated'] }}</td>
                                                    <td rowspan="2" class="bg-red text-right">
                                                            @foreach($partData[0]['daily_reports'] as $daily)
                                                                @php
                                                                    $kanbanNumbersSum = 0;
                                                                    $instructionNumbersSum = 0;
                                                                    $shipmentKanbanSum = 0;
                                                                    $shipmentInstructionSum = 0;

                                                                    $kanbanNumbersSum += $daily['kanban_number'];
                                                                    $instructionNumbersSum += $daily['instruction_number'];
                                                                    $shipmentKanbanSum += $daily['shipment_kanban_number'];
                                                                    $shipmentInstructionSum += $daily['shipment_instruction_number'];
                                                                @endphp
                                                            @endforeach
                                                        @if(Request::get('category') == 1 || Request::get('category') == '')
                                                            {{ $kanbanNumbersSum - $shipmentKanbanSum }}
                                                        @endif
                                                    </td>
                                                    <td rowspan="2" class="bg-red text-right">
                                                        @if(Request::get('category') == 2 || Request::get('category') == '')
                                                            {{ $instructionNumbersSum - $shipmentInstructionSum }}
                                                        @endif
                                                    </td>
                                                    @foreach($deliveryNos as $deliveryNo)
                                                        @foreach($partData[0]['daily_reports'] as $deliveryNumber => $daily)
                                                            <td class="text-right">
                                                                @if(Request::get('category') == 1 || Request::get('category') == '')
                                                                    @if ($deliveryNumber == $deliveryNo)
                                                                        {{ $daily['kanban_number'] }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                @if(Request::get('category') == 2 || Request::get('category') == '')
                                                                    @if ($deliveryNumber == $deliveryNo)
                                                                        {{ $daily['instruction_number'] }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{ $kanbanNumbersSum ?? 0 }}
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{ $instructionNumbersSum ?? 0 }}
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{ $kanbanNumbersSum * $item['number_of_accommodated'] ?? 0 }}
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{-- Planned daily volume	 --}}
                                                        @if($item['classification'] == 1)
                                                            {{ number_format((($kanbanNumbersSum * $item['number_of_accommodated']) / 31), 2) ?? 0 }}
                                                        @else
                                                            {{ number_format(($instructionNumbersSum), 2) ?? 0 }}
                                                        @endif
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{-- Actual daily volume	 --}}
                                                        @if($item['classification'] == 1)
                                                            {{ number_format((($shipmentKanbanSum * $item['number_of_accommodated']) / 31), 2) ?? 0 }}
                                                        @else
                                                            {{ number_format(($shipmentInstructionSum), 2) ?? null }}
                                                        @endif
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{-- Actual daily volume divided by Planned daily volume --}}
                                                        @if($item['classification'] == 1)
                                                            @php
                                                                $dividend = (($kanbanNumbersSum * $item['number_of_accommodated']) / 31);
                                                                $divisor = (($shipmentKanbanSum * $item['number_of_accommodated']) / 31);
                                                        
                                                                $percentage = $divisor !== 0 ? ($dividend / $divisor) * 100 : 0;
                                                            @endphp
                                                        
                                                            <span class="{{ $percentage < 50 ? 'text-red' : '' }}">
                                                                {{ number_format($percentage, 0) }} %
                                                            </span>
                                                        @else
                                                            @php
                                                                $dividend = ($instructionNumbersSum);
                                                                $divisor = ($shipmentInstructionSum);
                                                        
                                                                $percentage =  $divisor !== 0 ? ($dividend / $divisor) * 100 : 0;
                                                            @endphp
                                                        
                                                            <span class="{{ $percentage < 50 ? 'text-red' : '' }}">
                                                                {{ number_format($percentage, 0) }} %
                                                            </span>
                                                        @endif

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        {{ $item['product_name'] }}
                                                    </td>
                                                    @foreach($deliveryNos as $deliveryNo)
                                                        @foreach($partData[0]['daily_reports'] as $deliveryNumber => $daily)
                                                            <td class="text-right">
                                                                @if(Request::get('category') == 1 || Request::get('category') == '')
                                                                    @if ($deliveryNumber == $deliveryNo)
                                                                        {{ $daily['shipment_kanban_number'] }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                @if(Request::get('category') == 2 || Request::get('category') == '')
                                                                    @if ($deliveryNumber == $deliveryNo)
                                                                        {{ $daily['shipment_instruction_number'] }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @empty
                                                <tr>
                                                    <td colspan="35" class="text-center">検索結果はありません</td>
                                                </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif(Request::get('display_info') == 'order_only')
                        <div class="mb-3">
                            @if(count($result) > 0)
                            <span>
                                {{ $result->total() }}件中、{{ $result->firstItem() ?? 0 }}件～{{ $result->lastItem() ?? 0 }}件を表示してます
                            </span>
                            <span class="float-right">
                                上段：注文情報　下段：実績情報
                            </span>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">受入</th>
                                            <th rowspan="2">工場</th>
                                            <th rowspan="2">品番・品名</th>
                                            <th rowspan="2">背番号</th>
                                            <th rowspan="2">収容数</th>
                                            <th colspan="2" class="text-red f-bold">遅れ</th>
                                            @foreach($deliveryNos as $deliveryNo)
                                                <th colspan="2">{{ $deliveryNo }}便</th>
                                            @endforeach
                                            <th rowspan="2">かんばん枚合計</th>
                                            <th rowspan="2">指示数計</th>
                                            <th rowspan="2">個数計</th>
                                            <th rowspan="2">計画日量</th>
                                            <th rowspan="2">実績日量</th>
                                            <th rowspan="2">納品率(%)</th>
                                        </tr>
                                        <tr>
                                            <th class="text-red f-bold">枚</th>
                                            <th class="text-red f-bold">個</th>
                                            @foreach($deliveryNos as $deliveryNo)
                                                <th>枚</th>
                                                <th>個</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($result as $partNumber => $partData)
                                            
                                        @foreach ($partData as $item)
                                            <tr>
                                                <td rowspan="2" class="valign-center">{{ $item['acceptance'] }}</td>
                                                <td rowspan="2" class="valign-center">{{ $item['plant'] }}</td>
                                                <td>{{ $item['part_number'] }}</td>
                                                <td rowspan="2" class="valign-center">{{ $item['uniform_number'] }}</td>
                                                <td rowspan="2" class="valign-center text-right">{{ $item['number_of_accommodated'] }}</td>
                                                <td rowspan="2" class="bg-red text-right">
                                                        @foreach($partData[0]['daily_reports'] as $daily)
                                                            @php
                                                                $kanbanNumbersSum = 0;
                                                                $instructionNumbersSum = 0;
                                                                $shipmentKanbanSum = 0;
                                                                $shipmentInstructionSum = 0;

                                                                $kanbanNumbersSum += $daily['kanban_number'];
                                                                $instructionNumbersSum += $daily['instruction_number'];
                                                                $shipmentKanbanSum += $daily['shipment_kanban_number'];
                                                                $shipmentInstructionSum += $daily['shipment_instruction_number'];
                                                            @endphp
                                                        @endforeach
                                                    @if(Request::get('category') == 1 || Request::get('category') == '')
                                                        {{ $kanbanNumbersSum - $shipmentKanbanSum }}
                                                    @endif
                                                </td>
                                                <td rowspan="2" class="bg-red text-right">
                                                    @if(Request::get('category') == 2 || Request::get('category') == '')
                                                        {{ $instructionNumbersSum - $shipmentInstructionSum }}
                                                    @endif
                                                </td>
                                                @foreach($deliveryNos as $deliveryNo)
                                                    @foreach($partData[0]['daily_reports'] as $deliveryNumber => $daily)
                                                        <td class="text-right">
                                                            @if(Request::get('category') == 1 || Request::get('category') == '')
                                                                @if ($deliveryNumber == $deliveryNo)
                                                                    {{ $daily['kanban_number'] }}
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td class="text-right">
                                                            @if(Request::get('category') == 2 || Request::get('category') == '')
                                                                @if ($deliveryNumber == $deliveryNo)
                                                                    {{ $daily['instruction_number'] }}
                                                                @endif
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                @endforeach
                                                <td rowspan="2" class="valign-center text-right">
                                                    {{ $kanbanNumbersSum ?? 0 }}
                                                </td>
                                                <td rowspan="2" class="valign-center text-right">
                                                    {{ $instructionNumbersSum ?? 0 }}
                                                </td>
                                                <td rowspan="2" class="valign-center text-right">
                                                    {{ $kanbanNumbersSum * $item['number_of_accommodated'] ?? 0 }}
                                                </td>
                                                <td rowspan="2" class="valign-center text-right">
                                                    {{-- Planned daily volume	 --}}
                                                    @if($item['classification'] == 1)
                                                        {{ number_format((($kanbanNumbersSum * $item['number_of_accommodated']) / 31), 2) ?? 0 }}
                                                    @else
                                                        {{ number_format(($instructionNumbersSum), 2) ?? 0 }}
                                                    @endif
                                                </td>
                                                <td rowspan="2" class="valign-center text-right">
                                                    {{-- Actual daily volume	 --}}
                                                    0
                                                </td>
                                                <td rowspan="2" class="valign-center text-right">
                                                    {{-- Actual daily volume divided by Planned daily volume --}}
                                                    0

                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    {{ $item['product_name'] }}
                                                </td>
                                                @foreach($deliveryNos as $deliveryNo)
                                                    <td></td>
                                                    <td></td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @empty
                                            <tr>
                                                <td colspan="35" class="text-center">検索結果はありません</td>
                                            </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif(Request::get('display_info') == 'performance_only')
                        <div class="mb-3">
                            @if(count($result) > 0)
                            <span>
                                {{ $result->total() }}件中、{{ $result->firstItem() ?? 0 }}件～{{ $result->lastItem() ?? 0 }}件を表示してます
                            </span>
                            <span class="float-right">
                                上段：注文情報　下段：実績情報
                            </span>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th rowspan="2">受入</th>
                                            <th rowspan="2">工場</th>
                                            <th rowspan="2">品番・品名</th>
                                            <th rowspan="2">背番号</th>
                                            <th rowspan="2">収容数</th>
                                            <th colspan="2" class="text-red f-bold">遅れ</th>
                                            @foreach($deliveryNos as $deliveryNo)
                                                <th colspan="2">{{ $deliveryNo }}便</th>
                                            @endforeach
                                            <th rowspan="2">かんばん枚合計</th>
                                            <th rowspan="2">指示数計</th>
                                            <th rowspan="2">個数計</th>
                                            <th rowspan="2">計画日量</th>
                                            <th rowspan="2">実績日量</th>
                                            <th rowspan="2">納品率(%)</th>
                                        </tr>
                                        <tr>
                                            <th class="text-red f-bold">枚</th>
                                            <th class="text-red f-bold">個</th>
                                            @foreach($deliveryNos as $deliveryNo)
                                                <th>枚</th>
                                                <th>個</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($result as $partNumber => $partData)
                                            
                                            @foreach ($partData as $item)
                                                <tr>
                                                    <td rowspan="2" class="valign-center">{{ $item['acceptance'] }}</td>
                                                    <td rowspan="2" class="valign-center">{{ $item['plant'] }}</td>
                                                    <td>{{ $item['part_number'] }}</td>
                                                    <td rowspan="2" class="valign-center">{{ $item['uniform_number'] }}</td>
                                                    <td rowspan="2" class="valign-center text-right">{{ $item['number_of_accommodated'] }}</td>
                                                    <td rowspan="2" class="bg-red text-right">
                                                            @foreach($partData[0]['daily_reports'] as $daily)
                                                                @php
                                                                    $kanbanNumbersSum = 0;
                                                                    $instructionNumbersSum = 0;
                                                                    $shipmentKanbanSum = 0;
                                                                    $shipmentInstructionSum = 0;

                                                                    $kanbanNumbersSum += $daily['kanban_number'];
                                                                    $instructionNumbersSum += $daily['instruction_number'];
                                                                    $shipmentKanbanSum += $daily['shipment_kanban_number'];
                                                                    $shipmentInstructionSum += $daily['shipment_instruction_number'];
                                                                @endphp
                                                            @endforeach
                                                        
                                                    </td>
                                                    <td rowspan="2" class="bg-red text-right">
                                                    </td>
                                                    @foreach($deliveryNos as $deliveryNo)
                                                        @foreach($partData[0]['daily_reports'] as $deliveryNumber => $daily)
                                                            <td></td>
                                                            <td></td>
                                                        @endforeach
                                                    @endforeach
                                                    <td rowspan="2" class="valign-center text-right">
                                                        0
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{ $instructionNumbersSum ?? 0 }}
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        0
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{-- Planned daily volume	 --}}
                                                        0
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{-- Actual daily volume	 --}}
                                                        @if($item['classification'] == 1)
                                                            {{ number_format((($shipmentKanbanSum * $item['number_of_accommodated']) / 31), 2) ?? 0 }}
                                                        @else
                                                            {{ number_format(($shipmentInstructionSum), 2) ?? null }}
                                                        @endif
                                                    </td>
                                                    <td rowspan="2" class="valign-center text-right">
                                                        {{-- Actual daily volume divided by Planned daily volume --}}
                                                        0

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        {{ $item['product_name'] }}
                                                    </td>
                                                    @foreach($deliveryNos as $deliveryNo)
                                                        @foreach($partData[0]['daily_reports'] as $deliveryNumber => $daily)
                                                            <td class="text-right">
                                                                @if(Request::get('category') == 1 || Request::get('category') == '')
                                                                    @if ($deliveryNumber == $deliveryNo)
                                                                        {{ $daily['shipment_kanban_number'] }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                            <td class="text-right">
                                                                @if(Request::get('category') == 2 || Request::get('category') == '')
                                                                    @if ($deliveryNumber == $deliveryNo)
                                                                        {{ $daily['shipment_instruction_number'] }}
                                                                    @endif
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        @empty
                                                <tr>
                                                    <td colspan="35" class="text-center">検索結果はありません</td>
                                                </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                
                    @endif
                </div>
                @if(count($result) > 0)
                    {{ $result->appends(request()->all())->links() }}
                @endif
            </div>
           
        </div>
    </div>
@include('partials.modals.masters._search', [
    'modalId' => 'searchCustomerModal',
    'searchLabel' => '納入先',
    'resultValueElementId' => 'customer_code',
    'resultNameElementId' => 'customer_name',
    'model' => 'Customer'
])
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
@include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '外注先',
    'resultValueElementId' => 'supplier_code',
    'resultNameElementId' => 'supplier_name',
    'model' => 'Supplier'
])
<script>
    // Get the "検索条件をクリア" button element
    var clearButton = document.getElementById('clear-form');

    // Add a click event listener to the button
    clearButton.addEventListener('click', function() {
        // Get all the input elements in the form
        var formInputs = document.querySelectorAll('form input');

        // Loop through each input element and set its value to an empty string
        for (var i = 0; i < formInputs.length; i++) {
            formInputs[i].value = '';
        }
    });
</script>
@endsection
