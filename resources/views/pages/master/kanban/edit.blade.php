@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/received_materials_list.css')
    @vite('resources/css/master/kanban/edit.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', 'かんばんマスタ登録・編集')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>かんばんマスタ登録・編集</span></h1>
            </div>

            <form id='submit-kanban-form' data-action='{{ isset($data) ? $data->id : 'store' }}' class='overlayedSubmitForm' accept-charset="utf-8">
                @csrf
                <div class="bg-white">
                    <div class="row">
                        <div class="col-2 label-div">
                            管理No. &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="management_no" id="management_no"
                            class="acceptNumericOnly"
                            maxlength="6"
                            value="{{ $data->management_no ?? old('management_no') ?? '' }}"
                            required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            かんばん区分 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <div class="d-flex p-0">
                                <div class="mr-4 d-flex align-items-center">
                                    <input type="radio" name="kanban_classification" id="kanban_1"
                                        value="1"
                                        {{ isset($data) && $data->kanban_classification == 1 || Request::get('kanban_classification') == 1 ? 'checked' : '' }}
                                        required
                                    > &nbsp;
                                    <label for="kanban_1">支給材</label>
                                </div>
                                <div class="mr-4 d-flex align-items-center">
                                    <input type="radio" name="kanban_classification" id="kanban_2"
                                        value="2"
                                        {{ isset($data) && $data->kanban_classification == 2 || Request::get('kanban_classification') == 2 ? 'checked' : '' }}
                                    > &nbsp;
                                    <label for="kanban_2">外注加工</label>
                                </div>
                                <div class="mr-4 d-flex align-items-center">
                                    <input type="radio" name="kanban_classification" id="kanban_3"
                                        value="3"
                                        {{ isset($data) && $data->kanban_classification == 3 || Request::get('kanban_classification') == 3 ? 'checked' : '' }}
                                    > &nbsp;
                                    <label for="kanban_3">外注支給</label>
                                </div>
                                <div class="mr-4 d-flex align-items-center">
                                    <input type="radio" name="kanban_classification" id="kanban_4"
                                        value="4"
                                        {{ isset($data) && $data->kanban_classification == 4 || Request::get('kanban_classification') == 4 ? 'checked' : '' }}
                                    > &nbsp;
                                    <label for="kanban_4">社内</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            品番
                        </div>
                        <div class="col-10">
                            <div class="d-flex p-0">
                                <input type="text" id="part_number" name="part_number"
                                    value="{{ isset($data) ? $data->part_number : Request::get('part_number') }}"
                                    class="fetchQueryName mr-2"
                                    data-model="ProductNumber"
                                    data-query="part_number"
                                    data-query-get="product_name"
                                    data-reference="product_name"
                                    required
                                    >
                                <input type="text" readonly
                                    id="product_name"
                                    name="product_name"
                                    value="{{ isset($data) ? $data->product?->product_name : Request::get('product_name') }}"
                                    class="middle-name mr-2"
                                    >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchProductModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            工程コード
                        </div>
                        <div class="col-10">
                            <div class="d-flex p-0">
                                <input type="text" id="process_code" name="process_code"
                                    value="{{ isset($data) ? $data->process_code : Request::get('process_code') }}"
                                    class="w-100px fetchQueryName mr-2"
                                    data-model="Process"
                                    data-query="process_code"
                                    data-query-get="process_name"
                                    data-reference="process_name"
                                    >
                                <input type="text" readonly
                                    id="process_name"
                                    name="process_name"
                                    value="{{ isset($data) ? $data->process?->process_name : Request::get('process_name') }}"
                                    class="middle-name mr-2"
                                    >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchProcessModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            得意先受入
                        </div>
                        <div class="col-10">
                            <input type="text" name="customer_acceptance" id="customer_acceptance"
                                class="acceptNumericOnly w-100px"
                                maxlength="3"
                                value="{{ isset($data) ? $data->customer_acceptance : Request::get('customer_acceptance') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            次工程コード
                        </div>
                        <div class="col-10">
                            <div class="d-flex p-0">
                                <input type="text" id="next_process_code" name="next_process_code"
                                    value="{{ isset($data) ? $data->next_process_code : Request::get('next_process_code') }}"
                                    class="w-100px fetchQueryName mr-2"
                                    data-model="Process"
                                    data-query="process_code"
                                    data-query-get="process_name"
                                    data-reference="next-process_name"
                                    >
                                <input type="text" readonly
                                    id="next_process_name"
                                    name="next_process_name"
                                    value="{{ isset($data) ? $data->next_process?->process_name : Request::get('next_process_name') }}"
                                    class="middle-name mr-2"
                                    >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchNextProcessModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Display the below Once needed or approved --}}
                    {{-- <div class="row">
                        <div class="col-2 label-div">
                            サイクル
                        </div>
                        <div class="col-10 d-flex">
                            <div class="d-flex align-items-center mr-4">
                                <label for="cycle_day">サイクル日：</label>
                                <input type="text" id="cycle_day" name="cycle_day"
                                    value="{{ isset($data) ? $data->cycle_day : Request::get('cycle_day') }}"
                                    class="w-100px acceptNumericOnly"
                                    maxlength="2"
                                >
                            </div>
                            <div class="d-flex align-items-center mr-4">
                                <label for="number_of_cycles">サイクル回数：</label>
                                <input type="text" id="number_of_cycles" name="number_of_cycles"
                                    value="{{ isset($data) ? $data->number_of_cycles : Request::get('number_of_cycles') }}"
                                    class="w-100px acceptNumericOnly"
                                    maxlength="2"
                                >
                            </div>
                            <div class="d-flex align-items-center">
                                <label for="cycle_interval">サイクル間隔：</label>
                                <input type="text" id="cycle_interval" name="cycle_interval"
                                    value="{{ isset($data) ? $data->cycle_interval : Request::get('cycle_interval') }}"
                                    class="w-100px acceptNumericOnly"
                                    maxlength="2"
                                >
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-2 label-div">
                            収容数 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" id="number_of_accomodated" name="number_of_accomodated"
                                value="{{ isset($data) ? $data->number_of_accomodated : Request::get('number_of_accomodated') }}"
                                class="w-100px"
                                maxlength="4"
                                required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            箱種
                        </div>
                        <div class="col-10">
                            <input type="text" id="box_type" name="box_type"
                                value="{{ isset($data) ? $data->box_type : Request::get('box_type') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            受入/返却先
                        </div>
                        <div class="col-10">
                            <input type="text" id="acceptance" name="acceptance"
                                class="acceptNumericOnly"
                                maxlength="3"
                                value="{{ isset($data) ? $data->acceptance : Request::get('acceptance') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            印刷背番号
                        </div>
                        <div class="col-10">
                            <input type="text" id="printed_jersey_number" name="printed_jersey_number"
                                value="{{ isset($data) ? $data->printed_jersey_number : Request::get('printed_jersey_number') }}"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            備考1
                        </div>
                        <div class="col-10 d-flex">
                            <textarea name="remark_1" id="remark_1" cols="30" rows="6"
                                class="mr-2"
                            >{{ isset($data) ? $data->remark_1 : Request::get('remark_1') }}</textarea>
                            <button type="button" class="btn btn-blue">製品品番参照</button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-2 label-div">
                            備考2
                        </div>
                        <div class="col-10">
                            <textarea name="remark_2" id="remark_2" cols="30" rows="6"
                                class="mr-1"
                            >{{ isset($data) ? $data->remark_1 : Request::get('remark_2') }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            QRコード面備考 
                        </div>
                        <div class="col-10">
                            <textarea name="remark_qr_code" id="remark_qr_code" cols="30" rows="3"
                                class="mr-1"
                            >{{ isset($data) ? $data->remark_1 : Request::get('remark_qr_code') }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            発行済連番
                        </div>
                        <div class="col-10">
                            <input type="text" id="issued_sequence_number" name="issued_sequence_number"
                                value="{{ isset($data) ? $data->issued_sequence_number : Request::get('issued_sequence_number') }}"
                                class="w-100px acceptNumericOnly"
                                maxlength="4"
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            有償無償区分
                        </div>
                        <div class="col-10 d-flex">
                            <div class="mr-4 d-flex align-items-center">
                                <input type="radio" name="paid_category" id="pc_1"
                                    value="1"
                                    {{ isset($data) && $data->paid_category == 1 || Request::get('paid_category') == 1 ? 'checked' : '' }}
                                > &nbsp;
                                <label for="pc_1">有償</label>
                            </div>
                            <div class="mr-4 d-flex align-items-center">
                                <input type="radio" name="paid_category" id="pc_2"
                                    value="2"
                                    {{ isset($data) && $data->paid_category == 2 || Request::get('paid_category') == 2 ? 'checked' : '' }}
                                > &nbsp;
                                <label for="pc_2">無償</label>
                            </div>  
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-2 label-div">
                            無効にする
                        </div>
                        <div class="col-10">
                             <input type="hidden" name="delete_flag" value="0">
                            <input type="checkbox" id="delete_flag" name="delete_flag"
                                value="1"
                                {{ isset($data) && $data->delete_flag == 1 ? 'checked' : '' }}
                                >
                        </div>
                    </div>

                </div>
           
            <div class="d-flex justify-content-between mt-4 btn-div">
                <div>
                    <button type="button" class="btn btn-delete @if(isset($data) && $data->delete_flag == 1)btn-disabled @endif" 
                        @if(isset($data) && $data->delete_flag == 1) disabled @endif
                        id="update-delete">削除</button>
                </div>
                <div>
                    <button type="button" class="btn btn-primary js-modal-open" data-target="print_modal">かんばん印刷</button>
                    <button type="button" class="btn btn-blue" id="copy-data">複写入力</button>
                    <button type="button" id="submit-add-update" class="btn btn-success">登録する</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '工程コード',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'process_name',
        'model' => 'Process'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchNextProcessModal',
        'searchLabel' => '工程コード',
        'resultValueElementId' => 'next_process_code',
        'resultNameElementId' => 'next_process_name',
        'model' => 'Process'
    ])

    @include('pages.master.kanban.print_modal')

@endsection

@push('scripts')
    @vite(['resources/js/master/kanban/edit.js'])
@endpush