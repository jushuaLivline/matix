<table>
    <thead>
        <tr>
            <th align="center">仕入先コード</th>
            <th align="center">仕入先名</th>
            <th align="center">件数</th>
            <th align="center">金額</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_amount = 0;
        @endphp
        @forelse($datas as $data)
            @php 
                $total_amount += $data->sum_amount_of_money;
            @endphp
            <tr>
                <td>{{ $data->supplier->customer_code }}</td>
                <td>{{ $data->supplier->customer_name }}</td>
                <td>{{ $data->count_of_records }}</td>
                <td>{{ $data->sum_amount_of_money }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">検索結果はありません</td>
            </tr>
        @endforelse
        @if ($datas && count($datas) > 0)
            <tr>
                <td></td>
                <td></td>
                <td>合計</td>
                <td>{{ $total_amount }}</td>
            </tr>
        @endif
    </tbody>                        
</table>