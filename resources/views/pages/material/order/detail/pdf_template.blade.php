<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Table 1</title>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Noto Sans JP', sans-serif;
            }
            table tbody td{
                padding-left: 5px;
                padding-right: 5px;
            }
            .page-break { page-break-before: always; }
            @page {
                margin: 5mm 5mm 5mm 5mm;
                /* Adjust margins as needed */
            }
        </style>
    </head>

    <body>
        {{-- Page 1 --}}
        <div class="container">
            <div style="margin-top: 40px;">
                <div style="float: right; line-height: 13pt; text-align: right;">
                    <span>No. <span>{{ now()->format('Ymd') }}</span></span><br>
                    <span>作成:<span>{{ now()->format('Y/m/d h:i') }}</span></span><br>
                    <span style="font-size: 15pt;">メイティックス株式会社</span>
                </div>
                <div style=" float: left; font-size: 20pt; text-align: right;">
                    <p> 外製 かんばん発注明細 </p>
                </div>
                <div style="clear:both"></div>
                @php 
                    $count = 0;
                    $x = 0;
                    $groupSizes = collect($orders)->map(fn($records) => count($records));
                    $threshold = intval($groupSizes->avg()); // Dynamically set threshold based on average
                @endphp
          
                @foreach ($orders as $groupKey => $records)
                @php
                        $firstRecord = $records->first(); // Get the first record for heading
                        $count += count($records);
                @endphp

                <table style="width: 100%;border-collapse: collapse;">
                   <thead>
                    <tr>
                        <td colspan="7">
                            <table style="width: 100%;border-collapse: collapse; border: 1px solid;">
                                <tr style="border: 1px solid;">
                                    <td rowspan="2" style="border: 1px solid;">仕入先</td>
                                    <td rowspan="2" style="border: 1px solid;">
                                        <p style="line-height: 10pt; margin:0; padding: 0;">{{ $firstRecord->material_manufacturer_code ?? null }}</p>
                                        <span style="line-height: 10pt; font-size: 15pt">{{ $firstRecord->customer_name ?? null }} </span>
                                        <span style="float: right;  vertical-align: bottom;">御中</span>
                                    </td>
                                    <td style="border: 1px solid; text-align: center;">指示日</td>
                                    <td style="text-align: center">便</td>
                                </tr>
                                <tr>
                                    <td style="border: 1px solid; text-align: center"><span>{{ $firstRecord->instruction_date?->format('Y/m/d') ?? "" }}</span></td>
                                    <td style="border: 1px solid; text-align: center"><span>{{ $firstRecord->instruction_no ?? '' }}</span></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr><td colspan="7">&nbsp;</td></tr>
                   <tr style="border: 1px solid;">
                        
                        <td style="border: 1px solid; text-align: center; width: 40px;">No.</td>
                        <td style="border: 1px solid; text-align: center; width: 120px;">発注No.</td>
                        <td style="border: 1px solid; text-align: center;">品番</td>
                        <td style="border: 1px solid; text-align: center;">背番</td>
                        <td style="border: 1px solid; text-align: center;">収容数</td>
                        <td style="border: 1px solid; text-align: center;">納入数</td>
                        <td style="border: 1px solid; text-align: center;">枚数</td>
                    </tr>
                   </thead>
                   <tbody>
                   @foreach ($records as $index => $order)
                    @php
                        $index++;
                    @endphp
                    <tr>
                        <td style="border: 1px solid; text-align: right;">{{ $index }}</td>
                        <td style="border: 1px solid; text-align: center">{{ $order->supply_material_order_no ?? "" }}</td>
                        <td style="border: 1px solid;">{{ $order->material_number ?? "" }}</td>
                        <td style="border: 1px solid; text-align: center;">{{ $order->uniform_number ?? "" }}</td>
                        <td style="border: 1px solid; text-align: right;">{{ $order->number_of_accomodated ?? "" }}</td>
                        <td style="border: 1px solid; text-align: right;">{{ $order->arrival_quantity ?? "" }}</td>
                        <td style="border: 1px solid; text-align: right;">{{ $order->instruction_kanban_quantity ?? "" }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                   
                </table>
                <div class="page-break"></div>
               

                @endforeach
            </div>
        </div>
    </body>

</html>