@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    {{-- @vite('resources/css/shipments/shipment_entry.css') --}}
    <style>
        .input-error {
            border: 2px solid red !important;
        }
    </style>
@endpush

@push('scripts')
    @vite(['resources/js/shipment-inspections/entry.js'])
    <script>
        const shipmentEntryUrl = @json(route('shipment-inspections.shipmentEntry'));
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#clear-add").on("click", function() {
            $("#_product-number").val("");
            $("#_quantity").val("");
            $("#_remarks").val("");
        });

        $("#update-shipment").on("submit", function(e) {
            e.preventDefault();
            $("#error").html("");
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: {
                    "productNumber": $("input[name=productNumber]").val(),
                    "quantity": $("input[name=quantity]").val(),
                    "remarks": $("input[name=remarks]").val(),
                },
                success: function(data) {
                    if (data.status) {
                        location.reload();
                    }
                },
                error: function() {
                    $("#error").html("登録に必要ないくつかの情報が入力されていません！");
                }
            });
        });

        $(".edit-button").on("click", function() {
            $(this).parent().siblings("._show-mode").css("display", "none");
            $(this).parent().css("display", "none");
            $(this).parent().siblings("._edit-mode").css("display", "");
        });

        $(".cancel-button").on("click", function() {
            $(this).parent().siblings("._edit-mode").css("display", "none");
            $(this).parent().css("display", "none");
            $(this).parent().siblings("._show-mode").css("display", "");
        });

        $(".update-button").on("click", function() {
            if(confirm('「出荷実績情報を更新します、よろしいでしょうか？」')){
                console.log($(this).parent().siblings("._edit-mode").children("._remarks").val());
                $("input[name=productNumber]").val($(this).parent().siblings("._edit-mode").children("._product-number").val());
                $("input[name=quantity]").val($(this).parent().siblings("._edit-mode").children("._quantity").val());
                $("input[name=remarks]").val($(this).parent().siblings("._edit-mode").children("._remarks").val());
                // console.log(($("#update-shipment").attr("url")));
                $("#update-shipment").attr("action", ($("#update-shipment").attr("action") + "/" + $(this).siblings("._entry-id").val()));
                $("#update-shipment").submit();
            }
        });
        function enableInputs(button) {
            // Enable inputs in the same row
            const row = button.closest('tr');
            const inputs = row.querySelectorAll('input');
            inputs.forEach(input => {
                input.disabled = false;
            });

            // Hide "EditDelete" div and show "UdpateUndo" div
            const editDeleteDiv = row.querySelector('#EditDelete');
            const updateUndoDiv = row.querySelector('#UdpateUndo');
            editDeleteDiv.style.display = 'none';
            updateUndoDiv.style.display = 'flex';
        }
        function cancelEdit(button) {
            if (confirm('「キャンセルしますか？」')) {
                // Disable inputs in the same row
                const row = button.closest('tr');
                const inputs = row.querySelectorAll('input');
                inputs.forEach(input => {
                    input.disabled = true;
                });

                // Hide "UdpateUndo" div and show "EditDelete" div
                const editDeleteDiv = row.querySelector('#EditDelete');
                const updateUndoDiv = row.querySelector('#UdpateUndo');
                editDeleteDiv.style.display = 'flex';
                updateUndoDiv.style.display = 'none';
            }
        }
        function updateData(button) {
            // Get the row and kanban data ID
            const row = button.closest('tr');
            const dataId = row.getAttribute('data-supply-material-order-id');

            // Get the input values to update
            const productNumber = row.querySelector('input[name="part_no"]');
            const productName = row.querySelector('input[name="part_name"]');
            const quantity = row.querySelector('input[name="quantity"]');
            const remarks = row.querySelector('input[name="remarks"]');

            // Prepare the data to send in the request
            const data = {
                temp_data_id: dataId,
                productNumber: productNumber.value,
                productName: productName.value,
                quantity: quantity.value,
                remarks: remarks.value,
            };

            console.log(data);

            // Get the CSRF token from the meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Send an AJAX request to update the session data
            $.ajax({
                url: '/shipment-inspections/update-temp-data',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    // Handle the success response
                    alert('「データは正常に更新されました」');

                    location.reload();
                    // Disable inputs in the same row
                    const inputs = row.querySelectorAll('input');
                    inputs.forEach(input => {
                        input.disabled = true;
                    });

                    // Hide "UpdateUndo" div and show "EditDelete" div
                    const editDeleteDiv = row.querySelector('#EditDelete');
                    const updateUndoDiv = row.querySelector('#UdpateUndo');
                    editDeleteDiv.style.display = 'flex';
                    updateUndoDiv.style.display = 'none';
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    alert('An error occurred while updating the data.');
                }
            });
        }

        function confirmDelete(button) {
            if (confirm('「内示情報を削除します、よろしいでしょうか？」')) {
                const row = button.closest('tr');
                const dataId = row.getAttribute('data-supply-material-order-id');

                console.log(dataId);

                fetch(`/shipment-inspections/delete-temp-data/${dataId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include the CSRF token in the request headers
                    },
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error deleting data');
                        }
                        return response.json();
                    })
                    .then(responseData => {
                        // Handle the response data
                        alert('「支給材情報は正常に削除されました」');

                        // Remove the table row from the DOM
                        row.remove();
                    })
                    .catch(error => {
                        // Handle the error
                        alert('Error deleting data: ' + error);
                    });
            }
        }

        function bulkSavingData(button) {
            var stopExecution = false;
            var customerCode = $('#customer_code').val();
            var slipNo = $('#_slip-no').val();
            var dueDate = $('#instruction_date').val();
            var deliveryNo = $('#_delivery-no').val();
            var plant = $('#_plant').val();
            var acceptance = $('#_acceptance').val();
            var dropShip = $('#supplier_code').val();
            
            const sessionData = {!! json_encode(session('sessionShipmentTempData', [])) !!};

            // if no session data then return input missing
            if (!sessionData || sessionData.length === 0) {
                // console.log(123);
                $('#warningInputs').show();
                $('#_product-number').addClass('input-error');
                // $('#_product-name').addClass('input-error');
                $('#_quantity').addClass('input-error');
                stopExecution = true
                // return; // Stop execution if sessionKanbanData is missing or empty
            }

            if (!customerCode || !slipNo || !dueDate || !deliveryNo) {
                if (!customerCode) {
                    $('#customer_code').addClass('input-error');
                }
                if (!slipNo) {
                    $('#_slip-no').addClass('input-error');
                }
                if (!dueDate) {
                    $('#instruction_date').addClass('input-error');
                }
                if (!deliveryNo) {
                    $('#_delivery-no').addClass('input-error');
                }

                // if (!plant) {
                //     $('#_plant').addClass('input-error');
                // }
                // if (!acceptance) {
                //     $('#_acceptance').addClass('input-error');
                // }

                return;
            }
            
            // Prepare the data to send in the request
            const data = {
                session_data: sessionData,
                customerCode: customerCode,
                slipNo: slipNo,
                dueDate: dueDate,
                deliveryNo: deliveryNo,
                plant: plant,
                acceptance: acceptance,
                dropShip: dropShip,
            };

            // Send an AJAX request to the server to save the data
            $.ajax({
                url: '/shipment-inspections/store-data',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    sessionData.length = 0;

                    window.onbeforeunload = function() {
                        sessionStorage.removeItem('previousDate');
                        sessionStorage.removeItem('latestNumber');
                    };

                    // Show the success message with a delay
                    $('#successInputs').delay(1000).fadeIn(400, function() {
                        $(this).delay(500);
                    });

                    // Reload the page after a delay
                    setTimeout(function() {
                        window.location.href = shipmentEntryUrl;
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    // Handle the error response
                    alert('An error occurred while storing the data.');
                }
            });
        }
    </script>
@endpush

@section('title', '出荷実績入力')
@section('content')
    <form action="{{ route('shipment-inspections.shipmentEntry.add') }}" id="update-shipment" method="post" class="overlayedSubmitForm">
        <input type="hidden" name="productNumber">
        <input type="hidden" name="quantity">
        <input type="hidden" name="remarks">
    </form>
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                出荷実績入力
            </div>

            <div class="section">
                <h1 class="form-label bar indented">出荷実績入力</h1>
                <div class="box mb-1">
                    <form id="form" action="{{ route("shipment-inspections.store") }}" method="POST" class="overlayedSubmitForm">
                        @csrf
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">納入先 </label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p">
                                        <input type="text" id="customer_code" name="customer_code" value="{{ Request::get('customer_code') }}" class="mr-25 searchOnInput Customer" style="width:100px" required>
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <input type="text" id="customer_name" name="customer_name" disabled style="width: 170px;" class="mr-25">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchCustomerModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                </div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">伝票No.</label> <span class="btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" name="slip_no" value="{{ Request::get('slip_no') }}" class="mr-half" id="_slip-no" required>
                                </div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">納入日</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                        'inputName' => 'instruction_date',
                                        'value' => Request::get('instruction_date'),
                                        'requeue' => true,
                                        'required' => true
                                    ])
                                </div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">便No.</label> <span class="btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text" name="delivery_no" value="{{ Request::get('delivery_no') }}" style="width: 50px;" class="mr-half" id="_delivery-no" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 d-flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">工場
                                    {{-- <span class="others-frame btn-orange badge">必須</span> --}}
                                </label>
                                <div class="d-flex">
                                    <input type="text" name="plant" value="{{ Request::get('plant') }}" style="width: 50px;" class="mr-half">
                                </div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">受入
                                    {{-- <span class="others-frame btn-orange badge">必須</span> --}}
                                </label>
                                <div class="d-flex">
                                    <input type="text" name="acceptance" value="{{ Request::get('acceptance') }}" style="width: 50px;" class="mr-half">
                                </div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">直送先 </label>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p">
                                        <input type="text" id="supplier_code" name="supplier_code" value="{{ Request::get('supplier_code') }}" style="width:100px" class="mr-25 searchOnInput Supplier">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <input type="text" id="supplier_name" name="supplier_name" value="{{ Request::get('supplier_name') }}" disabled class="mr-25">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchSupplierModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="mt-2">
                                <table class="table table-bordered table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>製品品番 <span class="others-frame btn-orange badge">必須</span></th>
                                            <th>品名</th>
                                            <th width="10%">納入計 <span class="others-frame btn-orange badge">必須</span></th>
                                            <th >備考</th>
                                            <th width="15%">操作</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body">
                                    {{-- @foreach($sessionShipmentTempData as $data)
                                            <tr data-supply-material-order-id="{{ $data['id'] }}">
                                                <td class="center">
                                                    <input type="text" value="{{ $data['part_no'] }}" id="part_no__{{ $data['id'] }}"
                                                        class="numberCharacter searchOnInput ProductNumber{{ $data['id'] }} mr-25" style="width: 100%" name="part_no" disabled>
                                                    <p class="formPack fixedWidth fpfw25p">
                                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                                data-target="searchProductModal_{{ $data['id'] }}">
                                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                    alt="magnifying_glass.svg">
                                                        </button>
                                                    </p>
                                                </td>
                                                <td>
                                                    <input type="text" readonly style="width:100%" name="part_name" class="textCharacter" value="{{ $data['part_name'] }}" id="part_name__{{ $data['id'] }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="numberCharacter" value="{{ $data['quantity'] }}" name="quantity" disabled>
                                                </td>
                                                <td>
                                                    <input type="text" class="textCharacter" value="{{ $data['remarks'] }}" name="remarks" disabled>
                                                </td>
                                                <td>
                                                    <div class="center" id="EditDelete">
                                                        <button onclick="enableInputs(this)" class="btn btn-block btn-blue" id="edit">編集</button>
                                                        <button onclick="confirmDelete(this)" class="btn btn-block btn-orange" style="margin-left: 2px" id="delete">削除</button>
                                                    </div>
                                                    
                                                    <div class="center" id="UdpateUndo" style="display: none;">
                                                        <button onclick="updateData(this)" class="btn btn-block btn-green" id="update">更新</button>
                                                        <button onclick="cancelEdit(this)" class="btn btn-block btn-gray" style="margin-left: 1px" id="undo">取消</button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @include('partials.modals.masters._search', [
                                                'modalId' => 'searchProductModal_'. $data['id'],
                                                'searchLabel' => '品番',
                                                'resultValueElementId' => 'part_no__'. $data['id'],
                                                'resultNameElementId' => 'part_name__'. $data['id'],
                                                'model' => 'ProductNumber'
                                            ])
                                            @php
                                                $dataConfigs['ProductNumber' . $data['id']] = [
                                                    'model' => 'ProductNumber',
                                                    'reference' => 'part_name__' . $data['id']
                                                ];
                                            @endphp
                                        @endforeach --}}
                                        {{-- @foreach ($entries as $entry)
                                        <tr>
                                            <td class="_show-mode">{{ $entry->part_no }}</td>
                                            <td class="_show-mode">{{ $entry->part_no }}</td>
                                            <td class="_show-mode">
                                                {{ $entry->quantity }}
                                            </td>
                                            <td class="_show-mode">
                                                {{ $entry->remarks ?? '' }}
                                            </td>
                                            <td class="center _show-mode">
                                                <button
                                                    class="btn btn-block btn-blue edit-button">編集
                                                </button>
                                                <input type="hidden" class="_customerCode" value="{{ $entry->customer_code }}">
                                                <input type="hidden" class="_slipNo" value="{{ $entry->slip_no }}">
                                                <input type="hidden" class="_dueDate" value="{{ $entry->due_date }}">
                                                <input type="hidden" class="_deliveryNo" value="{{ $entry->delivery_no }}">
                                                <input type="hidden" class="_plant" value="{{ $entry->plant ?? '' }}">
                                                <input type="hidden" class="_acceptance" value="{{ $entry->acceptance ?? '' }}">
                                                <input type="hidden" class="_dropShipCode" value="{{ $entry->drop_ship_code ?? '' }}">
                                                <button onclick="if(confirm('「内示情報を削除します、よろしいでしょうか？」')){}"
                                                    class="btn btn-block btn-orange" style="margin-left: 1px">削除
                                                </button>
                                            </td>
                                            <td class="center _edit-mode" style="display: none;">
                                                <input type="text" name="_product-number" value="{{ $entry->part_no }}"
                                                    class="numberCharacter _product-number" style="width: 100%">
                                                <button type="button" class="btnAction js-modal-open"
                                                    style="margin-left: 1px" data-target="searchCustomerModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </td>
                                            <td class="_edit-mode" style="display: none;">
                                                <input type="text" disabled style="width:100%" name="" class="textCharacter" value="{{ $entry->part_no }}">
                                            </td>
                                            <td class="_edit-mode" style="display: none;">
                                                <input type="text" class="numberCharacter _quantity" name="_quantity" value="{{ $entry->quantity }}">
                                            </td>
                                            <td class="_edit-mode" style="display: none;">
                                                <input type="text" class="textCharacter _remarks" name="_remarks" value="{{ $entry->remarks ?? '' }}">
                                            </td>
                                            <td class="center _edit-mode" style="display: none;">
                                                <button
                                                    class="btn btn-block btn-green update-button">更新
                                                </button>
                                                
                                                <input type="hidden" class="_entry-id" value="{{ $entry->id }}">

                                                <button
                                                    class="btn btn-block btn-gray cancel-button" style="margin-left: 1px">取消
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach --}}
                                        {{-- <tr>
                                            <td class="center">
                                                <input type="text" value="" id="_product-number"
                                                    class="numberCharacter searchOnInput ProductNumber mr-25" style="width: 100%">
                                                <p class="formPack fixedWidth fpfw25p">
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchPartNumberModal">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </p>
                                            </td>
                                            <td>
                                                <input type="text" disabled style="width:100%" name="" class="textCharacter" value="" id="_product-name">
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter" value="" id="_quantity">
                                            </td>
                                            <td>
                                                <input type="text" class="textCharacter" value="" id="_remarks">
                                            </td>
                                            <td class="center">
                                                <button type="button" id="cache-shipment-data" class="btn btn-block btn-success">追加</button>
                                                <button type="button"  class="btn btn-block btn-success addRow">追加</button>
                                                <button class="btn btn-block btn-secondary" style="margin-left: 1px" id="clear-add">クリア
                                                </button>
                                            </td>
                                        </tr> --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="space-between">
                <div>
                    <p class="text-red" id="warningInputs" style="display:none;">登録に必要ないくつかの情報が入力されていません！</p>
                    <p class="text-red" id="productNumberWarning" style="display:none;">マスターに登録が無い品番です</p>
                    @if(Session::has("success"))
                    <p id="successInputs" style="color:#0d9c38;">「データは正常に登録されました」</p>
                    @endif
                </div>
                <div class="sc">
                    <a href="#" class="btn btn-primary btn-wide"> メニューに戻る </a>
                    <!-- <a href="#" class="btn btn-orange" style="width: 250px"> 全行のデータを削除 </a> -->
                    {{-- <button onclick="bulkSavingData(this)"  class="btn btn-success btn-wide"> この内容で登録する </button> --}}
                    <button onclick="formValidateAndSubmit()" class="btn btn-success btn-wide"> この内容で登録する </button>
                </div>
            </div>
        </div>
    </div>

    <div id="modalContainer">
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '直送先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => '_product-number',
        'resultNameElementId' => '_product-name',
        'model' => 'ProductNumber'
    ])
@endsection

@php
    $dataConfigs['Customer'] = [
        'model' => 'Customer',
        'reference' => 'customer_name'
    ];
    $dataConfigs['Supplier'] = [
        'model' => 'Supplier',
        'reference' => 'supplier_name'
    ];
    $dataConfigs['ProductNumber'] = [
        'model' => 'ProductNumber',
        'reference' => '_product-name'
    ];
@endphp
<x-search-on-input :dataConfigs="$dataConfigs" />
@push("scripts")
    <script>

        var allProductNumberIsValid = true;

        $(document).ready(function(){
            addRow();
        });


        function formValidateAndSubmit(){
            var valid = true;

            //clear validation error first
            $('#form').find("input").removeClass("input-error")

            $('#form').find('input[required]').each(function() {
                if($(this).val() == ""){
                    valid = false;
                    $(this).addClass("input-error")
                }
            })

            if(valid){
               $('#form').find("input").removeClass("input-error")
            }

            if(valid){
                $('#form').submit();
            }
        }


        $("#table-body").on( 'click', '.addRow', function (e) {
            addRow($(this))
        });

        $("#table-body").on( 'click', '.removeRow', function (e) {
            var currentLength = $("#table-body tr").length;
            if(currentLength > 1){
                var confirmDeleteMessage = confirm("削除しますか。")
                if(confirmDeleteMessage){
                    $(this).parents("tr").remove();
                }
            }
        });
        $(document).on( 'change, keyup', '.productNumberValidation', function (e) {
            var productNumber = $(this).val();
            if(productNumber){
                $(this).parents("tr").find("td").each( function (index){
                    $(this).find("input[name='product_name[]']").val('')
                })
                $("#productNumberWarning").hide();
                var response = fetch("/api/part-number/check-exists", {
                        method: 'POST',
                        body: JSON.stringify({ product_number: productNumber }),
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include the CSRF token in the request headers
                        },
                    })
                    .then(response => response.json())
                    .then( data => {
                        if(data.status == 'success'){
                            $(this).removeClass("input-error")
                            
                            $(this).parents("tr").find("td").each( function (index){
                                $(this).find("input[name='product_name[]']").val(data.data.product_name)
                            })
                        }else{
                            $("#productNumberWarning").show();
                            $(this).addClass("input-error")
                        }
                    })
            }else{

            }
        });

        function updateDeleteButtonsInRow(){
            $("#table-body tr").not(":last").each( function (index){
                // $(this).find(".addRow").remove()
                $(this).find(".removeRow").removeClass("btn-secondary").addClass("btn-orange")
            })

        }

        function addRow(btn){
            var current = $("#table-body tr").length;
            count = current + 1;

            if(count != 1){
                if(!validatedRow(btn)){
                    return;
                }else{

                }
            }

            $("#table-body").append(
                    '<tr>' +
                        '<td class="center">' + 
                            '<input required name="product_number[]" type="text" id="_product-number-'+ count +'" ' +
                                'class="numberCharacter searchOnInput ProductNumber productNumberValidation mr-25" style="width: 100%">' +
                            '<div class="error-message"></div>' + 
                            '<p class="formPack fixedWidth fpfw25p">' +
                                '<button type="button" class="btnSubmitCustom js-modal-open"' +
                                        'data-target="searchPartNumberModal-'+ count +'">' +
                                    '<img src="/images/icons/magnifying_glass.svg"' +
                                            'alt="magnifying_glass.svg">' +
                                '</button>' +
                            '</p>' +
                        '</td>' +
                        '<td>' +
                            '<input type="text" disabled style="width:100%" name="product_name[]" class="textCharacter" value="" id="_product-name-'+ count +'">' +
                        '</td>' +
                        '<td>' +
                            '<input type="text" required class="numberCharacter" name="quantity[]" id="_quantity">' +
                        '</td>' +
                        '<td>' +
                            '<input type="text" class="textCharacter" name="remarks[]" value="" id="_remarks">' +
                        '</td>' +
                        '<td class="center">' +
                            '<button type="button"  class="btn btn-block btn-success addRow">追加</button>' +
                            '<button class="btn btn-block btn-secondary removeRow" style="margin-left: 1px" id="clear-add">クリア</button>' +
                        '</td>' +
                    '</tr>'
            ) 

            addModal(count)
            updateDeleteButtonsInRow()
        }

        function validatedRow(btn){
            $(this).find("input").removeClass("input-error")
            var valid = true;
            var fetchStatus = "no";
            $(btn).parents("tr").find("td").each( function (index){
                if($(this).find("input[required]").val() === ''){
                    valid = false
                    $(this).find('input[required]').addClass("input-error")
                }else{
                    var productNumberValue = $(this).find("input[name='product_number[]']").val()
                    if(productNumberValue){
                   
                    }
                }
            })

            if(valid){
                $(btn).parents("tr").find("td").each( function (index){
                    $(this).find("input").removeClass("input-error")
                })
            }

            return valid;
        }

        function validateProductNumber(productNumber){
            let valid = false;
            var response = fetch("/api/part-number/check-exists", {
                        method: 'POST',
                        body: JSON.stringify({ product_number: productNumber }),
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include the CSRF token in the request headers
                        },
                    })
                    // .then(response => response.text())
                    // .then( data => {
                    //     if(parseInt(data) === 1){
                    //         alert("hehe")
                    //         valid = true;
                    //     }
                    // })
            return response
        }

        function addModal(count){
            var current = $("#modalContainer").html();
            $('#modalContainer').html(current + 
                '<div id="searchPartNumberModal-'+ count +'" class="modal js-modal modal__bg modalSs">'+
                        '<div class="modal__content modal_fix_width">'+
                            '<button type="button" class="modalCloseBtn js-modal-close">x</button>'+
                            '<div class="modalInner">'+
                                '<form action="#" accept-charset="utf-8">'+
                                    '<div class="section">'+
                                        '<div class="boxModal mb-1">'+
                                            '<div class="mr-0">'+
                                                '<label class="form-label dotted indented label_for">製品品番選択</label>'+
                                                '<div class="flex searchModal">'+
                                                    '<input type="hidden" id="model" value="ProductNumber">'+
                                                    '<input type="hidden" id="searchLabel" value="製品品番一覧">'+
                                                    '<input type="hidden" id="query" value="">'+
                                                    '<input type="hidden" id="reference" value="">'+
                                                    '<input type="text" class="w-100 mr-half"'+
                                                        'placeholder="検索キーワードを入力"'+
                                                        'name="keyword">'+
                                                    '<ul class="searchResult"'+
                                                        'id="search-result"'+
                                                        'data-result-value-element="_product-number-'+ count +'"'+
                                                        'data-result-name-element="_product-name-'+ count +'">'+
                                                    '</ul>'+
                                                    '<div class="clear">'+
                                                        '<button'+
                                                            'type="button"'+
                                                            'id="clear"'+
                                                            'class="clear-button"'+
                                                            'data-result-value-element="_product-number-'+ count +'"'+
                                                            'data-result-name-element="_product-name-'+ count +'">'+
                                                            '選択した値をクリアする'+
                                                        '</button>'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</form>'+
                            '</div>'+
                        '</div>'+
                    '</div>'
                )
        }

    </script>
@endpush