<table border="1">
    <thead>
    <tr>
        <th align="center">部門</th>
        <th align="center">発注先</th>
        <th align="center">数量</th>
        <th align="center">単位</th>
        <th align="center">依頼日</th>
        <th align="center" rowspan="2" valign="middle">購買依頼No.</th>
    </tr>
    <tr>
        <th align="center">ライン</th>
        <th align="center">品番・品名・規格</th>
        <th align="center">単価</th>
        <th align="center">金額</th>
        <th align="center">納期</th>
    </tr>
    </thead>
    <tbody>
        @forelse($purchaseRequisitions ?? [] as $purchaseRequisition)
        <tr>
            <td>{{ $purchaseRequisition->department->department_name ?? '' }}</td>
            <td>{{ $purchaseRequisition->supplier->supplier_name_abbreviation ?? '' }}</td>
            <td>{{ $purchaseRequisition->quantity ?? 0 }}</td>
            <td>{{ $purchaseRequisition->unit->name ?? '' }}</td>
            <td>{{ date('Y/m/d', strtotime($purchaseRequisition->requested_date)) }}</td>
            <td rowspan="2" valign="middle">{{ $purchaseRequisition->requisition_number }}</td>
        </tr>
        <tr>
            <td>{{ $purchaseRequisition->line->line_name ?? '' }}</td>
            <td>{{ $purchaseRequisition->product_name }}・{{ $purchaseRequisition->part_number }}・{{ $purchaseRequisition->standard }}</td>
            <td>{{ $purchaseRequisition->unit_price }}</td>
            <td>{{ $purchaseRequisition->amount_of_money }}</td>
            <td>{{ date('Y/m/d', strtotime($purchaseRequisition->deadline)) }}</td>
        </tr>
        @empty
            <tr>
                <td colspan="6" align="center">
                    検索結果はありません
                </td>
            </tr>
        @endforelse
        
    </tbody>
</table>