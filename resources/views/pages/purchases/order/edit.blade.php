@extends('layouts.app')
@section('title', '発注入力')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>発注入力</span>
                </h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>発注入力</span></h1>
            </div>

            <form action="{{ route('purchase.order.update', array_merge([$item->id], request()->all())) }}" method="POST" id="orderForm" class="with-js-validation">
                @csrf
                @method('PUT')
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                            <tr>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>注文書No.</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw75p">
                                                <input type="text" name="" value="{{ $item->purchase_order_number  ?? ''}}" readonly>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>購買依頼No.</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw75p">
                                                <input type="text" name="" value="{{ $item->requisition_number ?? '' }}" readonly>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td style="max-width: 30%;">
                                    <dl class="formsetBox">
                                        <dt class="">依頼者</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name=""  value="{{ $item?->employee?->employee_name ?? '' }}" readonly>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>発注日</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw50p">
                                                <input type="text" name="" 
                                                value="{{ optional($item->order_date)->format('Ymd') ?? '' }}" placeholder="YYYY/MM/DD" class="" readonly>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>発注先</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name=""  
                                                data-validate-exist-model="Supplier" 
                                                data-validate-exist-column="customer_code" 
                                                data-error-messsage-container = "#customer_code-error"
                                                pattern="[0-9]*" 
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                                maxlength="3"
                                                value="{{ $item->supplier_code ?? '' }}" 
                                                id="supplier_code" class="" readonly>
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="supplier_name"
                                                    id="supplier_name"
                                                    value="{{ $item?->supplier?->customer_name ?? '' }}"
                                                    class="middle-name">
                                            </p>
                                            <div id="customer_code"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr> 
                            <tr>
                                <td style="max-width: 30%;">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">部門</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="department_code"
                                                data-error-messsage-container = "#department_code-error"
                                                data-validate-exist-model="department" 
                                                data-validate-exist-column="code" 
                                                data-inputautosearch-model="department" 
                                                data-inputautosearch-column="code" 
                                                data-inputautosearch-return="name" 
                                                data-inputautosearch-reference="department_name" 
                                                pattern="[0-9]*" 
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                                maxlength="6"
                                                value="{{ $item->department_code ?? ''}}" 
                                                id="department_code" class=""
                                                required>
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="department_name"
                                                    id="department_name"
                                                    value="{{ $item?->department?->name ?? '' }}"
                                                    class="middle-name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchDepartmentModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div id="department_code-error"></div>
                                            @error('department_code')
                                                <div class="error_msg text-danger">{{ $message }}</div>
                                            @enderror
                                            @error('department_name')
                                                <div class="error_msg text-danger">{{ $message }}</div>
                                            @enderror
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td style="max-width: 30%;">
                                    <dl class="formsetBox">
                                        <dt>ライン</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="line_code"
                                                data-validate-exist-model="line" 
                                                data-validate-exist-column="line_code" 
                                                data-inputautosearch-model="line" 
                                                data-inputautosearch-column="line_code" 
                                                data-inputautosearch-return="line_name" 
                                                data-inputautosearch-reference="line_name"
                                                data-error-messsage-container = '#line_name-error' 
                                                pattern="[0-9]*"
                                                maxlength="3"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ $item->line_code ?? ''}}" 
                                                id="line_code" class="">
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="line_name"
                                                    id="line_name"
                                                    value="{{ $item?->line?->line_name ?? ''}}"
                                                    class="middle-name">
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchLineModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            <div id="line_name-error"></div>
                                            @error('line_code')
                                                <div class="error_msg text-danger">{{ $message }}</div>
                                            @enderror
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">品番</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" id="part_number" name="part_number" value="{{ $item->part_number ?? ''}}" required>
                                            </p>
                                            <div class="error_msg"></div>
                                            @error('part_number')
                                                <div class="error_msg text-danger">{{ $message }}</div>
                                            @enderror
                                        </dd>
                                    </dl>
                                </td>
                                <td width="20%">
                                    <dl class="formsetBox">
                                        <dt>品名</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="product_name" value="{{ $item->product_name ?? ''}}">
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td width="20%">
                                    <dl class="formsetBox">
                                        <dt>規格</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="standard" value="{{ $item->standard ?? ''}}">
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">発注数</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="quantity"
                                                id="quantity"
                                                maxlength="7"
                                                pattern="[0-9]*" 
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                                value="{{ $item->quantity ?? ''}}"
                                                required>
                                            </p>
                                            <div class="error_msg"></div>
                                            @error('quantity')
                                                <div class="error_msg text-danger">{{ $message }}</div>
                                            @enderror
                                        </dd>
                                    </dl>
                                </td>
                                <td width="20%">
                                    <dl class="formsetBox">
                                        <dt>単位</dt>
                                        <dd>
                                            <select name="unit_code" style="width: 100%; height: 40px">
                                                @foreach($units as $unit)
                                                    <option value="{{ $unit->code }}" @if($item->unit_code == $unit->code) selected @endif>{{ $unit->name ?? ''}}</option>
                                                @endforeach
                                            </select>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <tr>
                                    <td width="20%">
                                        <dl class="formsetBox">
                                            <dt>単価</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p">
                                                    <input type="text" 
                                                    name="unit_price"
                                                    pattern="[0-9]*" 
                                                    maxlength="7"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');"  
                                                    value="{{ $item->unit_price ?? ''}}">
                                                </p>
                                                <div class="error_msg"></div>
                                            </dd>
                                        </dl>
                                    </td>
                                    <td width="20%" class="d-flex">
                                        <dl class="formsetBox">
                                            <dt>金額</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p">
                                                    <input type="text" name="amount_of_money"
                                                    id="amount_of_money"
                                                    maxlength="7"
                                                    pattern="[0-9]*" 
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');" 
                                                    value="{{ $item->amount_of_money ?? ''}}" readonly>
                                                </p>
                                                <div class="error_msg"></div>
                                            </dd>
                                        </dl>
                                    </td>
                                </tr>
                            <tr>
                                <td width="50%">
                                    <dl class="formsetBox">
                                        <dt>購入理由</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="reason" value="{{ $item->reason ?? ''}}" value="" class="">
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td style="max-width: 30%;">
                                    <dl class="formsetBox">
                                        <dt>費目</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="expense_items"
                                                    data-validate-exist-model="item"
                                                    data-validate-exist-column="expense_item"
                                                    data-inputautosearch-model="item"
                                                    data-inputautosearch-column="expense_item"
                                                    data-inputautosearch-return="item_name"
                                                    data-inputautosearch-reference="expense_item_name"
                                                    id="expense_item_code"
                                                    class="text-left searchOnInput Item acceptNumericOnly"
                                                    minlength="3"
                                                    maxlength="3"
                                                    value="{{ $item->expense_items ?? ''}}" required
                                                >
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly
                                                    name="expense_item_name"
                                                    id="expense_item_name"
                                                    value="{{ $item?->expense?->item_name ?? ''}}"
                                                    class="middle-name text-left"
                                                >
                                            </p>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <button type="button" class="btnSubmitCustom js-modal-open" data-target="searchItemModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}" alt="magnifying_glass.svg">
                                                </button>
                                            </p>
                                            @error('expense_items')
                                                <div class="error_msg text-danger">{{ $message }}</div>
                                            @enderror
                                        </dd>
                                    </dl>
                                </td>
                            <tr>
                            <tr>
                                <td width="30%">
                                    <dl class="formsetBox">
                                        <dt>納期</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                @include('partials._date_picker_estimates', [
                                                    'inputName' => 'deadline',
                                                    'value' => $item->deadline ? Date('Ymd', strtotime($item->deadline)) : null,
                                                    'required' => false
                                                ])
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                    <td width="50%">
                                        <dl class="formsetBox">
                                            <dt>発注時備考</dt>
                                            <dd>
                                                <p class="formPack fixedWidth fpfw100p">
                                                    <input type="text" name="remarks" value="{{ $item->remarks ?? ''}}" value="" class="">
                                                </p>
                                                <div class="error_msg"></div>
                                            </dd>
                                        </dl>
                                    </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
            @if(session('success'))
                <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: center;">
                        <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif
                <div class="float-right">
                    <ul class="buttonlistWrap">
                        <li>
                            <button type="button" class="btn btn-danger" 
                                style="width: 200px;" 
                                id="cancelOrder"
                                onclick="confirmCancelRequisition()">
                                発注取消
                            </button>

                            <form id="cancelForm" action="{{ route('purchase.order.cancel', $item->id) }}" method="POST" style="display: none;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="is_cancel" value="1">
                            </form>
                        </li>
                        <li>
                            <a href="{{ route("purchase.order.index",[
                                'order_date_start' => now()->startOfMonth()->format('Ymd'),
                                'order_date_end' => now()->endOfMonth()->format('Ymd'),
                                'status' => 'all',
                                'acceptance' => 'all'
                            ]) }}"
                                   class="buttonBasic bColor-ok"
                                   style="max-width: 200px;">一覧に戻る</a>
                        </li>
                        <li>
                            <button class="btn btn-primary"
                                   onclick="confirmClearInputContent()"
                                   style="width: 200px;">クリア</button>
                        </li>
                        <li>
                            <button class="btn btn-success"
                                   style="width: 200px;"
                                   onclick="orderButton()">
                                   この内容で登録する
                            </button>
                        </li>
                    </ul>
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
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_code', 
        'resultNameElementId' => 'expense_item_name',  
        'model' => 'Item'
    ])
@endsection

@vite('resources/js/purchase/order/edit.js')
