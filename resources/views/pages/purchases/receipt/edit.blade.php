@extends('layouts.app')

@section('title', '入荷入力')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>入荷入力</span>
                </h1>
            </div>

            @if (session('success'))
                    <div id="card"
                        style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <div style="text-align: left;">
                            <p style="font-size: 18px; color: #0d9c38;">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                @endif
                @if (session('delete'))
                    <div id="card"
                        style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                        <div style="text-align: left;">
                            <p style="font-size: 18px; color: #d81414;">
                                {{ session('delete') }}
                            </p>
                        </div>
                    </div>
                @endif

            <div class="pagettlWrap">
                <h1><span>入荷入力</span></h1>
            </div>

            <div class="tableWrap borderLesstable inputFormArea">
                <table class="tableBasic">
                    <tbody>
                        <tr>
                            <td width="30%">
                                <dl class="formsetBox">
                                    <dt>注文書No.</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw75p">
                                            <input type="text" maxlength="200"
                                                value="{{ $requisition->purchase_order_number ?? '' }}" readonly>
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
                                            <input type="text" value="{{ $requisition->requisition_number ?? ''}}" readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 30%;">
                                <dl class="formsetBox">
                                    <dt>依頼者</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw75p">
                                            <input type="text" value="{{ $requisition?->employee?->employee_name ?? ''}}"
                                                readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="10%">
                                <dl class="formsetBox">
                                    <dt>発注日</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw30p">
                                            <input type="date" value="{{ optional($requisition->order_date)->format('Y-m-d') ?? ''}}"
                                                readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <td width="30%">
                                <dl class="formsetBox">
                                    <dt>納期</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw30p">
                                            <input type="date" value="{{ optional($requisition->deadline)->format('Y-m-d') ?? ''}}"
                                                readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                <dl class="formsetBox">
                                    <dt>部門</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw25p">
                                            <input type="text" value="{{ $requisition?->department?->code ?? ''}}" readonly>
                                        </p>
                                        <p class="formPack fixedWidth fpfw50p box-middle-name">
                                            <input type="text" readonly value="{{ $requisition?->department?->name ?? ''}}"
                                                class="middle-name">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                <dl class="formsetBox">
                                    <dt>ライン</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw25p">
                                            <input type="text" value="{{ $requisition?->line?->line_code ?? ''}}" readonly>
                                        </p>
                                        <p class="formPack fixedWidth fpfw50p box-middle-name">
                                            <input type="text" readonly value="{{ $requisition?->line?->line_name ?? ''}}"
                                                class="middle-name">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%">
                                <dl class="formsetBox">
                                    <dt>機番</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw25p">
                                            <input type="text" value="{{ $requisition?->machine?->machine_code ?? ''}}"
                                                readonly maxlength="5">
                                        </p>
                                        <p class="formPack fixedWidth fpfw50p box-middle-name">
                                            <input type="text" readonly
                                                value="{{ $requisition?->machine?->machine_name ?? ''}}" class="middle-name">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%">
                                <dl class="formsetBox">
                                    <dt>品番</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" value="{{ $requisition->part_number ?? ''}}" readonly
                                                maxlength="70">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <td width="20%">
                                <dl class="formsetBox">
                                    <dt>品名</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" maxlength="60" value="{{ $requisition->product_name ?? ''}}"
                                                readonly>
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
                                            <input type="text" maxlength="40" value="{{ $requisition->standard ?? ''}}"
                                                readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="20%">
                                <dl class="formsetBox">
                                    <dt>発注数</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" pattern="[0-9]*" maxlength="7"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                value="{{ $requisition->quantity ?? ''}}" readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <td width="5%">
                                <dl class="formsetBox">
                                    <dt>単位</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" value="{{ $requisition?->unit?->name ?? '' }}" readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%">
                                <dl class="formsetBox">
                                    <dt>購入理由</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" value="{{ $requisition->remarks ?? ''}}"
                                                readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%">
                                <dl class="formsetBox">
                                    <dt>発注時備考</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" value="{{ $requisition->reason_for_denial ?? ''}}" readonly>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td width="40%">
                                <dl class="formsetBox">
                                    <dt>入荷履歴</dt>
                                    <dd>
                                        <table style="width: 100%; border: 1px solid #000;" class="align-middle">
                                            <thead>
                                                <tr>
                                                    <th style="border: 1px solid #000; height: 40px; padding: 8px;">
                                                        入荷日
                                                    </th>
                                                    <th style="border: 1px solid #000; height: 40px; padding: 8px;">
                                                        入荷数
                                                    </th>
                                                    <th style="border: 1px solid #000; height: 40px; padding: 8px;">
                                                        伝票No.
                                                    </th>
                                                    <th style="border: 1px solid #000; height: 40px; padding: 8px;">
                                                        入荷時備考
                                                    </th>
                                                    <th style="border: 1px solid #000; height: 40px; padding: 8px;">
                                                        操作
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($items as $item)
                                                    <tr>
                                                        <td style="border: 1px solid #000; height: 40px; padding: 10px;"
                                                            class="tA-cn">
                                                            {{ $item->arrival_day?->format('Y-m-d') }}
                                                        </td>
                                                        <td style="border: 1px solid #000; height: 40px; padding: 10px;"
                                                            class="tA-cn">
                                                            {{ $item->arrival_quantity }}
                                                        </td>
                                                        <td style="border: 1px solid #000; height: 40px; padding: 10px;"
                                                            class="tA-cn">
                                                            {{ $item->slip_no }}
                                                        </td>
                                                        <td style="border: 1px solid #000; height: 40px; padding: 10px;"
                                                            class="tA-cn">
                                                            {{ $item->remarks }}
                                                        </td>
                                                        <td style="border: 1px solid #000; height: 40px; padding: 10px;"
                                                            class="tA-cn">
                                                            <form method="POST"
                                                                action="{{ route('purchase.receipt.destroy', $item->id) }}">
                                                                @csrf
                                                                @method('DELETE')
                                                                @if (!isset($item->purchase_receipt_date) && $item->purchase_receipt_date == null)
                                                                    <input type="submit" class="buttonBasic bColor-etc"
                                                                        value="取消"
                                                                        style="height: 40px; padding: 8px; width: 80px;"
                                                                        onclick="return confirm('削除したい項目を選択します。削除しますか?');">
                                                                @endif
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="tA-cn">
                                                            検索結果はありません
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <sub class="float-right mt-2">※購買にて受入済みの入荷情報は取消できません。</sub>
                                    </dd>
                                </dl>
                            </td>
                        <tr>
                    </tbody>
                </table>
                <hr />
               
                <form action="{{ route('purchase.receipt.store') }}" accept-charset="utf-8" method="POST"
                    class="arrivalForm overlayedSubmitForm with-js-validation mt-4" id="arrivalForm"
                    enctype="multipart/form-data"
                    data-confirmation-message="入荷情報を登録します、よろしいでしょうか？">
                    @csrf @method('POST')
                    <table class="tableBasic">
                        <tbody>
                            <tr>
                                <td width="40%">
                                    <dl class="formsetBox">
                                        <dt>発注先</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw25p">
                                                <input type="text" name="supplier_code"
                                                    value="{{ $requisition->supplier_code ?? ''}}" id="supplier_code"
                                                    class="" readonly>
                                                <input type="text" name="purchase_order_no"
                                                    value="{{ $requisition?->purchase_order_number ?? ''}}" hidden>
                                                <input type="text" name="purchase_order_details_no"
                                                    value="{{ $requisition?->purchase_order_details_number ?? ''}}" hidden>
                                            </p>
                                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                                <input type="text" readonly name="supplier_name" id="supplier_name"
                                                    value="{{ $requisition?->supplier?->supplier_name_abbreviation ?? '' }}"
                                                    class="middle-name">
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="20%">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">入荷日</dt>
                                        <dd>
                                            <div class="d-flex">
                                                <input type="text"
                                                    data-validate-date-format="YYYYMMDD"
                                                    maxlength="8"
                                                    id="arrival_day"
                                                    class="w-50"
                                                    data-format="YYYYMMDD"
                                                    data-value="YYYYMMDD"
                                                    value="{{ request()->get('arrival_day') }}"
                                                    name="arrival_day"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                    required
                                                />
                                                    
                                                <button type="button" class="btnSubmitCustom buttonPickerJS ml-2 {{ $buttonClass ?? '' }}"
                                                    data-target="arrival_day"
                                                    data-format="YYYYMMDD"
                                                    >
                                                    <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}"
                                                        alt="iconsvg_calendar_w.svg">
                                                </button>
                                            </div>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%">
                                    <dl class="formsetBox">
                                        <dt class="requiredForm">入荷数</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" pattern="[0-9]*" maxlength="7"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                    min="0" name="arrival_quantity" required>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                                <td width="120px">
                                    <dl class="formsetBox">
                                        <dt>再建不能</dt>
                                        <dd>
                                            <p class="formPack fixedWidth ">
                                                <input type="checkbox" name="unable_to_resharpen_flag" value="1"
                                                    {{ (request()->cookie('unable_to_resharpen_flag') ?? '') != '' ? 'checked' : '' }}>
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="10%">
                                    <dl class="formsetBox">
                                        <dt>伝票No.</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="slip_no" maxlength="20">
                                            </p>
                                            <div class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                            <tr>
                                <td width="40%">
                                    <dl class="formsetBox">
                                        <dt>入力時備考</dt>
                                        <dd>
                                            <p class="formPack fixedWidth fpfw100p">
                                                <input type="text" name="remarks" oninput="document.getElementById('error_msg_remarks').textContent = this.value.length > 200 ? '200文字以内で入力してください。' : '';">
                                            </p>
                                            <div id="error_msg_remarks" class="error_msg"></div>
                                        </dd>
                                    </dl>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            </div>
            <div class="float-right">
                <ul class="buttonlistWrap">
                    <li>
                        <a href="{{ route('purchase.order.index',[
                            'order_date_end' => now()->endOfMonth()->format('Ymd'),
                            'status' => 'all',
                            'acceptance' => 'all'
                        ]) }}" class="buttonBasic bColor-ok">
                            メニューに戻る
                        </a>
                    </li>
                    <li>
                        <input type="submit" class="buttonBasic bColor-green "
                            style="height: 39px; padding: 8px; width: 100px;" value="この内容で登録する">
                    </li>
                </ul>
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

    @push('scripts')
    @vite(['resources/js/purchase/receipt/edit.js'])
    @endpush
@endsection
