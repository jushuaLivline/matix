<table>
    <thead>
        <tr>
            <th align="center">入荷日</th>
            <th align="center">仕入先名</th>
            <th align="center">品番</th>
            <th align="center">品名</th>
            <th align="center">規格</th>
            <th align="center">数量</th>
            <th align="center">単価</th>
            <th align="center">購入区分</th>
            <th align="center">金額</th>
            <th align="center">伝票種類</th>
            <th align="center">伝票区分</th>
            <th align="center">伝票No</th>
            <th align="center">承認方法</th>
            <th align="center">依頼日</th>
            <th align="center">費目</th>
            <th align="center">依頼者</th>
            <th align="center">購買依頼No</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total_amount = 0;
        @endphp
        @forelse($datas as $data)
          @php
            $total_amount += $data->amount_of_money;
            $purchase_category = '';
            $slip_type = '';
            $voucher_class = '';
            $purchase_requisition_method = '';
            
            if ($data->purchase_category == 1) {
                $purchase_category = '生産品';
            } elseif ($data->purchase_category == 2) {
                $purchase_category = '購買品';
            }
            if ($data->slip_type == 1) {
                $slip_type = '納入伝票';
            } elseif ($data->slip_type == 2) {
                $slip_type = '外注加工伝票';
            } elseif ($data->slip_type == 3) {
                $slip_type = '購入材伝票';
            }
            if ($data->voucher_class == 1) {
                $voucher_class = '購入';
            } elseif ($data->voucher_class == 6) {
                $voucher_class = '返品';
            } elseif ($data->voucher_class == 9) {
                $voucher_class = '値引';
            }
            if ($data?->requisition?->approval_method_category == 1) {
                $purchase_requisition_method = 'システム';
            } elseif ($data?->requisition?->approval_method_category == 2) {
                $purchase_requisition_method = '依頼書';
            }
        @endphp
            <tr>
              <td class="tA-le">{{ $data->arrival_date->format("Y-m-d") }}</td>
              <td class="tA-le">{{ $data?->supplier?->customer_name }}</td>
              <td class="tA-le">{{ $data->part_number }}</td>
              <td class="tA-le">{{ $data->product_name }}</td>
              <td class="tA-ri">{{ $data->standard }}</td>
              <td class="tA-ri">{{ $data->quantity }}</td>
              <td class="tA-ri">{{ $data->unit_price }}</td>
              <td class="tA-cn">{{ $purchase_category }}</td>
              <td class="tA-ri">{{ $data->amount_of_money }}</td>
              <td class="tA-cn">{{ $slip_type }}</td>
              <td class="tA-cn">{{ $voucher_class }}</td>
              <td class="tA-ri">{{ $data->slip_no }}</td>
              <td class="tA-cn">{{ $purchase_requisition_method }}</td>
              <td class="tA-cn">{{ $data?->requisition?->requested_date->format("Y-m-d") }}</td>
              <td class="tA-le">{{ $data?->item?->item_name }}</td>
              <td class="tA-le">{{$data?->requisition?->employee?->employee_name}}</td>
              <td class="tA-le">{{ $data?->requisition?->requisition_number }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="17" class="text-center">検索結果はありません</td>
            </tr>
        @endforelse
        @if ($datas)
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="1">合計</td>
                <td  colspan="1" class="tA-ri">{{ $total_amount }}</td>
            </tr>
        @endif
    </tbody>                        
</table>