@extends('layouts.app')

@push('styles')
    @vite('resources/css/order/style.css')
@endpush

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('#submit-form').on('click', function() {
            let text = "「ファイルで取込をした内示情報を登録します、よろしいでしょうか？」";
            if (confirm(text) == true) {
                $(this).attr('disabled', true);
                $(this).removeClass('btn-green');
                $(this).addClass('btn-disabled');
                console.log('button clicked');
                $('#uploading-form').submit();
            }
        });
        $('#uploading-form').on('submit', function(e) {
            e.preventDefault();
            console.log('form loading');
            $.ajax({
                url: $(this).attr('action'),
                type: "post",
                data: [],
                success: function (response) {
                    $('#submit-form').attr('disabled', false);
                    $('#submit-form').addClass('btn-green');
                    $('#submit-form').removeClass('btn-disabled');
                    alert('「データは正常に登録されました」');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                    $('#submit-form').attr('disabled', false);
                    $('#submit-form').addClass('btn-green');
                    $('#submit-form').removeClass('btn-disabled');
                }
            });
        });
    </script>
@endpush

@section('title', '内示データ取込内容確認')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                内示データ取込内容確認
            </div>
            <form action="{{ route('order.process.data.import.content.confirmation', [$input_id]) }}"  class="overlayedSubmitForm" method="post" id="uploading-form">

            </form>
            <div class="section">
                <h1 class="form-label bar indented">取込内容</h1>
                <div class="box">
                    <div class="ml-1">
                        <div class="d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">納入先</label>
                                <div>
                                    <input type="text" disabled value="ZZZZZZZZZZZZZZZZZZ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="section">
                        <div class="box">
                            n件の内示データ取込内容を表示してます
                            <table class="table table-bordered table-striped" style="width: 71%">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="valign-center" style="width: 20%">製品品番</th>
                                        <th rowspan="3" class="valign-center" style="width: 20%">品名</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">1</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">2</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">3</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">4</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">5</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">6</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">7</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">8</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">9</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">10</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">12</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">13</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">14</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">15</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">16</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">17</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">18</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">19</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">20</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">21</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">22</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">23</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">24</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">25</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">26</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">27</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">28</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">29</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">30</th>
                                        <th rowspan="1" class="valign-center" style="width: 1%">31</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($instructions as $instruction)
                                <tr>
                                    <td class="valign-middle">{{ $instruction->product_id }}</td>
                                    <td class="valign-middle">123456-1品名</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_1 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_2 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_3 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_4 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_5 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_6 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_7 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_8 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_9 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_10 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_11 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_12 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_13 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_14 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_15 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_16 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_17 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_18 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_19 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_20 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_21 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_22 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_23 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_24 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_25 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_26 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_27 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_28 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_29 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_30 }}</td>
                                    <td class="text-small text-right valign-middle">{{ $instruction->day_31 }}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="section">
                        <div class="box">
                            n件の内示データ取込内容を表示してます
                            <table class="table table-bordered table-striped" style="width: 71%">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="valign-center">製品品番</th>
                                        <th rowspan="2" class="valign-center">品名</th>
                                        <th rowspan="2" class="valign-center">2023年03月</th>
                                        <th rowspan="2" class="valign-center">2023年04月</th>
                                        <th rowspan="2" class="valign-center">2023年05月</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($kanbans as $kanban)
                                <tr>
                                    <td class="valign-middle">{{ $kanban->product_id }}</td>
                                    <td class="valign-middle">123456-1品名</td>
                                    <td class="valign-middle text-right">{{ $kanban->current_month }}</td>
                                    <td class="valign-middle text-right">{{ $kanban->next_month }}</td>
                                    <td class="valign-middle text-right">{{ $kanban->two_months_later }}</td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="ml-1">
                        <div class="d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">警告ファイル</label>
                                <div>
                                    <a href="javascript:void(0)">ダウンロード</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="float-right">
                <button class="btn btn-green text-white" id="submit-form">この内容で登録する</button>
            </div>
           
        </div>
    </div>
@endsection
