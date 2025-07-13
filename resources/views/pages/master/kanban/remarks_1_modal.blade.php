<div id="remark_1_modal" class="modal js-modal modal__bg">
    <div class="modal__content " style="min-width: 1000px; max-width: 1000px">
        <button type="button" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner" style="min-width: 950px; max-width: 950px; margin-top: 20px" id="modalContent">
            <div class="content" style="min-width: 950px; max-width: 950px">
                <div class="contentInner">
                    <div class="pagettlWrap">
                        <h1><span>品番検索</span></h1>
                    </div>
        
                    {{-- <form action="#" accept-charset="utf-8" style="min-width: 800px; max-width: 800px"> --}}
                        <div class="tableWrap borderLesstable inputFormArea" 
                            style="
                                width: 950px;
                                margin-top: 0.5rem;
                                background-color: #ffffff;
                                padding: 10px;
                                box-sizing: border-box;
                                border: #cccccc solid 1px;"
                            >
                            <div class="row-content">
                                <!-- part_number -->
                                <div class="flex-row">
                                    <label for="part_number_" class="label_for">品番</label>
                                    <input type="text" class="row-input" id="part_number_" name="part_number" value="{{ Request::get('part_number') }}">
                                </div>
                                <!-- product_name -->
                                <div class="flex-row">
                                    <label for="product_name" class="label_for">品名</label>
                                    <input type="text" class="row-input" id="product_name_" name="product_name" value="{{ Request::get('product_name') }}">
                                </div>
                            </div>

                            <div class="row-group-content">
                                <!-- line_code -->
                                <div class="flex-row">
                                    <label for="line_code_" class="label_for">ライン</label>
                                    <div class="search-group">
                                        <input type="text" id="line_code_" name="line_code" value="{{ Request::get('line_code') }}" class="" style="width: 80px">
                                        <input type="text" readonly
                                                id="line_name_"
                                                name="line_name"
                                                value=""
                                                class="middle-name"
                                                style="width: 135px">
                                        <button type="button" class="btnSubmitCustomSecond btnSubmitCustom js-modal-open"
                                                        data-target="searchLineModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <!-- department_code -->
                                <div class="flex-row">
                                    <label for="department_code_" class="label_for">部門</label>
                                    <div class="search-group">
                                        <input type="text" id="department_code_" name="department_code" value="{{ Request::get('department_code') }}" class="" style="width: 100px">
                                        <input type="text" readonly
                                                id="department_name_"
                                                name="department_name"
                                                value=""
                                                class="middle-name"
                                                style="width: 170px">
                                        <button type="button" class="btnSubmitCustomSecond btnSubmitCustom js-modal-open"
                                                        data-target="searchDepartmentModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row-group-content">
                                <!-- customer_code -->
                                <div class="flex-row">
                                    <label for="customer_code_" class="label_for">取引先</label>
                                    <div class="search-group">
                                        <input type="text" id="customer_code_" name="customer_code" value="{{ Request::get('customer_code') }}" class="" style="width: 100px">
                                        <input type="text" readonly
                                                id="customer_name_"
                                                name="customer_name"
                                                value=""
                                                class="middle-name"
                                                style="width: 170px">
                                        <button type="button" class="btnSubmitCustomSecond btnSubmitCustom js-modal-open"
                                                        data-target="searchCustomerModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                                <!-- supplier_code -->
                                <div class="flex-row">
                                    <label for="supplier_code_" class="label_for">仕入先</label>
                                    <div class="search-group">
                                        <input type="text" id="supplier_code_" name="supplier_code" value="{{ Request::get('supplier_code') }}" class="" style="width: 100px">
                                        <input type="text" readonly
                                                id="supplier_name_"
                                                name="supplier_name"
                                                value=""
                                                class="middle-name"
                                                style="width: 170px">
                                        <button type="button" class="btnSubmitCustomSecond btnSubmitCustom js-modal-open"
                                                        data-target="searchSupplierModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row-group-content" style="gap: 30px">
                                <!-- product_category -->
                                <div class="flex-row">
                                    <label for="product_category_" class="label_for">製品区分</label>
                                    <select name="product_category" id="product_category_" class="classic" style="width: 130px">
                                        <option value="">すべて</option>
                                        @foreach ($productCategory as $key => $category)
                                            <option value="{{ $key }}" {{ Request::get('product_category') ? 'selected' : '' }}>{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- delete_flag -->
                                <div class="flex-row">
                                    <label for="delete_flag_" class="label_for">有効/無効</label>
                                    <select name="delete_flag" id="delete_flag_" class="classic">
                                        <option value="0" {{ Request::get('delete_flag') == 0 ? 'selected' : '' }}>有効</option>
                                        <option value="1" {{ Request::get('delete_flag') == 1 ? 'selected' : '' }}>無効</option>
                                    </select>
                                </div>
                            </div>

                            <ul class="buttonlistWrap" style="width: 0;">
                                <li>
                                    <div class="parent">
                                        <div>
                                            <a href="#"
                                               class="buttonBasic btn-reset bColor-ok js-btn-reset-reload-second" style="width: 250px!important">検索条件をクリア</a>
                                        </div>
                                        <div>
                                            <input type="button" id="searchBtnProduct" value="検索"
                                                class="buttonBasic bColor-ok" style="width: 250px!important">
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    {{-- </form> --}}
        
                    <div class="tableWrap bordertable"
                        style="
                            width: 950px;
                            margin-top: 0.5rem;
                            background-color: #ffffff;
                            padding: 10px;
                            box-sizing: border-box;
                            border: #cccccc solid 1px;"
                        >
                        <table class="tableBasic list-table" id="tableResults_id">
                            <thead>
                                <tr>
                                    <th>品番</th>
                                    <th>品名</th>
                                    <th>製品区分</th>
                                    <th>得意先</th>
                                    <th>仕入先</th>
                                    <th style="width: 100px">操作</th>
                                </tr>
                            </thead>
                            <tbody id="tableResults">
                                <tr>
                                    <td class="tA-le"></td>
                                    <td class="tA-le"></td>
                                    <td class="tA-le"></td>
                                    <td class="tA-le"></td>
                                    <td class="tA-le"></td>
                                    <td class="tA-cn"></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="pagination-container">
                            <div class="paginationLinks" id="paginationLinks"></div>
                        </div>

                    </div>
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