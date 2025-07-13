@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    <style>
        table td {
            vertical-align: middle !important;
        }

        tfoot {
            background-color: #dbe0f2;
        }

    </style>
@endpush

@section('title', '支払予定検索・一覧')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                支払予定検索・一覧
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索</h1>
                <form id="form" class="overlayedSubmitForm">
                <div class="box mb-3">
                    <div class="mb-3 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">年月</label> <span
                                class="btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" value="{{ request()->date }}" style="width: 150px" name="date" required>
                            </div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">振込銀行</label>
                            <div class="d-flex">
                                <select name="bank" style="width: 150px">
                                    <option value="_all">全て</option>
                                    @foreach ($banks as $bank)
                                        <option value="{{ $bank }}" {{ $bank == request()->bank ? 'selected' : '' }}>{{ $bank }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <a href="#" class="float-right btn btn-success btn-wide">検索結果をEXCEL出力</a>
                    <div class="text-center">
                        <a class="btn btn-primary btn-wide" href="{{ route('monthly.paymentScheduleList') }}"> 検索条件をクリア</a>
                        <button type="submit" class="btn btn-primary btn-wide">検索</button>
                    </div>
                </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    <div class="mb-2">
                        @if(($data != []))
                            {{ $data->total() }}件中、{{ $data->firstItem() }}件～{{ $data->lastItem() }} 件を表示しています
                        @endif
                        <table class="table table-bordered text-center table-striped-custom">
                            <thead>
                            <tr>
                                <th rowspan="2" width="6%"></th>
                                <th rowspan="2">仕入先コード</th>
                                <th rowspan="2">仕入先名</th>
                                <th rowspan="2">振込銀行</th>
                                <th>仕入額（税抜）</th>
                                <th rowspan="2">仕入額（税込）</th>
                                <th rowspan="2">相殺額</th>
                                <th rowspan="2">手数料</th>
                                <th rowspan="2">当月支払額</th>
                                <th rowspan="2">振込金額</th>
                                <th rowspan="2">振込金額</th>
                                <th rowspan="2" width="6%"></th>
                            </tr>
                            <tr>
                                <th>消費税額</th>
                            </tr>
                            </thead>
                                <tbody>
                                @php
                                    $total_tax_exempt_amount = 0;
                                    $total_taxable_amount = 0;
                                    $total_tax_amount = 0;
                                    $total_offset_amount = 0;
                                    $total_transfer_fee = 0;
                                    $total_bill_amount = 0;
                                    $total_transfer_amount = 0;
                                @endphp
                                @forelse ($data ?? [] as $row)
                                    @php
                                        $tax_amount = ($row->taxable_amount - $row->tax_amount);

                                        $total_tax_exempt_amount += $row->tax_exempt_amount;
                                        $total_taxable_amount += $row->taxable_amount;
                                        $total_tax_amount += $tax_amount;
                                        $total_offset_amount += $row->offset_amount;
                                        $total_transfer_fee += $row->transfer_fee;
                                        $total_bill_amount += $row->bill_amount;
                                        $total_transfer_amount += $row->transfer_amount;
                                    @endphp
                                    <tr>
                                        <td rowspan="2">
                                            <input type="checkbox">
                                        </td>
                                        <td rowspan="2" class="text-right">{{ $row->supplier_code }}</td>
                                        <td rowspan="2" class="text-left">{{ $row->supplier->customer_name ?? '' }}</td>
                                        <td rowspan="2" class="text-left">{{ $row->supplier->transfer_source_bank_code ?? '' }}</td>
                                        <td class="text-right">{{ $row->tax_exempt_amount }}</td>
                                        <td rowspan="2" class="text-right">{{ $tax_amount }}</td>
                                        <td rowspan="2" class="text-right">{{ $row->offset_amount }}</td>
                                        <td rowspan="2" class="text-right">{{ $row->transfer_fee ?? 0 }}</td>
                                        <td rowspan="2" class="text-right">{{ $row->bill_amount }}</td>
                                        <td rowspan="2" class="text-right">{{ $row->transfer_amount }}</td>
                                        <td rowspan="2" class="text-right">{{ $row->transfer_amount }}</td>
                                        <td rowspan="2">
                                            <a class="btn btn-primary" href="{{ route('monthly.paymentScheduleDetails') . '?supplier=' . $row->supplier_code . '&period=' . request()->date }}">詳細</a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right">{{ $row->taxable_amount }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center" colspan="12">検索結果はありません</td>
                                    </tr>
                                @endforelse
                                
                                </tbody>
                                @if($data != [])
                                <tfoot>
                                <tr>
                                    <td rowspan="2" colspan="4" class="text-left">合計</td>
                                    <td class="text-right">{{ $total_tax_exempt_amount }}</td>
                                    <td rowspan="2" class="text-right">{{ $total_tax_amount }}</td>
                                    <td rowspan="2" class="text-right">{{ $total_offset_amount }}</td>
                                    <td rowspan="2" class="text-right">{{ $total_transfer_fee }}</td>
                                    <td rowspan="2" class="text-right">{{ $total_bill_amount }}</td>
                                    <td rowspan="2" class="text-right">{{ $total_transfer_amount }}</td>
                                    <td rowspan="2" class="text-right">{{ $total_transfer_amount }}</td>
                                    <td rowspan="2"></td>
                                </tr>
                                <tr>
                                    <td class="text-right">{{ $total_taxable_amount }}</td>
                                </tr>
                                </tfoot>
                                @endif
                        </table>
                        @if($data != [])
                        {{ $data->links() }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-right">
                <div>
                    <a href="#" class="btn btn-primary"> メニューに戻る </a>
                    <a href="#" class="btn bg-success" > 通知書プレビュー </a>
                    <a href="#" class="btn bg-success" > 通知書直接印刷 </a>
                    <a href="#" class="btn bg-success" > 元帳プレビュー </a>
                    <a href="#" class="btn bg-success" > 元帳直接印刷 </a>
                </div>
            </div>
        </div>
    </div>
@endsection

