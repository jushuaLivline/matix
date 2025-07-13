@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/modals/index.css')
    <style>
        .calendar-plugin input {
            text-align: left;
            width: 6rem !important;
        }
        .btnExport {
            cursor: pointer;
        }
    </style>
@endpush

@section('title', '発注金額明細表発行')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>発注金額明細表発行</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>発注金額明細表発行</span></h1>
            </div>

            <form accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
            <div class="box mb-3">
                    <div class="mb-2 d-flex">
                        

                        <div class="mr-3">
                            <label class="form-label dotted indented">便No.</label>
                            <div class="d-flex">
                                <input type="text" name="supplier_code"
                                       id="supplier_code"
                                       class="text-left"
                                       style="margin-right: 10px"
                                       value="{{ request()->get('supplier_code') }}">
                                <input type="text" readonly
                                       name="supplier_name"
                                       id="supplier_name"
                                       style="margin-right: 10px"
                                       value="{{ request()->get('supplier_name') }}"
                                       class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">年月</label>
                            <div class="d-flex">
                                {{-- @include('partials._date_picker', ['inputName' => 'year_month']) --}}
                                <input type="text"
                                    name="year_month"
                                    style="margin-right: 10px"
                                    value="{{ request()->get('year_month') }}"
                                    class="middle-name text-left"
                                    placeholder="YYYYMM">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btnListContainer">
                    <div class="btnContainerMain justify-content-flex-end">
                        <div class="btnContainerMainRight">
                            <a class="btn btn-primary " href="{{ route('sales.export') }}" target="_blank">
                                発注金額明細表を印刷
                            </a>
                            <button type="submit" class="btn btn-primary">
                                発注金額明細表のプレビュー
                            </button>
                            <a href="{{ route('sales.export') }}" class="btn btn-success" target="_blank">
                                発注金額明細表をEXCEL出力
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            @if((request()->supplier_code ?? '') != '')
            <div class="tableWrap bordertable" style="clear: both;">
                <div class="mb-2">
                    n件中、n件～n件を表示してます
                    <table class="table table-bordered text-center table-striped-custom">
                        <thead>
                        <tr>
                            <th rowspan="2">品番</th>
                            <th rowspan="2">品名</th>
                            <th rowspan="2">発注</th>
                            <th rowspan="2">月間依頼数</th>
                            <th rowspan="2">単価</th>
                            <th rowspan="2">金額</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_amount = 0;
                                $total_qty = 0;
                            @endphp
                            @foreach($datas as $data)
                            @php
                                $total_amount += $data->salePlans()->sum('amount');
                                $total_qty += $data->salePlans()->sum('quantity');
                            @endphp
                            <tr>
                                <td class="tA-le">{{ $data->part_number }}</td>
                                <td class="tA-le">{{ $data->product_name }}</td>
                                <td class="tA-ri">かんばん</td>
                                <td class="tA-ri">{{ $data->salePlans()->sum('quantity') }}</td>
                                <td class="tA-ri">{{ $data->sale_plans?->amount }}</td>
                                <td class="tA-ri">{{ $data->salePlans()->sum('amount') }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td class="tA-le text-center" colspan="3">合計</td>
                                <td class="tA-le">{{ $total_qty }}</td>
                                <td class="tA-le"></td>
                                <td class="tA-le">{{ $total_amount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '直送先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
@endsection
