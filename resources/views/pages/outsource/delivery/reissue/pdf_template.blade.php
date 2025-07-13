<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>外注加工納品書_受領書再発行</title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Noto Sans JP', sans-serif;
    }
    @page {
        margin: 5mm 5mm 5mm 5mm; /* Adjust margins as needed */
    }
</style>

</head>
<body>
    {{-- page 2 --}}
    <div class="container">
        <div style="margin-top: 20px;">
            <div style="float: right;">
                <span>発行日：<span style="color: rgb(32, 107, 221);">{{ $order->instruction_date ?? '' }}</span></span>
            </div>
        </div>
        <br><br>
        <div style=" float: left;">
            <p>外製 納品書</p>
        </div>
        <div style="float: right;">
            <table style="border-collapse: collapse; border: 1px solid;">
                <tr>
                    <td colspan="2" style="width: 100px;">
                        納入者
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px;">
                        <span style="color: rgb(32, 107, 221);">{{ $order->supplier_code ?? null }}</span>
                    </td>
                    <td style="width: 250px;">
                        <span style="color: rgb(32, 107, 221);">
                            村松鉄工（株）				
                        </span>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <br>
        <br>
        <div>
            <table style="width: 100%;border-collapse: collapse; border: 1px solid;">
                <tr style="border: 1px solid;">
                    <td style="border: 1px solid;">発注No.</td>
                    <td style="border: 1px solid; text-align: center;">品番</td>
                    <td style="border: 1px solid; text-align: center;">納期</td>
                    <td style="border: 1px solid; text-align: center;">発注数</td>
                    <td style="border: 1px solid; text-align: center;">収容数</td>
                    <td style="border: 1px solid; text-align: center;">枚数</td>
                </tr>
                <tr>
                    <td style="border: 1px solid;">{{ $order->order_no ?? ''}}</td>
                    <td style="border: 1px solid;">{{ $order->product_code ?? ''}}</td>
                    <td style="border: 1px solid;">{{ $order->instruction_date ?? '' }}</td>
                    <td style="border: 1px solid;">{{ $order->instruction_number  ?? ''}}</td>
                    <td style="border: 1px solid;">{{ $order->kanbanMaster?->number_of_accomodated ?? null }}</td>
                    <td style="border: 1px solid;">{{ $order->arrival_quantity ?? '' }}</td>
                </tr>
            </table>
        </div>
        <br>
        <div>
            <table style="border-collapse: collapse; border: 1px solid;">
                <tr style="border: 1px solid;">
                    <td rowspan="2" style="border: 1px solid;">
                        備<br>
                        考
                    </td>
                    <td rowspan="2" style="border: 1px solid;width: 210px;">
                        
                    </td>
                    <td rowspan="2" style="border-top: 1px solid #ffffff;border-bottom: 1px solid #ffffff; width: 20px;">
                        
                    </td>
                    <td style="border: 1px solid;">
                        分納日
                    </td>
                    <td style="border: 1px solid;width: 100px;">
                        
                    </td>
                    <td rowspan="2" style="border-top: 1px solid #ffffff;border-bottom: 1px solid #ffffff;width: 20px;">
                        
                    </td>
                    <td style="border: 1px solid;width: 100px;">
                        受入部署
                    </td>
                </tr>
                <tr style="border: 1px solid;">
                    <td style="border: 1px solid;">
                        分納数
                    </td>
                    <td style="border: 1px solid #000000;">
                        
                    </td>
                    <td rowspan="2" style="border: 1px solid #000000;">
                    </td>
                </tr>
                <tr style="border: 1px solid;">
                    <td colspan="4" style="border-left: 1px solid #ffffff;border-right: 1px solid #ffffff;border-bottom: 1px solid #ffffff; height: 20px;">
                    </td>
                    <td style="border: 1px solid; border-right: 1px solid #ffffff;">
                    </td>
                    <td style="border-bottom: 1px solid #ffffff; height: 20px;">
                    </td>
                </tr>
                <tr style="border: 1px solid;">
                    <td colspan="4" style="border-left: 1px solid #ffffff;border-top: 1px solid #ffffff;border-bottom: 1px solid #ffffff;text-align: center;">
                        <span style="color: rgb(32, 107, 221);">(BARCODE)</span>	
                    </td>
                    <td style="border: 1px solid #000000; text-align: center;">
                        受領日付印
                    </td>
                    <td style="border-bottom: 1px solid #ffffff; height: 20px;">
                    </td>
                    <td rowspan="2" style="border: 1px solid;">
                    </td>
                </tr>
                <tr style="border: 1px solid;">
                    <td colspan="4" style="border-left: 1px solid #ffffff;border-top: 1px solid #ffffff;border-bottom: 1px solid #ffffff;text-align: center;">
                        <span style="color: rgb(32, 107, 221);">{{ $order->management_no ?? '' }}</span>	
                    </td>
                    <td style="border: 1px solid #000000; text-align: center; height: 50px;">
                    </td>
                    <td style="border-bottom: 1px solid #ffffff; height: 20px;">
                    </td>
                </tr>
            </table>
            <br>
            <table>
                <tr style="border: 1px solid;">
                    <td colspan="4" style="border: 1px solid #000000;padding: 10px;">
                        <span style="color: rgb(221, 32, 32);">
                            分納の場合 <br>
                            納入日・分納数を赤で記入してください
                        </span>	
                    </td>
                </tr>
            </table>
            <hr>
            <div style="margin-top: 20px;">
                <div style="float: right;">
                    <span>発行日：<span style="color: rgb(32, 107, 221);">{{ now()->format('Ymd') }}</span></span>
                    <p>メイティックス株式会社</p>
                </div>
            <div style=" float: left;">
                <p>外製 納品書</p>
            </div>
            <br>
            <br>
            <br>
            <div>
                <table style="border-collapse: collapse; border-bottom: 1px solid;">
                    <tr>
                        <td colspan="2" style="width: 50px;"></td>
                        <td colspan="3" style="width: 280px;">
                            <span style="color: rgb(32, 107, 221);">
                                {{ $order->supplier_code ?? '' }}				
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span style="color: rgb(4, 4, 4);">納入者</span>
                        </td>
                        <td colspan="3" style="width: 280px;">
                            <span style="color: rgb(32, 107, 221);">
                                村松鉄工（株）				
                            </span>
                        </td>
                        <td>
                            御中
                        </td>
                    </tr>
                </table>
                <br>
                <table style="border-collapse: collapse; border: 1px solid;">
                    <tr style="border: 1px solid;">
                        <td style="border: 1px solid;width: 120px;">発注No.	</td>
                        <td style="border: 1px solid;width: 120px;">品番</td>
                        <td style="border: 1px solid;width: 120px;">納期</td>
                    </tr>
                    <tr style="border: 1px solid;">
                        <td style="border: 1px solid;">{{ $order->order_no ?? '' }}</td>
                        <td style="border: 1px solid;">{{ $order->product_code ?? '' }}</td>
                        <td style="border: 1px solid;">{{ $order->instruction_date ?? '' }}</td>
                    </tr>
                </table>
                <br>
                <table style="border-collapse: collapse; border: 1px solid;">
                    <tr style="border: 1px solid;">
                        <td style="border: 1px solid;width: 80px;">
                            納入日	
                        </td>
                        <td style="border: 1px solid;width: 180px;">
                            {{ $order->instruction_date ?? null}}
                        </td>
                    </tr>
                    <tr style="border: 1px solid;">
                        <td style="border: 1px solid;">納入数</td>
                        <td style="border: 1px solid;">{{ $order->arrival_quantity ?? null }}</td>
                    </tr>
                </table>
                <br>
                <table>
                    <tr style="border: 1px solid;">
                        <td rowspan="2" style="border: 1px solid; width:20px; text-align: center;">
                            備<br>
                            考
                        </td>
                        <td rowspan="2" style="border: 1px solid;width: 210px;">
                            
                        </td>
                    </tr>
                </table>
                <br>
                <table>
                    <tr>
                        <td style="border: 1px solid;width: 400px;color:rgb(221, 32, 32); padding: 10px;">
                            分納の場合 <br>
                            納入日・分納数を（控として）記入しておいてください
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>