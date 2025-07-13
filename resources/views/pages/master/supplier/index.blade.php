@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/master/supplier/index.css')
@endpush

@section('title', '取引先マスタ一覧
')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>取引先マスタ一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span> 検索</span></h1>
            </div>

            <div class="section mt-4">
                <form id="customer_form" method="GET" accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
                    <div class="box">
                        <div class="d-flex mb-4">
                            <div class="mr-4">
                                <label class="form-label dotted indented">取引先コード</label> <span class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" id="customer_code" name="customer_code" 
                                        value="{{ Request::get("customer_code") }}"
                                        class="acceptNumericOnly"
                                        maxlength="6"
                                        style="width: 120px;"
                                        required>
                                </div>
                            </div>
                            <div class="ml-4 mr-3">
                                <label class="form-label dotted indented">取引先名</label> <span class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" id="customer_name" name="customer_name" 
                                        value="{{ Request::get("customer_name") }}"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="mr-4">
                                <label class="form-label dotted indented">取引先名</label>
                                <div class="d-flex">
                                    <select name="customer_flag" id="customer_flag">
                                        <option value="all" {{ Request::get('customer_flag', 'all') == 'all' ? 'selected' : '' }}>すべて</option>
                                        <option value="1" {{ Request::get("customer_flag") == 1 ? 'selected' : '' }}>得意先</option>
                                        <option value="0" {{ Request::get("customer_flag") == 0 ? 'selected' : '' }}>仕入先</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mr-4">
                                <label class="form-label dotted indented">有効/無効</label>
                                <div class="d-flex">
                                    <select name="delete_flag" id="delete_flag">
                                        <option value="all" {{ Request::get("delete_flag") == "all" ? 'selected' : '' }}>すべて</option>
                                        <option value="0" {{ Request::get("delete_flag") == 0 || !Request::get("delete_flag") ? 'selected' : '' }}>有効</option>
                                        <option value="1" {{ Request::get("delete_flag") == 1 ? 'selected' : '' }}>無効</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="text-center button-div">
                            <button type="reset" class="btn btn-primary btn-wide">検索条件をクリア</button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>

                            <a href="{{ route('master.supplier.excel_export', Request::all()) }}" class="btn btn-success  {{ $customer_records->total() == 0 ? 'btn-disabled' : '' }}">検索結果をEXCEL出力</a>
                        </div>
                    </div>
                </form>
            </form>
        </div>

        <div class="section mt-4" id="search-label">
            <h1 class="form-label bar indented">検索結果</h1>
        </div>

        <div class="tableWrap bordertable" style="clear: both;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    @if($customer_records && $customer_records->total() > 0)
                        {{ $customer_records->total() }}件中、{{ $customer_records->firstItem() }}件～{{ $customer_records->lastItem() }} 件を表示しています
                    @endif
                </div>
                <a href="/master/supplier/create" class="btn btn-primary">新規登録</a>
            </div>
            <table class="tableBasic" id="daily-inputs">
                <thead>
                    <tr>
                        <th>取引先コード</th>
                        <th>取引先名</th>
                        <th>郵便番号</th>
                        <th>住所1</th>
                        <th>電話番号</th>
                        <th>得意先</th>
                        <th>仕入先</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customer_records as $customer)
                        <tr>
                            <td class="text-center">{{ $customer->customer_code }}</td>
                            <td>{{ $customer->customer_name }}</td>
                            <td class="text-center">
                                @php
                                    if($customer->post_code){
                                        $post = preg_replace('/\D/', '', $customer->post_code);
                                        if (strlen($post) >= 4) {
                                            echo substr($post, 0, 3) . '-' . substr($post, 3, 4);
                                        } else {
                                            echo $post;
                                        }
                                    }
                                @endphp
                            </td>
                            <td>{{ $customer->address_1 }}</td>
                            <td class="text-center">
                                @php
                                    if($customer->telephone_number){
                                        $tel = preg_replace('/\D/', '', $customer->telephone_number);
                                        
                                        if (preg_match('/^(090|080|070)/', $tel)) {
                                            $formattedTel = substr($tel, 0, 3) . '-' . substr($tel, 3, 3) . '-' . substr($tel, 6, 4);
                                        } else {
                                            $formattedTel = substr($tel, 0, 2) . '-' . substr($tel, 2, 4) . '-' . substr($tel, 6, 4);
                                        }
                                        // Remove trailing hyphen if the phone number is <= 6 digits
                                        if (strlen($tel) <= 6) {
                                            $formattedTel = rtrim($formattedTel, '-');
                                        }
                                        echo $formattedTel;
                                    }
                                @endphp
                            </td>
                            <td class="text-center">
                                {{ $customer->customer_flag == 1 ?'o' : 'x'  }}
                            </td>
                            <td class="text-center">
                                {{ $customer->supplier_tag == 1 ? 'o' : 'x' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('master.supplier.edit', $customer->id) }}" class="btn btn-primary">編集</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">検索結果はありません</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $customer_records->appends(request()->all())->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/master/supplier/index.js')
@endpush