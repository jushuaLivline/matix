@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/received_materials_list.css')
    @vite('resources/css/master/supplier/edit.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>取引先マスタ登録・編集</span></h1>
            </div>
            
            <form id='submit-supplier-form' 
                data-action='{{ isset($data) ? $data->id : 'store' }}' class='overlayedSubmitForm width-js-validation' accept-charset="utf-8">
                @csrf
                <div class="bg-white">
                    <div class="row">
                        <div class="col-2 label-div">
                            取引先コード &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="customer_code" id="customer_code"
                            class="acceptNumericOnly"
                            maxlength="6"
                            value="{{ isset($data) ? $data->customer_code : Request::get('customer_code') }}"
                            required
                        >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            取引先名 &nbsp; <span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="customer_name" id="customer_name"
                                value="{{ isset($data) ? $data->customer_name : Request::get('customer_name') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            取引先略名 &nbsp; <span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="supplier_name_abbreviation" id="supplier_name_abbreviation"
                                value="{{ isset($data) ? $data->supplier_name_abbreviation : Request::get('supplier_name_abbreviation') }}"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            取引先名カナ
                        </div>
                        <div class="col-10">
                            <input type="text" name="business_partner_kana_name" id="business_partner_kana_name"
                                value="{{ isset($data) ? $data->business_partner_kana_name : Request::get('business_partner_kana_name') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            支店工場名
                        </div>
                        <div class="col-10">
                            <input type="text" name="branch_factory_name" id="branch_factory_name"
                                value="{{  isset($data) ? $data->branch_factory_name :  Request::get('branch_factory_name') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            支店工場名カナ
                        </div>
                        <div class="col-10">
                            <input type="text" name="kana_name_of_branch_factory" id="kana_name_of_branch_factory"
                                value="{{ isset($data) ? $data->kana_name_of_branch_factory : Request::get('kana_name_of_branch_factory') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            郵便番号
                        </div>
                        <div class="col-10">
                            <input type="text" name="post_code" id="post_code"
                                value="{{ isset($data) ? $data->post_code : Request::get('post_code') }}"
                                maxlength="7"
                                class="acceptNumericOnly"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            住所1
                        </div>
                        <div class="col-10">
                            <input type="text" name="address_1" id="address_1"
                                value="{{ isset($data) ? $data->address_1 : Request::get('address_1') }}"
                                class="w-50"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            住所2
                        </div>
                        <div class="col-10">
                            <input type="text" name="address_2" id="address_2"
                                value="{{ isset($data) ? $data->address_2 : Request::get('address_2') }}"
                                class="w-50"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            電話番号
                        </div>
                        <div class="col-10">
                            <input type="text" name="telephone_number" id="telephone_number"
                                value="{{ isset($data) ? preg_replace('/\D/', '', $data->telephone_number) : Request::get('telephone_number') }}"
                                maxlength="11"
                                class="w-150px acceptNumericOnly"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            FAX番号
                        </div>
                        <div class="col-10">
                            <input type="text" name="fax_number" id="fax_number"
                                value="{{ isset($data) ? preg_replace('/\D/', '', $data->fax_number) : Request::get('fax_number') }}"
                                maxlength="11"
                                class="w-150px acceptNumericOnly"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            代表者名
                        </div>
                        <div class="col-10">
                            <input type="text" name="representative_name" id="representative_name"
                                value="{{ isset($data) ? $data->representative_name : Request::get('representative_name') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                           資本金 
                        </div>
                        <div class="col-10">
                            <input type="text" name="capital" id="capital"
                                value="{{ isset($data) ? $data->capital : Request::get('capital') }}"
                                class="number-format"
                                maxlength="11"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            得意先
                        </div>
                        <div class="col-10">
                            <input type="checkbox" name="customer_flag" id="customer_flag"
                                value="1"
                                {{ isset($data) && $data->customer_flag == 1 || Request::get('customer_flag') == 1 ? 'checked' : '' }}
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            仕入先
                        </div>
                        <div class="col-10">
                            <input type="checkbox" name="supplier_tag" id="supplier_tag"
                                value="1"
                                {{ isset($data) && $data->supplier_tag == 1 || Request::get('supplier_tag') == 1 ? 'checked' : '' }}
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            仕入先区分
                        </div>
                        <div class="col-10 radio-div d-flex">
                            <div class="mr-4 d-flex align-items-center">
                                <input type="radio" name="supplier_classication" id="supply_1"
                                    value="1"
                                    {{ isset($data) && $data->supplier_classication == 1 || Request::get('supplier_classication') == 1 ? 'checked' : '' }}
                                > &nbsp;
                                <label for="supply_1">加工メーカー</label>
                            </div>
                            <div class="mr-4 d-flex align-items-center">
                                <input type="radio" name="supplier_classication" id="supply_2"
                                    value="2"
                                    {{ isset($data) && $data->supplier_classication == 2 || Request::get('supplier_classication') == 2 ? 'checked' : '' }}
                                > &nbsp;
                                <label for="supply_2">材料メーカー</label>
                            </div>
                            <div class="mr-4 d-flex align-items-center">
                                <input type="radio" name="supplier_classication" id="supply_3"
                                    value="3"
                                    {{ isset($data) && $data->supplier_classication == 3 || Request::get('supplier_classication') == 3 ? 'checked' : '' }}
                                > &nbsp;
                                <label for="supply_3">その他</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            購入実績適用
                        </div>
                        <div class="col-10">
                            <div class="d-flex align-items-center">
                                <input type="checkbox" name="purchase_report_apply_flag" id="purchase_report_apply_flag"
                                    value="1"
                                    {{ isset($data) && $data->purchase_report_apply_flag == 1 || Request::get('purchase_report_apply_flag') == 1 ? 'checked' : '' }}
                                >&nbsp;
                                <label for="purchase_report_apply_flag">購買受け入れ処理をすると同時に購入実績に適用する</label>
                            </div>
                            <span class="sub-message">※一括の納品書で、購入実績入力から手入力する仕入先の場合は、チェックを外してください。</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            金額丸め区分
                        </div>
                        <div class="col-10 d-flex">
                            <div class="d-flex align-items-center mr-4">
                                <div class="label">売上:</div>
                                <div class="radio-div d-flex">
                                    <div class="mr-4 d-flex align-items-center">
                                        <input type="radio" name="sales_amount_rounding_indicator" id="round_1"
                                            value="1"
                                            {{ isset($data) && $data->sales_amount_rounding_indicator == 1 || Request::get('sales_amount_rounding_indicator') == 1 ? 'checked' : '' }}
                                        > &nbsp;
                                        <label for="round_1">加工メーカー</label>
                                    </div>
                                    <div class="mr-4 d-flex align-items-center">
                                        <input type="radio" name="sales_amount_rounding_indicator" id="round_2"
                                            value="2"
                                            {{ isset($data) && $data->sales_amount_rounding_indicator == 2 || Request::get('sales_amount_rounding_indicator') == 2 ? 'checked' : '' }}
                                        > &nbsp;
                                        <label for="round_2">材料メーカー</label>
                                    </div>
                                    <div class="mr-4 d-flex align-items-center">
                                        <input type="radio" name="sales_amount_rounding_indicator" id="round_3"
                                            value="3"
                                            {{ isset($data) && $data->sales_amount_rounding_indicator == 3 || Request::get('sales_amount_rounding_indicator') == 3 ? 'checked' : '' }}
                                        > &nbsp;
                                        <label for="round_3">その他</label>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center ">
                                <div class="label">仕入:</div>
                                <div class="radio-div d-flex">
                                    <div class="mr-4 d-flex align-items-center">
                                        <input type="radio" name="purchase_amount_rounding_indicator" id="round_1a"
                                            value="1"
                                            {{ isset($data) && $data->purchase_amount_rounding_indicator == 1 || Request::get('purchase_amount_rounding_indicator') == 1 ? 'checked' : '' }}
                                        > &nbsp;
                                        <label for="round_1a">加工メーカー</label>
                                    </div>
                                    <div class="mr-4 d-flex align-items-center">
                                        <input type="radio" name="purchase_amount_rounding_indicator" id="round_2a"
                                            value="2"
                                            {{ isset($data) && $data->purchase_amount_rounding_indicator == 2 || Request::get('purchase_amount_rounding_indicator') == 2 ? 'checked' : '' }}
                                        > &nbsp;
                                        <label for="round_2a">材料メーカー</label>
                                    </div>
                                    <div class="mr-4 d-flex align-items-center">
                                        <input type="radio" name="purchase_amount_rounding_indicator" id="round_3a"
                                            value="3"
                                            {{ isset($data) && $data->purchase_amount_rounding_indicator == 3 || Request::get('purchase_amount_rounding_indicator') == 3 ? 'checked' : '' }}
                                        > &nbsp;
                                        <label for="round_3a">その他</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            振込元銀行
                        </div>
                        <div class="col-10 d-flex">
                            <div class="d-flex align-items-center w-25">
                                <label for="transfer_source_bank_code">銀行コード：</label>
                                <input type="text" name="transfer_source_bank_code" id="transfer_source_bank_code"
                                    value="{{ isset($data) ? $data->transfer_source_bank_code : Request::get('transfer_source_bank_code') }}"
                                    class="w-40"
                                >
                            </div>
                            <div class="d-flex align-items-center w-25">
                                <label for="transfer_source_bank_branch_code">支店コード：</label>
                                <input type="text" name="transfer_source_bank_branch_code" id="transfer_source_bank_branch_code"
                                    value="{{ isset($data) ? $data->transfer_source_bank_branch_code : Request::get('transfer_source_bank_branch_code') }}"
                                    class="w-40"
                                >
                            </div>
                            <div class="d-flex align-items-center w-25">
                                <label for="transfer_source_account_number">口座番号：</label>
                                <input type="text" name="transfer_source_account_number" id="transfer_source_account_number"
                                    value="{{ isset($data) ? $data->transfer_source_account_number : Request::get('transfer_source_account_number') }}"
                                    class="w-50"
                                >
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="label">口座区分：</div>
                                <div class="d-flex">
                                    <input type="radio" name="transfer_source_account_clarification" id="transfer_1"
                                        value="1"
                                        {{ isset($data) && $data->transfer_source_account_clarification == 1 || Request::get('transfer_source_account_clarification') == 1 ? 'checked' : '' }}
                                    > &nbsp;
                                    <label for="transfer_1">普通</label> &nbsp; &nbsp;
                                    <input type="radio" name="transfer_source_account_clarification" id="transfer_2"
                                        value="2"
                                        {{ isset($data) && $data->transfer_source_account_clarification == 2 || Request::get('transfer_source_account_clarification') == 2 ? 'checked' : '' }}
                                    > &nbsp;
                                    <label for="transfer_2">当座</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            振込先銀行
                        </div>
                        <div class="col-10 d-flex">
                            <div class="d-flex align-items-center w-25">
                                <label for="payee_bank_code">銀行コード：</label>
                                <input type="text" name="payee_bank_code" id="payee_bank_code"
                                    value="{{ isset($data) ? $data->payee_bank_code : Request::get('payee_bank_code') }}"
                                    class="w-40"
                                >
                            </div>
                            <div class="d-flex align-items-center w-25">
                                <label for="transfer_destination_bank_branch_code">支店コード：</label>
                                <input type="text" name="transfer_destination_bank_branch_code" id="transfer_destination_bank_branch_code"
                                    value="{{ isset($data) ? $data->transfer_destination_bank_branch_code : Request::get('transfer_destination_bank_branch_code') }}"
                                    class="w-40"
                                >
                            </div>
                            <div class="d-flex align-items-center w-25">
                                <label for="transfer_account_number">口座番号：</label>
                                <input type="text" name="transfer_account_number" id="transfer_account_number"
                                    value="{{ isset($data) ? $data->transfer_account_number : Request::get('transfer_account_number') }}"
                                    class="w-50"
                                >
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="label">口座区分：</div>
                                <div class="d-flex">
                                    <input type="radio" name="transfer_account_clasification" id="transfer_1a"
                                        value="1"
                                        {{ isset($data) && $data->transfer_account_clasification == 1 || Request::get('transfer_account_clasification') == 1 ? 'checked' : '' }}
                                    > &nbsp;
                                    <label for="transfer_1a">普通</label> &nbsp; &nbsp;
                                    <input type="radio" name="transfer_account_clasification" id="transfer_2a"
                                        value="2"
                                        {{ isset($data) && $data->transfer_account_clasification == 2 || Request::get('transfer_account_clasification') == 2 ? 'checked' : '' }}
                                    > &nbsp;
                                    <label for="transfer_2a">当座</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            振込手数料負担区分
                        </div>
                        <div class="col-10 radio-div d-flex">
                            <div class="mr-4 d-flex align-items-center">
                                <input type="radio" name="transfer_fee_burden_category" id="burden_1"
                                    value="1"
                                    {{ isset($data) && $data->transfer_fee_burden_category == 1 || Request::get('transfer_fee_burden_category') == 1 ? 'checked' : '' }}
                                > &nbsp;
                                <label for="burden_1">先方</label>
                            </div>
                            <div class="mr-4 d-flex align-items-center">
                                <input type="radio" name="transfer_fee_burden_category" id="burden_2"
                                    value="2"
                                    {{ isset($data) && $data->transfer_fee_burden_category == 2 || Request::get('transfer_fee_burden_category') == 2 ? 'checked' : '' }}
                                > &nbsp;
                                <label for="burden_2">当方</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            手形割合  &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="bill_ratio" id="bill_ratio"
                                value="{{ isset($data) ? $data->bill_ratio : Request::get('bill_ratio') }}"
                                class="acceptNumericOnly w-10"
                                maxlength="3" 
                            > %
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            振込手数料
                        </div>
                        <div class="col-10 d-flex align-items-start">
                            <div class="d-flex align-items-center mt-5px">
                                <label for="transfer_fee_condition_amount">振込額</label> &nbsp;
                                <input type="text" name="transfer_fee_condition_amount" id="transfer_fee_condition_amount"
                                    value="{{ isset($data) ? $data->transfer_fee_condition_amount : Request::get('transfer_fee_condition_amount') }}"
                                    class="number-format"
                                    maxlength="11"
                                > &nbsp; 円
                            </div>
                            <div>
                                <div class="d-flex align-items-center">
                                    <label for="amount_less_than_transfer_fee_conditions">未満</label> &nbsp;
                                    <input type="text" name="amount_less_than_transfer_fee_conditions" id="amount_less_than_transfer_fee_conditions"
                                        value="{{ isset($data) ? $data->amount_less_than_transfer_fee_conditions : Request::get('amount_less_than_transfer_fee_conditions') }}"
                                        class="number-format"
                                        maxlength="11"
                                    > &nbsp; 円
                                </div>

                                <div class="d-flex align-items-center">
                                    <label for="transfer_fee_condition_or_more_amount">以上</label> &nbsp;
                                    <input type="text" name="transfer_fee_condition_or_more_amount" id="transfer_fee_condition_or_more_amount"
                                        value="{{ isset($data) ? $data->transfer_fee_condition_or_more_amount :  Request::get('transfer_fee_condition_or_more_amount') }}"
                                        class="number-format"
                                        maxlength="11"
                                    > &nbsp; 円
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            無効にする
                        </div>
                        <div class="col-10">
                            <input type="checkbox" name="delete_flag" id="delete_flag"
                                value="1"
                                {{ isset($data) && $data->delete_flag == 1 || Request::get('delete_flag') == 1 ? 'checked' : '' }}  
                            >
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-success btn-wide">登録する</button>
                </div>
            </form>

        </div>
    </div>
@endsection
@push('scripts')
    @vite(['resources/js/master/supplier/edit.js'])
@endpush