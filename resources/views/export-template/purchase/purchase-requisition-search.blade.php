<table>
    <thead>
    <tr>
        <td>状態</td>
        <td>承認方法</td>
        <td>次承認者</td>
        <td>部門</td>
        <td>ライン</td>
        <td>発注先</td>
        <td>品番・品名・規格</td>
        <td>数量</td>
        <td>単価</td>
        <td>金額</td>
        <td>依頼日</td>
        <td>納期</td>
        <td>購買依頼No.</td>
    </tr>
    </thead>
    <tbody>
        @foreach ($purchaseRequisitions as $purchaseRequisition)
        <tr>
            <td class="tA-le text-center" style="width:80px;">
                @switch($purchaseRequisition->state_classification)
                    @case(0)
                        依頼中
                        @break
                    @case(1)
                        承認中
                        @break
                    @case(2)
                        承認済
                        @break
                    @case(3)
                        発注済
                        @break
                    @case(4)
                        入荷済
                        @break
                    @case(9)
                        否認
                        @break
                @endswitch
            </td>                                
            <td class="tA-le text-center" style="width:80px;">
                @switch($purchaseRequisition->approval_method_category)
                    @case(1)
                        システム
                        @break
                    @case(2)
                        依頼書
                        @break
                @endswitch
            </td>
            <td class="tA-le text-center" style="width:90px;">{{ $purchaseRequisition->nextApprover ? $purchaseRequisition->nextApprover->employee_name : "（該当無し）" }}</td>
            <td class="tA-le">{{ $purchaseRequisition->department_code.''.$purchaseRequisition->department?->name }}</td>
            <td class="tA-le">{{ $purchaseRequisition->line?->line_name }}</td>
            <td class="tA-le">{{ $purchaseRequisition->supplier?->supplier_name_abbreviation }}</td>
            <td class="tA-le">{{ $purchaseRequisition->part_number.'・'. $purchaseRequisition->product_name.'・'.$purchaseRequisition->standard }}</td>
            <td class="tA-le" >="{{ number_format($purchaseRequisition->quantity) }}"</td>
            <td class="tA-le">="{{ number_format($purchaseRequisition->unit_price) }}"</td>
            <td class="tA-le">="{{ $purchaseRequisition->unit_code }}"</td>
            <td class="tA-le text-end">="{{ number_format($purchaseRequisition->amount_of_money) }}"</td>
            <td class="tA-le" style="width:85px;">{{ $purchaseRequisition->requested_date?->format('Y-m-d') }}</td>
            <td class="tA-le" style="width:85px;">{{ $purchaseRequisition->deadline?->format('Y-m-d') }}</td>
            <td class="tA-le">{{ $purchaseRequisition->requisition_number }}</td>                                                          
        </tr>
        @endforeach
    </tbody>
</table>