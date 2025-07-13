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

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form  accept-charset="utf-8" class="overlayedSubmitForm" id="form" data-disregard-empty="true">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                            <!-- 得意先 -->
                            <tr >
                                <td style="width: 30%;">
                                    <dl class="formsetBox">
                                        <dt class="">発注先</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="supplier_code"  value="{{ Request::get('supplier_code') }}" id="supplier_code" class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                      name="supplier_name"
                                                      id="supplier_name"
                                                      value="{{ Request::get('supplier_name') }}"
                                                      class="middle-name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchSupplierModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox w-100">
                                        <dt class="">依頼日</dt>
                                        <dd>
                                            @include('partials._date_picker_estimates', ['inputName' => 'request_date_from', 'value' => Request::get("request_date_from"), 'required' => false])
                                            <p class="formPack">～</p>
                                            @include('partials._date_picker_estimates', ['inputName' => 'request_date_to',  'value' => Request::get("request_date_to"), 'required' => false])
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30%;">
                                    <dl class="formsetBox">
                                        <dt class="">部門</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="department_code"  value="{{ Request::get('department_code') }}" id="department_code" class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                      name="department_name"
                                                      id="department_name"
                                                      value="{{ Request::get('department_name') }}"
                                                      class="middle-name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchDepartmentModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td>
                                    <dl class="formsetBox w-100">
                                        <dt class="">納期</dt>
                                        <dd>
                                            @include('partials._date_picker_estimates', ['inputName' => 'deadline_date_from', 'value' => Request::get("deadline_date_from"), 'required' => false])
                                            <p class="formPack">～</p>
                                            @include('partials._date_picker_estimates', ['inputName' => 'deadline_date_to',  'value' => Request::get("deadline_date_to"), 'required' => false])
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30%;">
                                    <dl class="formsetBox">
                                        <dt class="">依頼者</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="employee_code"  value="{{ Request::get('employee_code') }}" id="employee_code" class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                      name="employee_name"
                                                      id="employee_name"
                                                      value="{{ Request::get('employee_name') }}"
                                                      class="middle-name">
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
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>承認方法</dt>
                                        <dd>
                                            <label class='mr-1'>
                                                <input type="radio" name="approval_method_category" value="all" @if(Request::get('approval_method_category') == 'all') checked @else checked @endif> すべて
                                            </label>
                                            <label class='mr-1'>
                                                <input type="radio" name="approval_method_category" value="1" @if(Request::get('approval_method_category') == '1') checked @endif> システム
                                            </label>
                                            <label class='mr-1'>
                                                <input type="radio" name="approval_method_category" value="2" @if(Request::get('approval_method_category') == '2') checked @endif> 依頼書
                                            </label>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 30%;">
                                    <dl class="formsetBox">
                                        <dt class="">ライン</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="line_code"  value="{{ Request::get('line_code') }}" id="line_code" class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                      name="line_name"
                                                      id="line_name"
                                                      value="{{ Request::get('line_name') }}"
                                                      class="middle-name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchLineModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>状態</dt>
                                        <dd>
                                            <label class='mr-1'>
                                                <input type="radio" name="state_classification" value="all" @if(in_array(Request::get('state_classification'), ['all', ''])) checked @endif> すべて
                                            </label>
                                            <label class='mr-1'>
                                                <input type="radio" name="state_classification" value="0" @if(Request::get('state_classification') == "0") checked @endif> 依頼中
                                            </label>
                                            <label class='mr-1'>
                                                <input type="radio" name="state_classification" value="1" @if(Request::get('state_classification') == "1") checked @endif> 承認中
                                            </label>
                                            <label class='mr-1'>
                                                <input type="radio" name="state_classification" value="2" @if(Request::get('state_classification') == "2") checked @endif> 承認済
                                            </label>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>購買依頼No.</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="requisition_number" value="{{ Request::get("requisition_number") }}">
                                            </p>
                                        </dd>
                                    </dl>
                                </td>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>強制発注</dt>
                                        <dd>
                                            <p class="formPack">
                                                <label class="customLabelCheck">
                                                    <input type="checkbox" name="forced_order" value="1"
                                                           class="chk-middle-name" @checked(Request::get('forced_order'))>
                                                    <span></span> システム承認の「依頼中」,「承認中」も表示
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
                            <a href="{{ route("purchase.order.processing") }}"
                                   class="buttonBasic bColor-ok">検索条件をクリア</a>
                        </li>
                        <li>
                            <input type="submit" value="検索"
                                   class="buttonBasic bColor-ok">
                        </li>
                    </ul>

                    <a href="{{ route("purchase.order.processing.export", Request::all()) }}" class="float-right btn btn-green" style="margin-top: -35px;">検索結果をEXCEL出力</a>
                    
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                
                <ul class="headerList">
                    @if($items)
                        {{ $items->total() }}件中、{{ $items->firstItem() }}件～{{ $items->lastItem() }} 件を表示しています
                    @endif
                </ul>
                <table class="tableBasic list-table bordered">
                    <thead>
                        <tr>
                            <th>承認方法</th>
                            <th>発注先</th>
                            <th>依頼者</th>
                            <th rowspan="2"><input type="checkbox"></th>
                            <th rowspan="2">操作</th>
                            <th>数量</th>
                            <th>単位</th>
                            <th>依頼日</th>
                            <th>部門</th>
                        </tr>
                        <tr>
                            <th>状態</th>
                            <th>品番・品名・規格</th>
                            <th>購買依頼No.</th>
                            <th>単価</th>
                            <th>金額</th>
                            <th>納期</th>
                            <th>ライン</th>
                        </tr>
                    </thead>
                        <tbody>
                            @forelse($items as $item)
                                <tr>
                                    <td class="tA-le">
                                        @if( $item->approval_method_category == 1)
                                        システム
                                        @elseif( $item->approval_method_category == 2)
                                        依頼書
                                        @endif
                                    </td>
                                    <td class="tA-ri">{{ $item->supplier_code }}</td>  
                                    <td class="tA-ri">{{ $item->creator }}</td>
                                    
                                    <td class="tA-cn" rowspan="2"><input type="checkbox"></td>
                                    <td class="tA-cn" rowspan="2">
                                        <a href="{{ route("purchase.order.processing.detail", $item) }}" class="buttonBasic bColor-ok">操作</a>
                                    </td>
                                    <td class="tA-ri">{{ $item->quantity }}</td>
                                    <td class="tA-ri">{{ $item->unit?->name }}</td>
                                    <td class="tA-ri">{{ $item->requested_date?->format('Y-m-d') }}</td>
                                    <td class="tA-ri">{{ $item->department_code }}</td>
                                </tr>
                                <tr>
                                    <td class="tA-le">
                                        @if( $item->state_classification == 0)
                                            依頼中
                                        @elseif( $item->approval_method_category == 1)
                                            承認中
                                        @elseif( $item->approval_method_category == 2)
                                            承認済
                                        @elseif( $item->approval_method_category == 3)
                                            発注済
                                        @elseif( $item->approval_method_category == 4)
                                            入荷済
                                        @elseif( $item->approval_method_category == 9)
                                            否認
                                        @endif
                                    </td>
                                    <td class="tA-ri">{{ $item->part_number }}</td>
                                    <td class="tA-ri">{{ $item->requisition_number }}</td>
                                    <td class="tA-ri">{{ $item->unit_price }}</td>
                                    <td class="tA-ri">{{ $item->amount_of_money }}</td>
                                    <td class="tA-ri">{{ $item->deadline?->format('Y-m-d') }}</td>
                                    <td class="tA-ri">{{ $item->line_code }}</td>
                                </tr>
        
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="tA-cn">
                                        検索結果はありません
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </thead>
                </table>
                @if ($items)
                    {{ $items->appends(request()->all())->links() }}
                @endif
            </div>
    
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
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
        'resultValueElementId' => 'line_code_from',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '依頼者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_from',
        'resultNameElementId' => 'Item_name',
        'model' => 'Item'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItem2Modal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_to',
        'resultNameElementId' => 'Item_name',
        'model' => 'Item'
    ])
@endsection

