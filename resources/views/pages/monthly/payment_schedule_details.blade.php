@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    <style>
        td {
            line-height: 38px;
        }
    </style>
@endpush

@section('title', '支払予定詳細')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                支払予定詳細
            </div>
            <div class="section">
                <h1 class="form-label bar indented">詳細表示</h1>
                <div class="box">
                    <div class="mb-2">
                        @if(($data != []))
                            {{ $data->total() }}件中、{{ $data->firstItem() }}件～{{ $data->lastItem() }} 件を表示しています
                        @endif
                        <table class="table table-bordered text-center table-striped-custom">
                            <thead>
                            <tr>
                                <th>入荷日</th>
                                <th>伝票No.</th>
                                <th>品番</th>
                                <th>品名</th>
                                <th>数量</th>
                                <th>単価</th>
                                <th>金額</th>
                                <th>費目</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($data as $row)
                                <tr>
                                    <td>{{ date('Y/m/d', strtotime($row->arrival_day)) }}</td>
                                    <td class="tA-ri">{{ $row->slip_no }}</td>
                                    <td class="tA-ri">{{ $row->part_no }}</td>
                                    <td class="tA-le">{{ $row->product_name }}</td>
                                    <td class="tA-ri">{{ $row->quantity }}</td>
                                    <td class="tA-ri">{{ $row->unit_price }}</td>
                                    <td class="tA-ri">{{ $row->transfer_amount }}</td>
                                    <td class="tA-le">{{ $row->expense_item->item_name ?? '' }}</td>
                                    <td>
                                        <a href="{{ route('monthly.paymentTermsChange', [$row->id]) }}" class="btn btn-primary">変更</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="9">検索結果はありません</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        @if($data != [])
                            {{ $data->links() }}
                        @endif
                    </div>
                </div>
                <div class="text-right mt-3">
                        <a href="#" class="btn btn-success btn-wide" style="width: 250px"> EXCEL出力 </a>
                </div>
                <!-- @include('partials._pagination') -->
            </div>
        </div>
    </div>
@endsection

