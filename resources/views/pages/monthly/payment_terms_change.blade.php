@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    <style>
        table td {
            vertical-align: middle !important;
        }

        a {
            text-decoration: none;
        }
    </style>
@endpush

@section('title', '支払条件変更')
@section('content')
    <div class="content">
        <div class="contentInner">
            <form action="{{ route('monthly.paymentTermsChangeProcess', ['paymentDetailId' => $data->id]) }}" method="post">
                @csrf
            <div class="pageHeaderBox rounded">
                支払条件変更
            </div>

            <div class="section">
                <h1 class="form-label bar indented">支払条件変更</h1>
                <div class="box mb-1">
                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">入荷日</label>
                            <div class="d-flex">
                                <input type="text" disabled style="width: 150px" value="{{ date('Y/m/d', strtotime($data->arrival_day)) }}">
                            </div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">支払先名</label>
                            <div class="d-flex">
                                <input type="text" disabled value="{{ $data->supplier->customer_name ?? '' }}">
                            </div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">伝票No.</label>
                            <div class="d-flex">
                                <input type="text" disabled value="{{ $data->slip_no }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">品番</label>
                            <div class="d-flex">
                                <input type="text" disabled style="width: 35rem;" value="{{ $data->part_no }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">
                                <input type="text" disabled style="width: 35rem;" value="{{ $data->product_name }}">
                            </div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">費目名</label>
                            <div class="d-flex">
                                <input type="text" disabled value="{{ $data->expense_item->item_name ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">単価</label>
                            <div class="d-flex">
                                <input type="text" disabled class="numberCharacter" value="{{ $data->unit_price }}" style="width: 150px;">
                            </div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">数量</label>
                            <div class="d-flex">
                                <input type="text" disabled class="numberCharacter" value="{{ $data->quantity }}" style="width: 150px;">
                            </div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">支払金額</label>
                            <div class="d-flex">
                                <input type="text" disabled class="numberCharacter" value="{{ $data->payment }}" style="width: 150px;">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">振込金額</label>
                            <div class="d-flex">
                                <input type="text" disabled value="{{ $data->transfer_amount }}" class="numberCharacter">
                                <span style="font-size:24px; padding:1px 10px;">
                                    →
                                </span>
                                <input type="text" name="transfer_amount_edit" class="numberCharacter">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">手形金額</label>
                            <div class="d-flex">
                                <input type="text" disabled value="{{ $data->bill_amount }}" class="numberCharacter">
                                <span style="font-size:24px; padding:1px 10px;">
                                    →
                                </span>
                                <input type="text" name="bill_amount_edit" class="numberCharacter">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">課税区分</label>
                            <div>
                                <input type="radio" name="tax_classification" value="2" class="option-radio" {{ $data->tax_classification == 2 ? 'checked' : '' }}/>
                                課税
                                <input type="radio" name="tax_classification" value="1" class="option-radio" {{ $data->tax_classification == 1 ? 'checked' : '' }}/>
                                非課税
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-right">
                <div>
                    <a href="{{ route('monthly.paymentScheduleDetails') . '?supplier=' . $data->supplier_code . '&period=' . $date }}" class="btn btn-primary btn-wide"> キャンセル </a>
                    <button type="submit" class="btn btn-success btn-wide"> 確定 </button>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

