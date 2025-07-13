<table>
    <thead>
        <tr>
            <th align="center">承認方法</th>
            <th align="center">発注先</th>
            <th align="center">依頼者</th>
            <th align="center">数量</th>
            <th align="center">単位</th>
            <th align="center">依頼日</th>
            <th align="center">部門</th>
        </tr>
        <tr>
            <th align="center">状態</th>
            <th align="center">品番・品名・規格</th>
            <th align="center">購買依頼No.</th>
            <th align="center">単価</th>
            <th align="center">金額</th>
            <th align="center">納期</th>
            <th align="center">ライン</th>
        </tr>
    </thead>
    <tbody>
        @forelse($purchaseRequisitions as $purchaseRequisition)
            <tr>
                <td>
                    @if( $purchaseRequisition->approval_method_category == 1)
                    システム
                    @elseif( $purchaseRequisition->approval_method_category == 2)
                    依頼書
                    @endif
                </td>
                <td>{{ $purchaseRequisition->supplier_code }}</td>  
                <td>{{ $purchaseRequisition->creator }}</td>
                <td>{{ $purchaseRequisition->quantity }}</td>
                <td>{{ $purchaseRequisition->unit?->name }}</td>
                <td>{{ $purchaseRequisition->requested_date?->format('Y-m-d') }}</td>
                <td>{{ $purchaseRequisition->department_code }}</td>
            </tr>
            <tr>
                <td>
                    @if( $purchaseRequisition->state_classification == 0)
                        依頼中
                    @elseif( $purchaseRequisition->approval_method_category == 1)
                        承認中
                    @elseif( $purchaseRequisition->approval_method_category == 2)
                        承認済
                    @elseif( $purchaseRequisition->approval_method_category == 3)
                        発注済
                    @elseif( $purchaseRequisition->approval_method_category == 4)
                        入荷済
                    @elseif( $purchaseRequisition->approval_method_category == 9)
                        否認
                    @endif
                </td>
                <td>{{ $purchaseRequisition->part_number }}</td>
                <td>{{ $purchaseRequisition->requisition_number }}</td>
                <td>{{ $purchaseRequisition->unit_price }}</td>
                <td>{{ $purchaseRequisition->amount_of_money }}</td>
                <td>{{ $purchaseRequisition->deadline?->format('Y-m-d') }}</td>
                <td>{{ $purchaseRequisition->line_code }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" align="center">
                    検索結果はありません
                </td>
            </tr>
        @endforelse
    </tbody>
</table>