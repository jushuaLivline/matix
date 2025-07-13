<div id="print_modal" class="modal js-modal modal__bg">
    <div class="modal__content " style="width: 800px;">
        <button type="button" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner" style="" id="modalContent">
            <div class="content" style="">
                <div class="contentInner">
                    <div class="pagettlWrap">
                        <h1><span>かんばん印刷</span></h1>
                    </div>
        
                    {{-- <form action="#" accept-charset="utf-8" style="min-width: 800px; max-width: 800px"> --}}
                        <div class="tableWrap borderLesstable inputFormAreaCustomer" 
                            style="
                                
                                margin-top: 0.5rem;
                                background-color: #ffffff;
                                padding: 10px;
                                box-sizing: border-box;
                                border: #cccccc solid 1px;"
                            >
                            <table class="tableBasic" style="width: 100%;">
                                <tbody>
                                    {{-- printing_surface --}}
                                    <tr>
                                        <td style="width: 25%">
                                            <dl class="formsetBox">
                                                <dt class="">印刷面</dt>
                                            </dl>
                                        </td>
                                        <td>
                                            <div style="display: inline-flex; width: 100%;">
                                                <input type="radio" id="printing_surface1" name="printing_surface" style="min-width:30px; margin-left: 0px;"  value="1">
                                                <label for="printing_surface1" style="min-width: 100px; text-align: left">ﾊﾞｰｺｰﾄﾞ面</label>
                                            
                                                <input type="radio" id="printing_surface2" name="printing_surface" style="min-width:30px;" value="2">
                                                <label for="printing_surface2" style="min-width: 100px; text-align: left">QRｺｰﾄﾞ面</label>
                                            </div>
                                            <div class="error_msg"></div>
                                        </td>
                                    </tr>
                                    {{-- serial_number --}}
                                    <tr>
                                        <td style="width: 25%">
                                            <dl class="formsetBox">
                                                <dt class="requiredForm">発行開始連番</dt>
                                            </dl>
                                        </td>
                                        <td>
                                            <dd>
                                                <p class="formPack">
                                                    <input type="text" id="serial_number" name="serial_number" value="" style="max-width: 100px" required>
                                                </p>
                                                @error('serial_number')
                                                    <span class="err_msg">{{ $message }}</span>
                                                @enderror
                                            </dd>
                                        </td>
                                    </tr>
                                    {{-- number_of_sheets --}}
                                    <tr>
                                        <td style="width: 25%">
                                            <dl class="formsetBox">
                                                <dt class="requiredForm">印刷枚数</dt>
                                            </dl>
                                        </td>
                                        <td>
                                            <dd>
                                                <p class="formPack">
                                                    <input type="text" id="number_of_sheets" name="number_of_sheets" value="" style="max-width: 100px" required>
                                                </p>
                                                @error('number_of_sheets')
                                                    <span class="err_msg">{{ $message }}</span>
                                                @enderror
                                            </dd>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <div class="buttonRow" style="display: flex; justify-content: flex-end;">
                                <div style="display: flex; justify-content: flex-end;">
                                    <div>
                                        <button type="button" class="btn_print" style="width: 180px; cursor: pointer;">
                                            印刷
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
</div>
{{-- products --}}
@include('partials.modals.masters.kanbans._search', [
    'modalId' => 'searchLineModal',
    'searchLabel' => 'ラインコード',
    'resultValueElementId' => 'line_code_',
    'resultNameElementId' => 'line_name_',
    'model' => 'Line',
])
@include('partials.modals.masters.kanbans._search', [
    'modalId' => 'searchDepartmentModal',
    'searchLabel' => '部門',
    'resultValueElementId' => 'department_code_',
    'resultNameElementId' => 'department_name_',
    'model' => 'Department'
])
@include('partials.modals.masters.kanbans._search', [
    'modalId' => 'searchCustomerModal',
    'searchLabel' => '取引先',
    'resultValueElementId' => 'customer_code_',
    'resultNameElementId' => 'customer_name_',
    'model' => 'NotSupplier'
])
@include('partials.modals.masters.kanbans._search', [
    'modalId' => 'searchSupplierModal',
    'searchLabel' => '仕入先',
    'resultValueElementId' => 'supplier_code_',
    'resultNameElementId' => 'supplier_name_',
    'model' => 'Supplier'
])