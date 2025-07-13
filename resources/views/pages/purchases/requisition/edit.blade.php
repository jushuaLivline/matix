@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    <link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css">
    <style>
        .calendar-plugin input {
            text-align: left;
            width: 6rem !important;
        }

        .btnExport {
            cursor: pointer;
        }
    </style>
@vite('resources/css/sales/sale_plan_search.css') @endpush @section('title', '購買依頼入力')

@php
    $isEditable = request('edit', 'false') === 'true'; // Check if the `edit` param is set to true   
@endphp

@section('content')
<div class="content">
    <div class="contentInner">
        <div class="accordion">
            <h1><span>依頼内容編集</span></h1>
        </div>
        @if(session('success'))
            <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                <div style="text-align: left;">
                    <p style="font-size: 18px; color: #0d9c38">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="pagettlWrap">
            <h1><span>依頼内容編集</span></h1>
        </div>
        
        <form action="{{ route('purchase.requisition.update', $purchaseRequisition->id) }}" method="POST" accept-charset="utf-8" class="mt-4 overlayedSubmitForm with-js-validation">
            @csrf 
            @method('PUT')
            <input type="hidden" name="state_classification" value=" {{ $purchaseRequisition->state_classification }}">
            <input type="hidden" name="requisition_number" value=" {{ $purchaseRequisition->requisition_number }}">
           
            <div class="box mb-4">
                <!-- 部門・ラインセクション -->
                <div class="mb-4 d-flex-col space-y-4">
                    <div class="mb-4">
                        <label class="form-label dotted indented">部門</label> <span class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" name="department_code"
                                   id="department_code" style="margin-right: 10px; width: 100px; ime-mode: disabled"
                                   data-field-name="部門"
                                   data-validate-exist-model="Department"
                                   data-validate-exist-column="code"
                                   data-inputautosearch-model="Department"
                                   data-inputautosearch-column="code"
                                   data-inputautosearch-return="name"
                                   data-inputautosearch-reference="department_name"
                                   class="text-left acceptNumericOnly"
                                   minlength="6"
                                   maxlength="6"
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   value="{{ $purchaseRequisition->department_code }}"
                                   @if (!$isEditable) readonly @endif
                                   required>
                            <input type="text" readonly
                                   name="department_name"
                                   id="department_name" style="margin-right: 10px; width: 290px;"
                                   value="{{ $purchaseRequisition->department?->name }}"
                                   class="middle-name text-left">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchDepartmentModal" @if (!$isEditable) disabled @endif>
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                     alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div data-error-container="department_code"></div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label dotted indented">ライン</label>
                        <div class="d-flex">
                            <input type="text" name="line_code"
                                    data-field-name="ライン"
                                   data-validate-exist-model="Line"
                                   data-validate-exist-column="line_code"
                                   data-inputautosearch-model="line"
                                   data-inputautosearch-column="line_code"
                                   data-inputautosearch-return="line_name"
                                   data-inputautosearch-reference="line_name"
                                   id="line_code" style="margin-right: 10px; width: 100px"
                                   class="text-left acceptNumericOnly"
                                   minlength="3"
                                   maxlength="3"
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   value="{{ $purchaseRequisition->line_code }}"
                                   @if (!$isEditable) readonly @endif>
                            <input type="text" readonly
                                   name="line_name"
                                   id="line_name" style="margin-right: 10px; width: 290px;"
                                   value="{{ $purchaseRequisition->line?->line_name }}"
                                   class="middle-name text-left">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchLineModal" @if (!$isEditable) disabled @endif>
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                     alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div data-error-container="line_code"></div>
                    </div>
                </div>

                <!-- 品番・品名・規格セクション -->
                <div class="mb-4" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div>
                        <label class="form-label dotted indented">品番</label> <span
                            class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" id="part_number" 
                                   name="part_number" 
                                   data-field-name="品番"
                                   style="width: 100%;" 
                                   maxlength="100"
                                   value="{{ $purchaseRequisition->part_number }}"
                                   @if (!$isEditable) readonly @endif
                                   required>
                        </div>
                        <div data-error-container="part_number"></div>
                    </div>

                    <div>
                        <label class="form-label dotted indented">品名</label>
                        <div class="d-flex">
                            <input type="text" id="product_name" 
                                   name="product_name" 
                                   style="width: 100%;" 
                                   maxlength="100"
                                   value="{{ $purchaseRequisition->product_name }}"
                                   @if (!$isEditable) readonly @endif>
                        </div>
                    </div>

                    <div>
                        <label class="form-label dotted indented">規格</label>
                        <div class="d-flex">
                            <input type="text" id="standard" 
                                   name="standard" 
                                   style="width: 100%;" 
                                   maxlength="100"
                                   value="{{ $purchaseRequisition->standard }}"
                                   @if (!$isEditable) readonly @endif>
                        </div>
                    </div>
                </div>

                <!-- Continue with remaining sections following the same grid/layout pattern -->
                <!-- 発注先セクション -->
                <div class="mb-4">
                    <label class="form-label dotted indented">発注先</label> <span
                        class="others-frame btn-orange badge">必須</span>
                    <div class="d-flex">
                        <input type="text" name="supplier_code"
                               id="supplier_code" style="margin-right: 10px; width: 190px;"
                               data-field-name="発注先"
                               data-validate-exist-model="supplier"
                               data-validate-exist-column="customer_code"
                               data-inputautosearch-model="supplier"
                               data-inputautosearch-column="customer_code"
                               data-inputautosearch-return="supplier_name_abbreviation"
                               data-inputautosearch-reference="supplier_name"
                               class="text-left searchOnInput Supplier acceptNumericOnly"
                               value="{{ $purchaseRequisition->supplier_code }}"
                               @if (!$isEditable) readonly @endif
                               minlength="6"
                               maxlength="6"
                               onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                               required>
                        <input type="text" readonly
                               name="supplier_name"
                               id="supplier_name" style="margin-right: 10px; width: 200px;"
                               value="{{ $purchaseRequisition->supplier?->supplier_name_abbreviation }}"
                               class="middle-name text-left">
                        <button type="button" class="btnSubmitCustom js-modal-open"
                                data-target="searchSupplierModal" @if (!$isEditable) disabled @endif>
                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                 alt="magnifying_glass.svg">
                        </button>
                    </div>
                    <div data-error-container="supplier_code"></div>
                </div>

                <!-- 数量・単位セクション -->
                <div class="mb-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                    <div>
                        <label class="form-label dotted indented">数量</label> <span
                            class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="number" name="quantity"
                                   id="quantity" style="width: 100%;"
                                   class="text-left acceptNumericOnly"
                                   data-field-name="数量"
                                   value="{{ $purchaseRequisition->quantity }}"
                                   @if (!$isEditable) readonly @endif
                                   maxlength="9"
                                   data-accept-zero="true"
                                   onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                   required>
                        </div>
                        <div data-error-container="quantity"></div>
                    </div>

                    <div>
                        <label class="form-label dotted indented">単位</label>
                        <div class="d-flex">
                            <select name="unit_code" 
                                    id="unit_code" 
                                    style="width: 60%; height: 40px; background-color: {{ !$isEditable ? '#d9d9d9' : 'white' }} !important;" 
                                    @if (!$isEditable) disabled @endif>
                                @foreach ($codes as $code)
                                    <option value="{{ $code->code }}" @selected($code->code == $purchaseRequisition->unit_code)>
                                        {{ $code->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="mr-2">
                        <label class="form-label dotted indented">単価</label> <span
                        class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" name="unit_price" id="unit_price" style="margin-right: 10px; width: 190px;"
                                class="text-left acceptNumericOnly" value="{{ $purchaseRequisition->unit_price }}" @if (!$isEditable) readonly @endif
                                data-field-name="単価"
                                min="1"
                                minlength="1"
                                data-accept-zero="true"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"+
                                required>
                        </div>
                        <div data-error-container="unit_price"></div>
                    </div>
                    <div class="mr-3">
                        <label class="form-label dotted indented">金額</label>
                        <div class="d-flex">
                            <input type="text" readonly name="amount_of_money" id="amount_of_money"
                                style="margin-right: 10px;  width: 190px" value="{{ $purchaseRequisition->amount_of_money }}"
                                class="middle-name text-left" @if (!$isEditable) readonly @endif>
                        </div>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">購入理由</label>
                        <div class="d-flex">
                            <input type="text" id="reason" name="reason" style="width:600px; margin-right: 10px;"
                                value="{{ $purchaseRequisition->reason }}" @if (!$isEditable) readonly @endif>
                        </div>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">費目</label> <span
                            class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" name="expense_items" data-validate-exist-model="item"
                                data-field-name="費目"
                                data-validate-exist-column="expense_item" data-inputautosearch-model="item"
                                data-inputautosearch-column="expense_item" data-inputautosearch-return="item_name"
                                data-inputautosearch-reference="expense_item_name" id="expense_item_code"
                                style="margin-right: 10px; width: 100px;" class="text-left searchOnInput Item acceptNumericOnly"
                                minlength="3"
                                maxlength="3"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="{{ $purchaseRequisition->expense_items }}" @if (!$isEditable) readonly @endif required>
                            <input type="text" readonly name="expense_item_name" id="expense_item_name"
                                style="margin-right: 10px; width: 290px;" value="{{ $purchaseRequisition->expense?->item_name }}"
                                class="middle-name text-left">
                            <button type="button" class="btnSubmitCustom js-modal-open" data-target="searchItemModal" @if (!$isEditable) disabled @endif>
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}" alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div data-error-container="expense_items"></div>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="mb-4">
                        <label class="form-label dotted indented">納期</label>
                        <div class="d-flex">
                            @include('partials._date_picker', ['inputName' => 'deadline', 
                            "value" => $purchaseRequisition->deadline?->format("Ymd"), 'isEditable', $isEditable, 
                            'disabledPreviousDates' => true,
                            'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=納期'])
                        </div>
                        <div id="request_error_message"></div>
                    </div>
                    <div class="mr-3">
                        <label class="form-label dotted indented mt-2">見積書</label>
                        <div class="d-flex">
                            @foreach($quotationExistenceFlags as $index => $quotationExistenceFlag)
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="quotation_existence_flag" value="{{ $index }}" {{ ($purchaseRequisition->quotation_existence_flag ?? 0 ) == $index ? 'checked' : '' }} @if (!$isEditable) disabled @endif >
                                        <span>{{ $quotationExistenceFlag }}</span>
                                    </label>
                            </p> @endforeach
                            {{-- <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="quotation" value="2" {{ (request()->quotation ?? 1) == 2 ? 'checked' : '' }}>
                            <span>有り</span>
                            </label>
                            </p> --}}
                        </div>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">備考</label>
                        <div class="d-flex">
                            <input type="text" id="remarks" name="remarks" style="width:600px; margin-right: 10px;"
                                value="{{ $purchaseRequisition->remarks }}" @if (!$isEditable) readonly @endif >
                        </div>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">承認方法</label>
                        <div class="d-flex">
                            @foreach($approvalMethods as $index => $approvalMethod)
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="approval_method_category" value="{{ $index }}" {{ ($purchaseRequisition->approval_method_category ?? 1) == $index ? 'checked' : '' }}
                                        @if (!$isEditable) disabled @endif>
                                        <span>{{ $approvalMethod }}</span>
                                    </label>
                            </p> @endforeach
                        </div>
                    </div>
                </div>
                <div class="mb-4 mr-3" id="approval-form-container" @if($purchaseRequisition->approval_method_category != 1)style="display:none" @endif>
                  <label class="form-label dotted indented">承認ルート</label>
                  @if($isEditable)
                        <div class="d-flex">
                            <select name="approval_route_number" id="approval_route_number" style="width: 250px; margin-right: 10px;" @if (!$isEditable) disabled @endif
                            data-approval-route_number="{{ $purchaseRequisition->approval_route_number }}"></select>
                            <button type="button" class="btn btn-blue js-modal-open"
                                    data-target="approvalModal" style="padding-top: 10px;" id="open_approval_modal"
                                    @if (!$isEditable) disabled @endif >
                            承認ルート設定
                            </button>
                        </div>
                    @else
                        <div class="mr-3 d-flex">
                            <table class="table table-bordered text-center" style="width: auto;">
                                <tbody>
                                    <tr height="100">
                                        @foreach ($purchaseRequisition->approvals as $approval)
                                            <td width="150" class="approval-row cursor-default">
                                                @if ($approval->denial_date)
                                                    <p style="margin-top: 20px; color: red; font-weight: bold !important;"
                                                        class="status_name"> 否認 </p>
                                                    <p style="margin-top: 10px;">
                                                        {{ date('Y/m/d', strtotime($approval->denial_date)) }}
                                                    </p>
                                                @elseif ($approval->approval_date)
                                                    <p style="margin-top: 20px; color: green; font-weight: bold !important;"
                                                        class="status_name"> 承認 </p>
                                                    <p style="margin-top: 10px;"></p> 
                                                @else
                                                    <p style="margin-top: 20px; color: red; font-weight: bold !important;"
                                                        class="status_name"> 未承認 </p>
                                                    <p style="margin-top: 10px;"></p>
                                                @endif 
                                                <p style="margin-top: 10px;">
                                                    {{ $approval->employee->employee_name ?? '' }}
                                                </p>
                                        </td> @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            <div class="btnListContainer">
                <div class="justify-content-flex-end">
                    <div class="btnContainerMainRight">
                        @if ($purchaseRequisition->approval_method_category == 2)
                            <a href="{{ url('/purchase/requisition/' . $purchaseRequisition->id . '/export?type=pdf') }}" 
                                class="btn btn-green btn-success">
                                依頼書出力
                            </a>
                        @endif
                         
                        <a href="{{route('purchase.requisition.index',[
                            'request_date_from' => now()->startOfMonth()->format('Ymd'),
                            'request_date_to' => now()->endOfMonth()->format('Ymd'),
                        ])}}" class="btn btn-blue btn-primary">一覧に戻る</a>
                        @php
                            $isApprovalMethodCategory2 = in_array($purchaseRequisition->approval_method_category, ['2']);
                            $isApprovalMethodCategory1Or2 = in_array($purchaseRequisition->approval_method_category, ['1', '2']);
                            $isEditNotSet = !isset($requestData['edit']);
                            $isStateClassificationValid = in_array($purchaseRequisition->state_classification, ['2', '3', '4']);
                        @endphp

                        @if ($isApprovalMethodCategory2 && $isEditNotSet)
                            {{-- <button type="button" class="btn btn-success">依頼書出力</button> --}}
                        @endif

                        @if ($isApprovalMethodCategory2 && $isEditNotSet)
                            <a href="?edit=true" class="btn btn-blue btn-primary">修正</a>
                        @endif

                        @if ($isApprovalMethodCategory2 && $isEditNotSet)
                            <a href="{{ route('purchase.requisition.create') }}" class="btn btn-blue btn-primary">新規入力</a>
                        @endif
                        @if ($purchaseRequisition->approval_method_category == '1')
                            @if (!request()->has('edit'))
                                @if (in_array($purchaseRequisition->state_classification, [0, 1, 9]) && $isEditNotSet)
                                    <a href="?edit=true" class="btn btn-blue btn-primary">編集</a>
                                @else
                                    <button type="button" class="btn btn-blue btn-disabled" disabled>編集</button>
                                @endif
                            @endif
                        @endif
                        @if ($isEditable)
                            <button type="button" id="clearButton" class="btn btn-blue btn-primary"> クリア </button>
                            <a href="{{ route('purchase.requisition.create', ['clone' => $purchaseRequisition->id]) }}" class="btn btn-blue">複写入力</a>
                            <button type="submit" class="btn btn-green" onclick="return confirm('購買依頼内容を更新します、よろしいでしょうか？');">この内容で更新する </button>
                        @endif 
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="approvalRouteModalStorage">
        @php
          $search_img = asset('images/icons/magnifying_glass.svg');
        @endphp
    </div>

    @include('partials.modals.approval_modal')
    @include('partials.modals.create_approval_modal')
    @include('partials.modals.update_approval_modal')

    @include('partials.modals.masters._search', [
    'modalId' => 'searchDepartmentModal',
    'searchLabel' => '部門',
    'resultValueElementId' => 'department_code',
    'resultNameElementId' => 'department_name',
    'model' => 'Department'
])
    @include('partials.modals.masters._search', [
    'modalId' => 'searchLineModal',
    'searchLabel' => 'ライン',
    'resultValueElementId' => 'line_code',
    'resultNameElementId' => 'line_name',
    'model' => 'Line'
])
    @include('partials.modals.masters._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '発注先',
    'resultValueElementId' => 'supplier_code',
    'resultNameElementId' => 'supplier_name',
    'model' => 'Supplier'
])
    @include('partials.modals.masters._search', [
    'modalId' => 'searchItemModal',
    'searchLabel' => '費目',
    'resultValueElementId' => 'expense_item_code',
    'resultNameElementId' => 'expense_item_name',
    'model' => 'Item'
])
    @include('partials.modals.masters._search', [
    'modalId' => 'searchEmployeeModal',
    'searchLabel' => '承認者',
    'resultValueElementId' => 'employee_code',
    'resultNameElementId' => 'employee_name',
    'model' => 'Employee'
]) 
@endsection 

