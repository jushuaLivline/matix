<style>
    table {
        font-family: 'Noto-noto sans jp';
        width: 100%;
        border-collapse: collapse;
        border: 1px solid black;
    }
    td { padding: 5px; border: 1px solid black; }

    .label,
    .bottom-label td{ text-align: center; }
    .font-11 { font-size: 11px; }
    .font-14 { font-size: 14px; }
    
    .bottom-boxes td{ width: 10%; height: 75px; }
    .title {
        font-family: "noto sans jp", sans-serif;
        font-weight: bold;
    }

</style>
<table>
    <tbody>
        <tr>
            <td class="title" colspan="5" style="font-size: 36px; border: 1px solid white; border-bottom: 1px solid black;">
                購 買 依 頼 書
            </td>
            <td colspan="5" style="text-align: right; vertical-align:bottom; border: 1px solid white; border-bottom: 1px solid black;">
                No. <?= $requisition['requisition']['requisition_number'] ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">依&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;頼&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;日</td>
            <td colspan="8">
                <?php
                    $date = new DateTime($requisition['requisition']['requested_date']);
                    echo $date->format('Y年n月j日');
                ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">部&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;署</td>
            <td colspan="3">
                <?= $requisition['department_name'] ?>
            </td>
            <td class="label" colspan="2">依&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;頼&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;者</td>
            <td colspan="3">
                <?= $requisition['employee_name'] ?>
            </td>
        </tr>

        <tr>
            <td class="label" colspan="2">発&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;注&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;先</td>
            <td colspan="8">
                <?= $requisition['supplier_name'] ?>
            </td>
        </tr>

        <tr>
            <td class="label" colspan="2">納&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;期</td>
            <td colspan="8">
                <?= isset($requisition['requisition']['deadline']) && $requisition['requisition']['deadline'] 
                    ? (new DateTime($requisition['requisition']['deadline']))->format('Y年n月j日') 
                    : ''; 
                ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">ラ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;イ&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ン</td>
            <td colspan="8">
                <?= $requisition['line_name'] ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">品&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;番</td>
            <td colspan="8">
                <?= $requisition['requisition']['part_number'] ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">品&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;名</td>
            <td colspan="8">
                <?= $requisition['requisition']['product_name'] ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">規&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;格</td>
            <td colspan="8">
                <?= $requisition['requisition']['standard'] ?>
            </td>
        </tr>

        <tr>
            <td class="label" colspan="2">数&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;量</td>
            <td colspan="3" style="text-align: right">
                <?= $requisition['requisition']['quantity'] ?>
            </td>
            <td class="label" colspan="2">単&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;位</td>
            <td colspan="3">
                <?= $requisition['code_name'] ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">単&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;価</td>
            <td colspan="3" style="text-align: right; border-right: 1px solid white;">
                <?= number_format($requisition['requisition']['unit_price']) ?> 円
            </td>
            <td colspan="5" style="border-left: 1px solid white;"></td>
        </tr>
        <tr>
            <td class="label" colspan="2">金&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;額</td>
            <td colspan="3" style="text-align: right; border-right: 1px solid white;">
                <?= number_format($requisition['requisition']['amount_of_money']) ?> 円
            </td>
            <td colspan="5" style="border-left: 1px solid white;"></td>
        </tr>

        <tr>
            <td class="label" colspan="2">購&nbsp;&nbsp;入&nbsp;&nbsp;理&nbsp;&nbsp;由</td>
            <td colspan="8">
                <?= $requisition['requisition']['reason'] ?>
            </td>
        </tr>

        <!-- Expense Type -->
        <tr>
            <td class="label" colspan="2">費&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;目</td>
            <td colspan="3">
                <?= $requisition['requisition']['expense_items'] ?> &nbsp;
                <?= $requisition['item_name'] ?>
            </td>
            <td class="label" colspan="2">補&nbsp;&nbsp;助&nbsp;&nbsp;費&nbsp;&nbsp;目</td>
            <td colspan="3"></td>
        </tr>

        <!-- Quotation & Notes -->
        <tr>
            <td class="label" colspan="2">見&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;積&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;書</td>
            <td colspan="8">
                <?= $requisition['requisition']['quotation_existence_flag'] == 1 ? "有" : "無" ?>
            </td>
        </tr>
        <tr>
            <td class="label" colspan="2">備&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;考</td>
            <td colspan="8">
                <?= $requisition['requisition']['remarks'] ?>
            </td>
        </tr>

        <!-- Final Negotiation Section -->
        <tr>
            <td class="label font-14" colspan="2">相見積しましたか</td>
            <td colspan="3"></td>
            <td class="label font-14" colspan="2">値下げ交渉しましたか</td>
            <td colspan="3"></td>
        </tr>
        <tr><td colspan="10" style="border: 1px solid white;"> &nbsp;</td></tr>
        <tr><td colspan="10" style="border: 1px solid white; border-bottom: 1px solid black;"> &nbsp;</td></tr>
        <tr class="bottom-label" style="border-top: 1px solid black;">
            <td>社長</td>
            <td>役員</td>
            <td>役員</td>
            <td class="font-11">経営企画</td>
            <td>部長</td>
            <td class="font-11">GM・主査</td>
            <td class="font-11">課長・主担当</td>
            <td>係長</td>
            <td class="font-11">職長・主任</td>
            <td>購買</td>
        </tr>
        <tr class="bottom-boxes">
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="10" style="border: 1px solid white; text-align:right;">
                メイティックス株式会社
            </td>
        </tr>
    </tbody>
</table>
