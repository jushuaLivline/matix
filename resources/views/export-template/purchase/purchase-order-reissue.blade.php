<table style="font-family: 'Noto Sans JP', 'Arial', sans-serif; width: 100%; border-collapse: 
        collapse; vertical-align: center;">
        <tbody>
            <tr>
                <td style="width: <?php echo $cellSize['normal']?>;vertical-align:center"></td>
                <td style="width: <?php echo $cellSize['large']?>;"></td>
                <td style="width: <?php echo $cellSize['xlarge']?>;"></td>
                <td style="width: <?php echo $cellSize['small']?>;"></td>
                <td style="width: <?php echo $cellSize['xs']?>;"></td>
                <td style="width: <?php echo $cellSize['normal']?>;"></td>
                <td style="width: <?php echo $cellSize['normal']?>;"></td>
                <td style="width: <?php echo $cellSize['large']?>;"></td>
                <td style="width: <?php echo $cellSize['normal']?>;"></td>
                <td style="width: <?php echo $cellSize['xlarge']?>;"></td>
            </tr>
    
            <tr class="txtbold" style="margin:0px">
                <td></td>
                <td></td>
                <td></td>
                <td colspan="4" rowspan="2" style="font-size: 16pt; text-align:center;padding:0px; vertical-align:center">注　文　書</td>
                <td></td>
                <td style="font-size: 11pt; text-align:right;margin:0px;padding:0px; vertical-align:center" colspan="2">No. <?php echo $purchaseOrderItem->purchase_order_number?></td>
            </tr>
    
            <tr class="txtbold" style="margin:0px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right; font-size:11pt; margin:0px;padding:0px; vertical-align:center" colspan="2">発行日　<?php echo date('Y')?>年<?php echo date('m')?>月<?php echo date('d')?>日</td>
            </tr>
            <tr>
                <td colspan="10" <?php echo $exportType == 'pdf' ? 'style="height:20px"' : ''?>></td>
            </tr>
    
            <tr class="txtbold" style="margin:0px">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="5"></td>
                <td colspan="3" style="font-size: 14pt; text-align: left">メイティックス 株式会社 &nbsp;<label style="font-size: 12pt; padding:0px; vertical-align: middle">総務課</label></td>
                {{-- <td style="text-align: left; font-size: 12pt;padding:0px; vertical-align:center">総務課</td> --}}
            </tr>
            <tr class="txtbold">
                <td colspan="3" style="font-size: 11pt; text-align:left; border-bottom: 1px solid #000; vertical-align:center"><?php echo $purchaseOrderItem->supplier->customer_name?></td>
                <td style="font-size: 11pt; border-bottom: 1px solid #000; vertical-align:center">御中</td>
                <td></td>
                <td></td>
                <td colspan="4" style="font-size: 11pt;text-align:right; vertical-align:center">〒 444-0397 &nbsp;&nbsp; 愛知県西尾市中畑町安左東18番地</td>
                </tr>
    
                <tr class="txtbold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td colspan="4" style="text-align:right;font-size: 11pt; vertical-align:center">TEL 0563-59-4713 &nbsp;&nbsp; FAX 0563-59-1390</td>
                </tr>
            </tbody>

        <tr>
            <td colspan="10"></td>
        </tr>
        <tr class="txtbold text-center">
            <td style="border:1px solid #000; text-align:center;vertical-align:center">購買依頼No</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center">ライン</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center;">品番・品名・規格</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center;">数量</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center;">単位</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center">単価</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center">金額</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center">依頼者</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center">納期</td>
            <td style="border:1px solid #000; text-align:center;vertical-align:center">備考</td>
        </tr>

        @forelse($purchaseOrderItemDetails as $data)
        <?php
            $formatCellPartNumber = !empty($data->part_number) ? htmlspecialchars($data->part_number) ?? '': '';
            $formatCellPartNumber .= !empty($data->product_name) ? htmlspecialchars($data->product_name) ?? '': '';
            $formatCellPartNumber .= !empty($data->standard) ? htmlspecialchars($data->standard): '' ?? '';
        ?>
        <tr class="text-center">
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;"><?php echo $data->requisition_number?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;"><?php echo htmlspecialchars($data->line->line_name  ?? '')?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle; word-wrap:break-word"><?php echo $formatCellPartNumber?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;"><?php echo $data->quantity?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;"><?php echo htmlspecialchars($data->unit->name ?? '')?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;"><?php echo number_format($data->unit_price)?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;"><?php echo number_format($data->amount_of_money)?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;">
                <?php if (!empty($data->department?->department_name)): ?>
                    <span><?php echo htmlspecialchars($data->department->department_name); ?></span><br>
                <?php endif; ?>

                <?php if (!empty($data->employee?->employee_name)): ?>
                    <span><?php echo htmlspecialchars($data->employee->employee_name); ?></span>
                <?php endif; ?>
            </td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle;"><?php echo date('Y/m/d')?></td>
            <td style="border:1px solid #000; text-align:center; vertical-align: middle; word-wrap:break-word"><?php echo htmlspecialchars($data->remarks)?></td>
        </tr>

        @empty
            <tr>
                <td colspan="7" align="center">
                    検索結果はありません
                </td>
            </tr>
        @endforelse
    </table>