@push('scripts')
    <script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
  <script>
      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      var count = 0;
      // dom loaded
      @if(Request::get("clone"))
          $.ajax({
              url: "/api/purchase/purchase-requisition-input/" + {{ Request::get("clone") }} ,
              type: 'GET',
              headers: {
              'X-CSRF-TOKEN': token
              },
              success: function(response) {
                  populateInputFields(response);
              },
              error: function(xhr, status, error) {
              }
          });

          function populateInputFields(response) {
              var responseData = response;

              for (var key in responseData) {
                  if (responseData.hasOwnProperty(key)) {
                      var value = responseData[key];
                      $(`.input[name="${key}"`).prop('checked', false);
                      $('input[value="' + value + '"]').prop('checked', true);

                      if(key == "approval_method_category"){
                          if(value == 2){
                              $("#approval-form-container").hide()
                          }else{
                              $("#approval-form-container").show()
                          }
                      }
                      var inputField = document.getElementById(key);
                      if (inputField) {
                          inputField.value = value;
                      }
                  }
              }
          }
      @endif

      $('input[name="approval_method_category"]').click(function(){
          var inputValue = $(this).attr("value");
          if(inputValue == 2){
              $("#approval-form-container").hide()
          }else{
              $("#approval-form-container").show()
          }
      });

      $(function() {
          getApprovalRoute();
      })

      $(".close-modal-purchase").on("click", function () {
          $(this).parents('.modal__content').find('.js-modal-close').trigger('click');
      });
      $("#calculate_amount").on('click', function () {
          let amount = $("#unit_price").val() * $("#quantity").val();
          $("#amount_of_money").val(amount);
      });

      $("#open_approval_modal").on('click', function () {
          getApprovalRoute();
      });

      $("#createApprovalRouteButton").on('click', function () {
          clearCreateApprovalRouteRow();
          addCreateApprovalRouteRow();
          validationMessage();
      });

      $("#create-approval-route-body").on("click", ".row-down", function () {
          var tableRow = $(this).parents("tr");

          if (! tableRow.next().children('.hidden-part').hasClass('d-none')) {
              tableRow.insertAfter(tableRow.next());
              createApprovalRouteBodyOrdering()
          }
      });

      $("#create-approval-route-body").on("click", ".row-up", function () {
          var tableRow = $(this).parents("tr");
          tableRow.insertBefore(tableRow.prev());
          createApprovalRouteBodyOrdering()
      });

      $("#update-approval-route-body").on("click", ".row-down", function () {
          var tableRow = $(this).parents("tr");
          if (! tableRow.next().children('.hidden-part').hasClass('d-none')) {
              tableRow.insertAfter(tableRow.next());
              updateApprovalRouteBodyOrdering()
          }
      });

      $("#update-approval-route-body").on("click", ".row-up", function () {
          var tableRow = $(this).parents("tr");
          tableRow.insertBefore(tableRow.prev());
          updateApprovalRouteBodyOrdering()
      });

      $("#approval-route-body").on("click", ".row-down", function () {
          var tableRow = $(this).parents("tr");
          if (! tableRow.next().children('.hidden-part').hasClass('d-none')) {
              tableRow.insertAfter(tableRow.next());
              $.ajax({
                  type: 'POST',
                  url: '{{ route("purchase.reorderApprovalRoute") }}',
                  data : {id: $(this).attr('data-id'), type: 'down'},
                  headers: {
                      'X-CSRF-TOKEN': token
                  },
                  success: function(data) {
                      getApprovalRoute();
                  },
                  error: function (jqXHR, textStatus, errorThrown) {
                      // window.location.reload(true);
                  }
              });
          }
      });

      $("#approval-route-body").on("click", ".row-up", function () {
          var tableRow = $(this).parents("tr");
          tableRow.insertBefore(tableRow.prev());
          $.ajax({
              type: 'POST',
              url: '{{ route("purchase.reorderApprovalRoute") }}',
              data : {id: $(this).attr('data-id'), type: 'up'},
              headers: {
                  'X-CSRF-TOKEN': token
              },
              success: function(data) {
                  getApprovalRoute();
              },
              error: function (jqXHR, textStatus, errorThrown) {
                  // window.location.reload(true);
              }
          });
      });
      $("#approval-route-body").on("click", ".approval-route-update-row", function () {
          $("#update-id").val($(this).attr('data-id'));
          // $("#update-approval_route_name").val($(this).parent("td").siblings("#route_name").html());
          fetch('{{ route("purchase.getApprovalRouteDetails") }}' + "/" + $(this).attr('data-id'), {
              method: 'GET',
              headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
          })
          .then(response => response.json())
          .then(data => {
              $("#update-approval_route_name").val(data.data['approval_route_name']);
              $("#update-approval-route-body").html("");
              var html = "";
              $.each(data.details.data, function (index, value) {
                  count+=1;
                  html += '<tr>' +
                              '<td>' +
                              '<span class="pt-2 display_number">' + value['order_of_approval'] + '</span>'+
                              '</td>' +
                              '<td>' +
                              '    <div class="d-flex">' +
                              '        <input type="text" name="update_employee_codes"' +
                              '            id="update-employee_codes-'+value['order_of_approval']+'"' +
                              '            class="text-left mr-5c" maxlength="6"' +
                              '            value="' + value['employee'].data['code'] + '">' +
                              '        <input type="text" readonly' +
                              '            name="employee_name"' +
                              '            id="employee_name-'+value['order_of_approval']+'"' +
                              '            value="' + value['employee'].data['name'] + '"' +
                              '            class="middle-name text-left mr-5c">' +
                              '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                              '                data-target="searchLineModal-'+value['order_of_approval']+'">' +
                              '            <img src="{{ $search_img }}"' +
                              '                alt="magnifying_glass.svg">' +
                              '        </button>' +
                              '    </div>' +
                              '</td>' +
                              '<td id="button-cell-'+value['order_of_approval']+'" class="d-none">' +
                              '    <button type="button" class="btn btn-green update-approval-route-save-row" style="width: 47%" data-hold="'+value['order_of_approval']+'">' +
                              '        追加' +
                              '    </button>' +
                              '<button type="button" class="btn btn-gray ml-5c" style="width: 47%;">クリア</button>'+
                              '</td>' +
                              '<td id="hidden-button-cell-'+value['order_of_approval']+'" class="hidden-part">'+
                              '    <button type="button" class="btn btn-orange update-approval-route-delete-row" data-id="' + value['id'] +'" style="width: 47%">'+
                              '        削除'+ 
                              '    </button>'+
                              '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+value['approval_route_no']+'">↓</button>'+
                              '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+value['approval_route_no']+'">↑</button>'+
                              '</td>'+
                          '</tr>';
                          addNewModal();
              });

              count++

              html += '<tr>' +
                      '<td>' +
                          '<span class="pt-2 display_number"></span>'+
                      '</td>' +
                      '<td>' +
                      '    <div class="d-flex">' +
                      '        <input data-test3 type="text" name="update_employee_codes"' +
                      '            id="employee_codes-'+count+'" style="margin-right: 5px;"' +
                      '            class="text-left" maxlength="6"' +
                      '            data-validate-exist-model="employee"' +
                      '           data-validate-exist-column="employee_code"' +
                      '           data-inputautosearch-model="employee"' +
                      '           data-inputautosearch-column="employee_code"' +
                      '           data-inputautosearch-return="employee_name"' +
                      '           data-inputautosearch-reference="employee_name-'+count+'"' +
                      '           data-inputautosearch-counter="'+count+'"' +
                      '            data-modal-autosearch>' +
                      '        <input type="text" readonly' +
                      '            name="employee_name1"' +
                      '            id="employee_name-'+count+'" style="margin-right: 5px;"' +
                      '            value=""' +
                      '            class="middle-name text-left mr-5c">' +
                      '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                      '                data-target="searchLineModal-'+count+'">' +
                      '            <img src="{{ $search_img }}"' +
                      '                alt="magnifying_glass.svg">' +
                      '        </button>' +
                      '    </div>' +
                      '    <div data-error-container="employee_codes-'+count+'" class="text-left employee_error_message" data-required-message="{{ config("messages.validations.required") }}"></div>' +
                      '</td>' +
                      '<td id="button-cell-'+count+'" class="">' +
                      '    <button type="button" class="btn btn-green update-approval-route-save-row" style="width: 47%" data-hold="'+count+'">' +
                      '        追加' +
                      '    </button>' +
                          '<button type="button" class="btn btn-gray update-ar-clear-button create-ar-clear-button" style="width: 47%; margin-left: 5px">クリア</button>'+
                      '</td>' +
                      '<td id="hidden-button-cell-'+count+'" class="d-none hidden-part">'+
                      '    <button type="button" class="btn btn-orange update-approval-route-delete-row" style="width: 47%">'+
                      '        削除'+
                      '    </button>'+
                      '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+count+'">↓</button>'+
                      '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+count+'">↑</button>'+
                      '</td>'+
                      '</tr>';
                  addNewModal();
                  
              $("#update-approval-route-body").html(html);
          })
          .catch(error => console.error('Error:', error));
          
      });
      
      $("#create-approval-route-body").on("click", ".create-approval-route-delete-row", function () {
          var tableRow = $(this).parents("tr");
          tableRow.remove();
      });

      $("#update-approval-route-body").on("click", ".update-approval-route-delete-row", function () {
          var tableRow = $(this).parents("tr");
          var confirmationMessage = confirm("削除しますか。");
          if(confirmationMessage){
              var id = $(this).data("id");
              if(id){
                  fetch('/purchase/approval-route-detail/' + id, {
                      method: 'DELETE',
                      headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                  })
                  .then(response => {
                      // tableRow.remove();
                  })
              }
              tableRow.remove();
              updateApprovalRouteBodyOrdering();
              validationMessage();
          }
      });
      
      $("#approval-route-body").on("click", ".delete-route", function () {
          var tableRow = $(this).parents("tr");
          var id = $(this).data("id")
          var confirmMessage = confirm("削除しますか。")
          if(confirmMessage){
              fetch('/purchase/approval-route-list/' + id, {
                      method: 'DELETE',
                      headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                  })
                  .then(response => {
                      tableRow.remove();
                      getApprovalRoute();
                  })
          }
      });
      
      $("#update-approval-route-body").on( 'click', '.update-approval-route-save-row', function (e) {
          var hasInputWithNoValue = false;
          $("#update-approval-route-body tr").each( function (index){
              if($(this).find("input").val().trim() === ''){
                  hasInputWithNoValue = true;
              }
          })
          const errorMessages = $('.employee_error_message.validation-error-message');
          const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
          if(lastErrorMessageElement.text().trim() !== "" ) {
              hasInputWithNoValue = true;;
          }

          if(hasInputWithNoValue){
              $("#update-approval-route-body tr").each( function (index){
                  $(this).find("td").each(function(){
                      $(this).find("input").each(function(){
                          if($(this).val().trim() === ''){
                              $(this).addClass("border-danger")
                          }
                      })
                  })
              })
              return;
          }else{
              $("#update-approval-route-body tr").each( function (index){
                  $(this).find("td").each(function(){
                      $(this).find("input").each(function(){
                          if($(this).val().trim() != ''){
                              $(this).removeClass("border-danger")
                          }
                      })
                  })
              })
          }

          addUpdateApprovalRouteRow();
          $("#button-cell-" + $(this).attr("data-hold")).addClass("d-none");
          $("#employee_codes-" + $(this).attr("data-hold")).attr("name", "update_employee_codes");
          $("#hidden-button-cell-" + $(this).attr("data-hold")).removeClass("d-none");
          updateApprovalRouteBodyOrdering();
      })

      $("#create-approval-route-body").on( 'click', '.create-approval-route-save-row', function (e) {
          var hasInputWithNoValue = false;
          $("#create-approval-route-body tr").each( function (index){
              if($(this).find("input").val().trim() === ''){
                  hasInputWithNoValue = true;
              }
          })
          
          const errorMessages = $('.employee_error_message.validation-error-message');
          const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
          if(lastErrorMessageElement.text().trim() !== "" ) {
              hasInputWithNoValue = true;;
          }

          if(hasInputWithNoValue){
              $("#create-approval-route-body tr").each( function (index){
                  $(this).find("td").each(function(){
                      $(this).find("input").each(function(){
                          if($(this).val().trim() === ''){
                              $(this).addClass("border-danger")
                          }
                      })
                  })
              })
              return;
          }else{
              $("#create-approval-route-body tr").each( function (index){
                  $(this).find("td").each(function(){
                      $(this).find("input").each(function(){
                          if($(this).val().trim() != ''){
                              $(this).removeClass("border-danger")
                          }
                      })
                  })
              })
          }
          addCreateApprovalRouteRow();
          $("#button-cell-" + $(this).attr("data-hold")).addClass("d-none");
          $("#employee_codes-" + $(this).attr("data-hold")).attr("name", "employee_codes[]");
          $("#hidden-button-cell-" + $(this).attr("data-hold")).removeClass("d-none");
          createApprovalRouteBodyOrdering()
      });
      
      function clearCreateApprovalRouteRow() {
          var current = $("#create-approval-route-body").html("");
          count = 0;
      }
      function addCreateApprovalRouteRow() {
          var current = $("#create-approval-route-body").html();
          count += 1;
          $("#create-approval-route-body").append('<tr>' +
                                  '<td>' +
                                  '<span class="pt-2 display_number"></span>'+
                                  '</td>' +
                                  '<td>' +
                                    '    <div class="d-flex">' +
                                  '        <input type="text" name="employee_codes"' +
                                  '            id="employee_codes-'+count+'" style="margin-right: 5px;"' +
                                  '            class="text-left"' +
                                  '            data-validate-exist-model="employee"' +
                                  '           data-validate-exist-column="employee_code"' +
                                  '           data-inputautosearch-model="employee"' +
                                  '           data-inputautosearch-column="employee_code"' +
                                  '           data-inputautosearch-return="employee_name"' +
                                  '           data-inputautosearch-reference="employee_name-'+count+'"' +
                                  '           data-inputautosearch-counter="'+count+'"' +
                                  '            value="" data-modal-autosearch>' +

                                  '        <input type="text" readonly' +
                                  '            name="employee_name1"' +
                                  '            id="employee_name-'+count+'" style="margin-right: 5px;"' +
                                  '            value=""' +
                                  '            class="middle-name text-left mr-5c">' +
                                  '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                                  '                data-target="searchLineModal-'+count+'">' +
                                  '            <img src="{{ $search_img }}"' +
                                  '                alt="magnifying_glass.svg">' +
                                  '        </button>' +
                                  '    </div>' +
                                  '       <div data-error-container="employee_codes-'+count+'" class="text-left employee_error_message" data-required-message="{{ config("messages.validations.required") }}"></div>' +
                                  '</td>' +
                                  '<td id="button-cell-'+count+'" class="">' +
                                  '    <button type="button" class="btn btn-green create-approval-route-save-row" style="width: 47%" data-hold="'+count+'">' +
                                  '        追加' +
                                  '    </button>' +
                                  '<button type="button" class="btn btn-gray create-ar-clear-button" style="width: 47%; margin-left: 5px">クリア</button>'+
                                  '</td>' +
                                  '<td id="hidden-button-cell-'+count+'" class="d-none hidden-part">'+
                                  '    <button type="button" class="btn btn-orange create-approval-route-delete-row" style="width: 47%">'+
                                  '        削除'+
                                  '    </button>'+
                                  '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+count+'">↓</button>'+
                                  '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+count+'">↑</button>'+
                                  '</td>'+
                                  '</tr>');
              addNewModal();
              
      }

      function updateApprovalRouteBodyOrdering(){
          $("#update-approval-route-body tr").not(':last').each( function (index){
              $(this).find(".display_number").text(index + 1)
          })
      }

      function createApprovalRouteBodyOrdering(){
          $("#create-approval-route-body tr").not(':last').each( function (index){
              $(this).find(".display_number").text(index + 1)
          })
      }

      function addUpdateApprovalRouteRow() {
          var current = $("#update-approval-route-body").html();
          count += 2;
          $("#update-approval-route-body").append('<tr>' +
                                  '<td>' +
                                  '<span class="pt-2 display_number"></span>'+
                                  '</td>' +
                                  '<td>' +
                                  '    <div class="d-flex">' +
                                  '        <input data-test type="text" name="update_employee_codes"' +
                                  '            id="employee_codes-'+count+'" style="margin-right: 5px;"' +
                                  '            class="text-left" maxlength="6"' +
                                  '            data-validate-exist-model="employee"' +
                                  '           data-validate-exist-column="employee_code"' +
                                  '           data-inputautosearch-model="employee"' +
                                  '           data-inputautosearch-column="employee_code"' +
                                  '           data-inputautosearch-return="employee_name"' +
                                  '           data-inputautosearch-reference="employee_name-'+count+'"' +
                                  '           data-inputautosearch-counter="'+count+'"' +
                                  '            value="" data-modal-autosearch>' +
                                  '        <input type="text" readonly' +
                                  '            name="employee_name"' +
                                  '            id="employee_name-'+count+'"' +
                                  '            value=""' +
                                  '            class="middle-name text-left mr-5c">' +
                                  '        <button type="button" class="btnSubmitCustom js-modal-open"' +
                                  '                data-target="searchLineModal-'+count+'">' +
                                  '            <img src="{{ $search_img }}"' +
                                  '                alt="magnifying_glass.svg">' +
                                  '        </button>' +
                                  '    </div>' +
                                  '    <div data-error-container="employee_codes-'+count+'" class="text-left employee_error_message" data-required-message="{{ config("messages.validations.required") }}"></div>' +
                                  '</td>' +
                                  '<td id="button-cell-'+count+'" class="">' +
                                  '    <button type="button" class="btn btn-green update-approval-route-save-row" style="width: 47%" data-hold="'+count+'">' +
                                  '        追加' +
                                  '    </button>' +
                                  '<button type="button" class="btn btn-gray update-ar-clear-button create-ar-clear-button" style="width: 47%; margin-left: 5px">クリア</button>'+
                                  '</td>' +
                                  '<td id="hidden-button-cell-'+count+'" class="d-none hidden-part">'+
                                  '    <button type="button" class="btn btn-orange update-approval-route-delete-row" style="width: 47%">'+
                                  '        削除'+
                                  '    </button>'+
                                  '    <button type="button" class="btn btn-blue row-down" style="width: 23%" data-order="'+count+'">↓</button>'+
                                  '    <button type="button" class="btn btn-blue row-up" style="width: 23%" data-order="'+count+'">↑</button>'+
                                  '</td>'+
                                  '</tr>');
              addNewModal();
      }

      function addNewModal () {
          var current = $("#approvalRouteModalStorage").html();
          $("#approvalRouteModalStorage").html(current + '<div id="searchLineModal-'+count+'" class="modal js-modal modal__bg modalSs">'+
          '    <div class="modal__content modal_fix_width">'+
          '        <button type="button" class="modalCloseBtn js-modal-close">x</button>'+
          '        <div class="modalInner">'+
          '            <form action="#" accept-charset="utf-8">'+
          '                <div class="section">'+
          '                    <div class="boxModal mb-1">'+
          '                        <div class="mr-0">'+
          '                            <label class="form-label dotted indented label_for">社員選択</label>'+
          '                            <div class="flex searchModal">'+
          '                                <input type="hidden" id="model" value="Employee">'+
          '                                <input type="hidden" id="searchLabel" value="ライン一覧">'+
          '                                <input type="hidden" id="query" value="">'+
          '                                <input type="hidden" id="reference" value="">'+
          '                                <input type="text" class="w-100 mr-half" placeholder="検索キーワードを入力" name="keyword">'+
          '                                <ul class="searchResult" id="search-result" data-result-value-element="employee_codes-'+count+'" data-result-name-element="employee_name-'+count+'">'+
          '                                </ul>'+
          '                                <div class="clear">'+
          '                                    <button type="button" id="clear" class="clear-button" data-result-value-element="employee_codes-'+count+'" data-result-name-element="employee_name-'+count+'">'+
          '                                        選択した値をクリアする'+
          '                                    </button>'+
          '                                </div>'+
          '                            </div>'+
          '                        </div>'+
          '                    </div>'+
          '                </div>'+
          '            </form>'+
          '        </div>'+
          '    </div>'+
          '</div>');

          clearButton();
          validationMessage();
      }
      function getApprovalRoute() {
          fetch('{{ route("purchase.getApprovalRouteList") }}?employee_code={{ Auth::user()->employee_code }}', {
              method: 'GET',
              headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
          })
              .then(response => response.json())
              .then(data => {
                  $("#approval-route-body").html("");
                  $("#approval_route_number").html("");
                  var html = "";
                  var html_dropdown = "";
                  const isSingleItem = data?.data?.length === 1;
                  var dataCounter = data?.data.length - 1;
                  var disabledButtonUp = isSingleItem ? "disabled" : "";
                  var disabledButtonDown = isSingleItem ? "" : "disabled";
                  var approvalRouteNumber = $("#approval_route_number").attr('data-approval-route_number');
                  
                  if(data?.data?.length == 0) {
                      html = '<tr><td colspan="4">「該当データがありません」</td></tr>';
                  }else{
                      $.each(data.data, function (index, value) {
                          if(data?.data.length > 1) {
                              disabledButtonUp = (index === 0) ? 'disabled' : "";
                              disabledButtonDown = (index === dataCounter) ? "disabled" : "";
                          }

                          html += "<tr>"+
                                      "<td>"+value['display_order']+"</td>"+
                                      "<td id='route_name'>"+value['approval_route_name']+"</td>"+
                                      "<td>"+value['details_count']+"</td>"+
                                      "<td><button type='button' data-id='"+value['id']+"' class='btn btn-blue approval-route-update-row js-modal-open' data-target='updateApprovalModal' style='width: 30%; margin-right: 5px;'>"+
                                      '編集'+
                                      '</button>'+
                                      '<button type="button" class="btn btn-blue row-down" style="width: 15%; margin-right: 5px;" data-id="'+value['id']+'" '+disabledButtonDown+'>↓</button>'+
                                      '<button type="button" class="btn btn-blue row-up" style="width: 15%; margin-right: 5px;" data-id="'+value['id']+'" '+disabledButtonUp+'>↑</button>'+
                                      '<button type="button" class="btn btn-orange delete-route" style="width: 30%" data-id="'+value['id']+'">削除</button></td>'+
                                  "</tr>";
                          // Check if the approval_route_no is equal to approvalRouteNumber
                        if (value['approval_route_no'] == approvalRouteNumber) {
                            // Add the option with the selected attribute
                            html_dropdown += "<option value='"+value['approval_route_no']+"' selected>"+value['approval_route_name']+"</option>";
                        } else {
                            // Add the option without the selected attribute
                            html_dropdown += "<option value='"+value['approval_route_no']+"'>"+value['approval_route_name']+"</option>";
                        }
                      });
                  }

                  $("#approval-route-body").html(html);
                  $("#approval_route_number").html(html_dropdown);
              })
              .catch(error => console.error('Error:', error));
      }

      function clearButton() {
          setTimeout(function(){
              $('.create-ar-clear-button').each(function() {
                  $(this).on('click', function() {
                      $(this).parent().parent().find('.d-flex input[type="text"]').each(function(){
                          $(this).val("");
                          $(this).removeClass("validation-error-message");
                      })
                      
                      $(this).parent().parent().find('.employee_error_message').text("")
                  });
              });
          }, 300);
      }

      function validationMessage() {
          $.ajax({
              url: "/api/validation-messages",
              type: 'GET',
              headers: {
              'X-CSRF-TOKEN': token
              },
              success: function(response) {
                  const validationMessages = response 
                  $('.with-js-validation-modal').each(function(){
                      var form = $(this);
                      
                      $(this).find("input[data-modal-autosearch]").each( function () {
                          const model = $(this).data('inputautosearch-model');
                          const column = $(this).data('inputautosearch-column');
                          const columnReturn = $(this).data('inputautosearch-return');
                          const reference = $(this).data('inputautosearch-reference');
                          const counter = $(this).data('inputautosearch-counter');
                          let debounceTimeout; 
                          
                          $(this).keyup(function(){
                              const errorMessageElement = $(`[data-error-container="employee_codes-${counter}"]`);
                              var inputElement = $(this);
                              var inputValue = inputElement.val();
                              

                              // Clear the previous debounce timer
                              clearTimeout(debounceTimeout);
                              
                              // Set a new debounce timer
                              debounceTimeout = setTimeout(function () {

                                  if(inputValue.trim() == '') {
                                      inputElement.addClass("validation-error-message")
                                      errorMessageElement.addClass("validation-error-message").text(validationMessages.required)
                                  }else{
                                      $.ajax({
                                          type: 'POST',
                                          url: '/api/lookup-autosearch',
                                          data : {
                                              name: columnReturn,
                                              model: model,
                                              column: column,
                                              searchValue: inputValue
                                          },
                                          headers: {
                                              'X-CSRF-TOKEN': token
                                          },
                                          success: function(response) {
                                              errorMessageElement.removeClass("validation-error-message").text("")
                                              inputElement.removeClass("validation-error-message")
                                              if(response.value == '') {
                                                  inputElement.addClass("validation-error-message")
                                                  errorMessageElement.addClass("validation-error-message").text(validationMessages.remote)
                                                  form.find(`#${reference}`).val("")
                                              }else{
                                                  form.find(`#${reference}`).val(response.value)
                                              }
                                          },
                                          error: function (jqXHR, textStatus, errorThrown) {
                                              console.log(error)
                                          }
                                      });
                                  }
                              }, 300); 
                              
                          });
                      })

                  });
              },
              error: function(xhr, status, error) {
                  console.error('Error:', error);
              }
          });

      }
      $('#createApprovalRouteForm').validate({
          rules: {
              approval_route_name: {
                  required: true
              }
          },
          messages: {
              approval_route_name: {
                  required: '入力してください'
              }
          },
          errorElement : 'div',
          errorPlacement: function(error, element) {
              $(element).parents(".formBody").find('.error_msg').html(error)
          },
          invalidHandler: function(event, validator) {
              $('.submit-overlay').css('display', "none");
          },
          submitHandler: function(form) {
              var values = $("input[name^='employee_codes']").map(function (idx, ele) {
                  return $(ele).val();
              }).get();

              const errorMessages = $('.employee_error_message.validation-error-message');
              const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
              // Disabled the form submission when there are error
              if(lastErrorMessageElement.text().trim() !== "" ) {
                  return;
              }

              const errorMessageElement = $(`[data-error-container="employee_codes-1"]`);
              const employeeCodeInput = $('#employee_codes-1');
              // Disabled the form submission when there are error
              if (values.length === 1 && values[0] === "") {
                  const requiredMessage = errorMessageElement.data('required-message');
                  employeeCodeInput.addClass('validation-error-message');
                  errorMessageElement.addClass('validation-error-message').text(requiredMessage);
                  return;
              }


              // Reset error message styles
              errorMessageElement.removeClass('validation-error-message').text("");
              employeeCodeInput.removeClass('validation-error-message');

              var name = $("#approval_route_name").val();

              $.ajax({
                  type: 'POST',
                  url: '{{ route("purchase.saveApprovalRoute") }}',
                  data : {
                          values: values, 
                          name: name, 
                          employee_code: '{{ Auth::user()->employee_code }}' },
                  headers: {
                      'X-CSRF-TOKEN': token
                  },
                  success: function(data) {
                      getApprovalRoute();
                      $("#createApprovalModal .js-modal-close").trigger('click');
                      $("#approval_route_name").val("");
                      // $(this).parents('.modal__content').find('.js-modal-close').trigger('click');
                  },
                  error: function (jqXHR, textStatus, errorThrown) {
                      // window.location.reload(true);
                  }
              });
          }
      });
      $('#updateApprovalRouteForm').validate({
          rules: {
              approval_route_name: {
                  required: true
              },
          },
          messages: {
              approval_route_name: {
                  required: '入力してください'
              },
          },
          errorElement : 'div',
          errorPlacement: function(error, element) {
              $(element).parents(".formBody").find('.error_msg').html(error)
          },
          invalidHandler: function(event, validator) {
              $('.submit-overlay').css('display', "none");
          },
          submitHandler: function(form) {
              var values = $("input[name^='update_employee_codes']").map(function (idx, ele) {
                  var value = $(ele).val();
                  if(value){
                      return $(ele).val();
                  }
              }).get();
              
              const errorMessages = $('.employee_error_message.validation-error-message');
              const lastErrorMessageElement = errorMessages.last(); // Gets the last element directly
              if(lastErrorMessageElement.text().trim() !== "" ) {
                  return;
              }

              var name = $("#update-approval_route_name").val();

              $.ajax({
                  type: 'POST',
                  url: '{{ route("purchase.updateApprovalRoute") }}',
                  data : {update_id: $("#update-id").val(), values: values, name: name},
                  headers: {
                      'X-CSRF-TOKEN': token
                  },
                  success: function(data) {
                      getApprovalRoute();
                      $("#updateApprovalModal .js-modal-close").trigger('click');
                  },
                  error: function (jqXHR, textStatus, errorThrown) {
                      // window.location.reload(true);
                  }
              });
          }
      });
    document.addEventListener('DOMContentLoaded', function() {
        // 必要な要素を取得
        const quantityInput = document.getElementById('quantity');
        const unitPriceInput = document.querySelector('input[name="unit_price"]');
        const amountInput = document.getElementById('amount_of_money');

        // 金額を計算する関数
        function calculateAmount() {
            const quantity = parseInt(quantityInput.value) || 0;
            const unitPrice = parseInt(unitPriceInput.value) || 0;
            const amount = quantity * unitPrice;
            
            // 計算結果を金額フィールドに設定
            amountInput.value = amount;
        }

        // 発注数と単価の入力イベントにリスナーを追加
        quantityInput.addEventListener('input', calculateAmount);
        unitPriceInput.addEventListener('input', calculateAmount);
    });
  </script>
@endpush
