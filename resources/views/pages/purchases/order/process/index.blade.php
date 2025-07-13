@extends('layouts.app')

@section('title', '発注処理')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>発注処理</span>
                </h1>
            </div>
            @if (session('success'))
                <div id="card"
                    style="background-color: #ffffff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div>
                        <p style="font-size: 18px; color: #0d9c38">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif
            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form accept-charset="utf-8" class="overlayedSubmitForm with-js-validation" id="approveSeachList"
            data-disregard-empty="true">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                            <!-- 得意先 -->
                            <tr>
                                <td>
                                    <dl class="formsetBox mw-100">
                                        <dt class="requiredForm">発注先</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="supplier_code" id="supplier_code"
                                                    data-validate-exist-model="Customer"
                                                    data-validate-exist-column="customer_code"
                                                    data-inputautosearch-model="Customer"
                                                    data-inputautosearch-column="customer_code"
                                                    data-inputautosearch-return="customer_name"
                                                    data-inputautosearch-reference="supplier_name" class="text-left"
                                                    minlength="6" maxlength="6"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    value="{{$request['supplier_code'] ?? '' }}" required
                                                    style="width: 80px;" />
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly name="supplier_name" id="supplier_name"
                                                    value="{{ $request['supplier_name'] ?? '' }}" class="middle-name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchSupplierModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div class="error_msg"></div>
                                            <div data-error-container="supplier_code"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <dl class="formsetBox w-100">
                                        <dt class="">依頼日</dt>
                                        <dd>
                                          <div class="mr-3">
                                            <div class="d-flex">
                                                @include('partials._date_picker', ['inputName' => 'request_date_start', 'attributes' => 'data-error-messsage-container=#request_error_message'])
                                                <span style="font-size:24px; padding:5px 10px;">
                                                    ~
                                                </span>
                                                @include('partials._date_picker', ['inputName' => 'request_date_end', 'attributes' => 'data-error-messsage-container=#request_error_message'])
                                            </div>
                                            <div id="request_error_message"></div>
                                        </div>
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox w-100">
                                        <dt class="">納期</dt>
                                        <dd>
                                          <div class="mr-3">
                                            <div class="d-flex">
                                                @include('partials._date_picker', ['inputName' => 'deadline_date_start', 'attributes' => 'data-error-messsage-container=#deadline_line_error_message'])
                                                <span style="font-size:24px; padding:5px 10px;">
                                                    ~
                                                </span>
                                                @include('partials._date_picker', ['inputName' => 'deadline_date_end', 'attributes' => 'data-error-messsage-container=#deadline_line_error_message'])
                                            </div>
                                            <div id="deadline_line_error_message"></div>
                                          </div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <dl class="formsetBox mw-100">
                                        <dt>部門</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="department_code_start" minlength="6"
                                                    maxlength="6" style="width: 80px; margin-right: 10px"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    value="{{ $request['department_code_start']  ?? ''}}"
                                                    id="department_code_start" class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchDepartmentModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <p class="formPack">～</p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="department_code_end" minlength="6"
                                                    maxlength="6" style="width: 80px; margin-right: 10px"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    value="{{ $request['department_code_end'] ?? '' }}" id="department_code_end"
                                                    class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchDepartment2Modal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox mw-100">
                                        <dt>ライン</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="line_code_start"
                                                    value="{{ $request['line_code_start'] ?? '' }}" id="line_code_start"
                                                    class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchLineModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <p class="formPack">～</p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="line_code_end"
                                                    value="{{ $request['line_code_end'] ?? '' }}" id="line_code_end"
                                                    class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchLine2Modal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox mw-100">
                                        <dt>依頼者</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="employee_code" id="employee_code"
                                                    data-validate-exist-model="Employee"
                                                    data-validate-exist-column="employee_code"
                                                    data-inputautosearch-model="Employee"
                                                    data-inputautosearch-column="employee_code"
                                                    data-inputautosearch-return="employee_name"
                                                    data-inputautosearch-reference="employee_name" class="text-left"
                                                    value="{{ $request['employee_code'] ?? '' }}" />
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly name="employee_name" id="employee_name"
                                                    value="{{ $request['employee_name'] ?? '' }}" class="middle-name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchEmployeeModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <dl class="formsetBox w-100">
                                        <dt class="">購買依頼No.</dt>
                                        <dd>
                                            <input type="text" name="requisition_number"
                                                value="{{ $request['requisition_number'] ?? '' }}" class="middle-name">
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox w-100">
                                        <dt class="">承認方法</dt>
                                        <dd>
                                            <p class="formPack">
                                                <label class="checkBasic">
                                                    <input type="checkbox" name="approval_method_category[]" @if(in_array('1', $request['approval_method_category'] ?? [])) checked @endif value="1">
                                                    <span>システム</span>
                                                </label>
                                            </p>
                                            <p class="formPack">
                                                <label class="checkBasic">
                                                    <input type="checkbox" name="approval_method_category[]" @if(in_array('2', $request['approval_method_category'] ?? [])) checked @endif value="2">
                                                    <span>依頼書</span>
                                                </label>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox w-100">
                                        <dt class="">強制発注</dt>
                                        <dd>
                                            <p class="formPack">
                                                <label class="checkBasic">
                                                    <input type="checkbox" name="classification" @checked($request['classification'] ?? '') value="1">
                                                    <span>システム承認中も表示</span>
                                                </label>
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <ul class="buttonlistWrap">
                        <li>
                            <button type="button" 
                                class="btn btn-primary buttonBasic bColor-ok"
                                data-clear-inputs
                                data-clear-form-target="#approveSeachList">検索条件をクリア</button>
                        </li>
                        <li>
                            <button type="submit" class="btn btn-primary buttonBasic bColor-ok">検索</button>
                        </li>
                    </ul>

                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <form action="{{ route('purchase.orderConfirm.index') }}" method="GET" class="overlayedSubmitForm"
                data-disregard-empty="true">
                <div class="tableWrap bordertable p-3" style="clear: both;">

                    <ul class="headerList">
                        @if ($items)
                            {{ $items->total() }}件中、{{ $items->firstItem() }}件～{{ $items->lastItem() }} 件を表示しています
                        @endif
                    </ul>
                    <table class="tableBasic list-table bordered mt-3 mb-3">
                        <thead>
                            <tr class="p-2">
                                <th rowspan="2" width="25">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th>部門</th>
                                <th>発注先</th>
                                <th>数量</th>
                                <th>単位</th> 
                                <th>依頼日</th>
                                <th rowspan="2">
                                    依頼者<br>
                                    購買依頼No.
                                </th>
                                <th rowspan="2">承認方法</th>
                                <th rowspan="2">操作</th>
                            </tr>
                            <tr class="p-2">
                                <th>ライン</th>
                                <th>品番・品名・規格</th>
                                <th>単価</th>
                                <th>金額</th>
                                <th>納期</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($items)
                                @forelse($items as $item)
                                    <tr class="p-2" data-pruchase-requisition-id="{{ $item->id }}">
                                        <td rowspan="2" style="vertical-align: middle; text-align: center;">
                                            <input type="checkbox" name="selected_items[]" value="{{ $item->id }}"
                                                @if (in_array($item->id, session('selected_items', []))) checked @endif>
                                        </td>
                                        <td class="tA-le" style="vertical-align: middle">
                                            @if ($item->department_code != null)
                                                {{ '[' . $item->department_code . '] ' }}
                                            @endif
                                            @if ($item->department?->department_name)
                                                {{ $item->department?->department_name }}
                                            @endif
                                        </td>
                                        <td class="tA-le" style="vertical-align: middle">{{ $item->supplier?->customer_name }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $item->quantity }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $item->unit?->name }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $item->requested_date?->format('Y/m/d') }}</td>
                                        <td rowspan="2" class="tA-cn" style="vertical-align: middle">
                                            {{ $item->employee?->employee_name }}<br>
                                            {{ $item->requisition_number }}
                                        </td>
                                        <td rowspan="2" class="tA-cn" style="vertical-align: middle">
                                            @if ($item->approval_method_category == 1)
                                                システム
                                            @else
                                                依頼書
                                            @endif
                                        </td>
                                        <td rowspan="2" style="vertical-align: middle; text-align: center; width: 100px;">
                                            <a href="{{ route('purchase.orderProcessDetail.show',  array_merge([$item->id], request()->query()) ) }}"
                                                class="buttonBasic bColor-ok">編集</a>
                                        </td>
                                    </tr>
                                    <tr class="p-2">
                                        <td class="tA-le" style="vertical-align: middle">
                                            @if ($item->line_code)
                                                {{ '[' . $item->line_code . '] ' }}
                                            @endif
                                            @if ($item->line?->line_name)
                                                {{ $item->line?->line_name }}
                                            @endif
                                        </td>
                                        <td class="tA-le" style="vertical-align: middle">{{ implode("・", array_filter([$item->part_number, $item->product_name, $item->standard])) }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $item->unit_price }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $item->amount_of_money }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $item->deadline?->format('Y/m/d') }}</td>
                                    </tr>
                                @empty
                                    <tr class="p-2">
                                        <td colspan="9" class="tA-cn">
                                            検索結果はありません
                                        </td>
                                    </tr>
                                @endforelse
                            @else
                                <tr class="p-2">
                                    <td colspan="9" class="tA-cn">
                                        検索結果はありません
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                     </table>
                    @if ($items)
                        {{ $items->appends(request()->all())->links() }}
                    @endif
                </div>

                <div class="text-right mt-4">
                    <div>
                        <button id="orderSlip" class="btn btn-success btn-wide">
                            チェックした発注内容を確認
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_start',
        'resultNameElementId' => 'department_name',
        'model' => 'Department',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartment2Modal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_end',
        'resultNameElementId' => 'department_name',
        'model' => 'Department',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_start',
        'resultNameElementId' => 'line_name',
        'model' => 'Line',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLine2Modal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_end',
        'resultNameElementId' => 'line_name',
        'model' => 'Line',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '依頼者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee',
    ])
@endsection
@vite(['resources/js/purchase/order/process/index.js'])
