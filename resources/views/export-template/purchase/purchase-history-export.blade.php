<table>
    <thead>
        <tr>
            <th align="center">入荷日</th>
            <th align="center">仕入先名</th>
            <th align="center">製品品番</th>
            <th align="center">品名</th>
            <th align="center">数量</th>
            <th align="center">単位</th>
            <th align="center">単価</th>
            <th align="center">金額</th>
            <th align="center">伝票種類</th>
            <th align="center">伝票No.</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_amount = 0;
            $slip_type = '';
        @endphp
        @forelse($datas as $data)
            @php 
                $total_amount += $data->sum_amount_of_money;
                if ($data->slip_type == 1) {
                    $slip_type = '納入伝票';
                } elseif ($data->slip_type == 2) {
                    $slip_type = '外注加工伝票';
                } elseif ($data->slip_type == 3) {
                    $slip_type = '購入材伝票';
                }
            @endphp
            <tr>
                <td>{{ $data->arrival_date?->format('Y-m-d') }}</td>
                <td>{{ $data->supplier?->supplier_name_abbreviation }}</td>
                <td>{{ $data->part_number }}</td>
                <td>{{ $data->product_name }}</td>
                <td align="left">{{ $data->quantity }}</td>
                <td align="left">{{ $data->unit_code }}</td>
                <td align="left">{{ $data->unit_price }}</td>
                <td>{{ number_format($data->amount_of_money, 0, '.', ',')  }}</td>
                <td>{{ $slip_type }}</td>
                <td>{{ $data->slip_no }}</td>
                @php
                    $redirection = route('purchase.purchaseAmountDetail') . '?supplier=' . $data->supplier?->customer_code 
                                . '&arrival_date_start=' . (request()->arrival_date_start ?? '') 
                                . '&arrival_date_end=' . (request()->arrival_date_end ?? '') 
                                . '&purchase_category=' . (request()->purchase_category ?? 0);
                @endphp
            </tr>
        @empty
            <tr>
                <td colspan="11" class="text-center" align="center">検索結果はありません</td>
            </tr>
        @endforelse
        @if ($datas && count($datas) > 0)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>合計</td>
                <td>{{ $total_amount }}</td>
            </tr>
        @endif
    </tbody>                        
</table>