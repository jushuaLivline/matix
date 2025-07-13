<table class="tableBasic list-table">
    <thead>
        <tr>
            <th class="text-center">材料品番</th>
            <th class="text-center">グループ</th>
            <th class="text-center">計画</th>
            <th class="text-center">計画数</th>
            <th class="text-center">区分</th>
            @foreach($dates as $date)
                <th class="text-center" style="min-width: 10px; color: {{ $date['isWeekend'] ? 'red' : 'black' }}">
                    {{ $date['day'] }}
                </th>
            @endforeach
            <th class="text-center">翌月内示</th>
            <th class="text-center">翌々月内示</th>
        </tr>
    </thead>
    <tbody>
        @forelse($procurementPlanLists as $key => $result)
            @php
                $orders = $result->supplyMaterialOrders->keyBy(fn($order) => $order->instruction_date->format('Y-m-d'));
                $arrivals = $result->supplyMaterialArrivalManufacturers->keyBy(fn($arrival) => $arrival->arrival_day->format('Y-m-d'));
                $current_month = $result->current_month;
                $instruction_no = $result?->supplyMaterialOrders?->sum('instruction_number');
                
                if($result->current_month == 0){
                    $current_month = ($result->kanban_status == 'shiji') ? 0 : '';
                }
                if($instruction_no == 0){
                    $instruction_no = ($result->kanban_status == 'shiji') ? 0 : '';
                }
            @endphp

            {{-- Plan Row --}}
            <tr data-kanban-status="{{ $result->kanban_status }} " data-part-number=" {{ $result->part_number }}">
                <td class="text-left" rowspan="2">
                    @if($result->kanban_status == 'shiji')
                        <a href="{{ route('material.procurement.create', array_merge(['part_number' => $result->edited_part_number], Request::all())) }}">
                            {{ $result->edited_part_number }}
                        </a>
                    @else
                        {{ $result->edited_part_number }}
                    @endif

                    @if ($result->edited_part_number)
                    <br />{{ $result->product_name }}
                    @endif
                </td>
                <td class="text-center" rowspan="2">
                    {{ $result?->group?->supply_material_group ?? '__' }}
                </td>
                <td class="text-right" rowspan="2">
                    {{ $current_month }} 
                </td>
                <td class="text-right" rowspan="2">
                    {{$instruction_no}}
                </td>

                <td>計画</td>
                @php $counter = 0 @endphp
                @foreach($dates as $date)
                    <td class="text-right">
                        @if($result->kanban_status == 'shiji')
                            @php $counter++; @endphp
                            {{--  
                            {{ $orders[$date['date']]->instruction_number ?? '' }}
                            --}}
                            {{ $result['day_'.$counter] }}
                        @endif
                    </td>
                @endforeach
                <td class="text-right" rowspan="2">
                    {{ $result->next_month ?? 0}}
                </td>
                <td class="text-right" rowspan="2">
                    {{ $result->two_months_later ?? 0}}
                </td>
            </tr>

            {{-- Arrival Row --}}
            <tr>
                <td>入荷</td>
                @foreach($dates as $date)
                    <td class="text-right">
                        @if($result->kanban_status == 'shiji')
                            {{ $arrivals[$date['date']]->arrival_quantity ?? '' }}
                        @endif
                </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="{{ 7 + count($dates) }}" style="text-align:center;">検索結果はありません</td>
            </tr>
        @endforelse
    </tbody>
</table>