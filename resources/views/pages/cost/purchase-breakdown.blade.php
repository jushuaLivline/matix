@extends('layouts.app')

@push('styles')
    @vite('resources/css/estimates/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/master/product.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '原価表')
@section('content')
<style>
    .required {
        border: 1px transparent;
        padding: 4px 15px;
        background-color: #ed7d32;
        color: white;
    }
</style>
<div class="content">
    <div class="contentInner">
        <div class="accordion">
            <h1><span>原価表</span></h1>
        </div>

        <div class="pagettlWrap">
            <h1><span>検索</span></h1>
        </div>

        <form action="{{ route('cost.purchaseBreakdownSearch')  }}" method="GET" accept-charset="utf-8" id="createReqFrm" class="overlayedSubmitForm">
            <div class="tableWrap borderLesstable inputFormArea">
                <div class="row-content">
                    <div class="flex-row">
                        <label for="year_month" class="label_for" style="padding-bottom: 5px;">
                            年月
                            <span class="required">必須</span>
                        </label>

                        <input type="text" style="width: 150px;" id="year_month" name="year_month" placeholder="YYYYMM" value="{{ Request::get('year_month') }}">
                        <div class="error_msg"></div>
                    </div>
                </div>
                <div class="text-center sc relative">
                    <a href="#" id="export_csv" class="btn btn-green absolute-right">検索結果をEXCEL出力</a>
                    @php
                        $currentYear = now()->year;
                        $currentMonth = now()->format('m');
                    @endphp
                    <a href="{{ route("cost.purchaseBreakdown") }}?year_month={{ $currentYear.$currentMonth }}"
                        class="btn btn-blue js-btn-reset-reload" id="search">検索条件をクリア</a>
                    <button type="submit" class="btn btn-blue" id="search">検索</button>
                        {{-- <a href="{{ route("estimate.index") }}"
                        class="buttonBasic btn-reset bColor-ok js-btn-reset-reload" style="width: 250px!important; background-color: green;">検索結果をEXCEL出力 --}}
                    </div>
                </div>
            </div>
        </form>

        <div class="pagettlWrap">
            <h1><span>検索結果</span></h1>
        </div>
        <div class="tableWrap bordertable" style="clear: both;">
            <ul class="headerList">
                @if (count($breakdown_datas) > 0)
                    <li>{{ $count }}件中、{{ $breakdown_datas->firstItem() }}件～{{ $breakdown_datas->lastItem() }} 件を表示してます</li>
                @else
                    <li></li>
                @endif
                <li></li>
            </ul>
            <table class="tableBasic list-table">
                <thead>
                    <tr>
                        <th>費目</th>
                        <th>費目名</th>
                        <th>勘定科目</th>
                        <th>勘定科目名</th>
                        <th>補助科目</th>
                        <th>補助科目名</th>
                        <th>金額</th>
                    </tr>
                </thead>
                <tbody>
                    @if (count($breakdown_datas) <= 0)
                        @include('partials._no_record', ['colspan' => 8])
                    @else
                        @foreach ($breakdown_datas as $data)
                            <tr>
                                <td class="tA-le" style="text-align: center;">{{ $data->expense_item }}</td>
                                <td class="tA-le" style="text-align: left;">{{ $data->item?->item_name }}</td>
                                <td class="tA-le" style="text-align: center;">{{ $data->item?->acount }}</td>
                                <td class="tA-le" style="text-align: left;">{{ $data->item?->acount_name }}</td>
                                <td class="tA-le" style="text-align: center;">{{ $data->item?->supplementary_subjects }}</td>
                                <td class="tA-le" style="text-align: left;">{{ $data->item?->auxiliary_course_name }}</td>
                                <td class="tA-le" style="text-align: right;">
                                    <a href="{{ route('cost.purchaseData', ['year_month' => Request::get('year_month'), 'expense_item' => $data->expense_item, 'item_name' => $data->item?->item_name]) }}">
                                        {{ number_format($data->amount, 2) }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="border-bottom: none;"></td>
                        <td style="background-color: #d9e1f2; text-align: center;">合計</td>
                        <td style="background-color: #d9e1f2; text-align: right;">{{ number_format($sum, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            @if (count($breakdown_datas) > 0)
                {{ $breakdown_datas->links() }}
            @endif
        </div>
    </div>
</div>
@endsection
@push('scripts')
    @vite(['resources/js/cost/data-form.js'])
@endpush