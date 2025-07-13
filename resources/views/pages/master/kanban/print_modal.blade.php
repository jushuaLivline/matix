<div id="print_modal" class="modal js-modal modal__bg">
    <div class="modal__content " style="width: 550px;">
        <button type="button" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner" id="modalContent">
            <div class="content">
                <div class="contentInner">
                    <div class="pagettlWrap">
                        <h1><span>かんばん印刷</span></h1>
                    </div>
        
                    {{-- <form action="#" accept-charset="utf-8" style="min-width: 800px; max-width: 800px"> --}}
                        <div class="tableWrap borderLesstable inputFormAreaCustomer">
                            <table class="tableBasic" id="print_table">
                                <tbody>
                                    {{-- printing_surface --}}
                                    <tr>
                                        <td style="width: 40%">
                                            印刷面
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <input type="radio" id="printing_surface1" name="printing_surface" style="min-width:30px; margin-left: 0px;"  value="1"> &nbsp;
                                                <label for="printing_surface1" style="min-width: 100px; text-align: left">ﾊﾞｰｺｰﾄﾞ面</label>
                                            
                                                <input type="radio" id="printing_surface2" name="printing_surface" style="min-width:30px;" value="2"> &nbsp;
                                                <label for="printing_surface2" style="min-width: 100px; text-align: left">QRｺｰﾄﾞ面</label>
                                            </div>
                                            <div class="error_msg"></div>
                                        </td>
                                    </tr>
                                    {{-- serial_number --}}
                                    <tr>
                                        <td style="width: 40%">
                                            発行開始連番 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                                        </td>
                                        <td>
                                            <dd>
                                                <p class="formPack text-left">
                                                    <input type="text" id="serial_number" name="serial_number" class="w-100px" value=""  required>
                                                </p>
                                                @error('serial_number')
                                                    <span class="err_msg">{{ $message }}</span>
                                                @enderror
                                            </dd>
                                        </td>
                                    </tr>
                                    {{-- number_of_sheets --}}
                                    <tr>
                                        <td style="width: 40%">
                                            印刷枚数 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                                        </td>
                                        <td>
                                            <dd>
                                                <p class="formPack text-left">
                                                    <input type="text" id="number_of_sheets" name="number_of_sheets" class="w-100px" value="" required>
                                                </p>
                                                @error('number_of_sheets')
                                                    <span class="err_msg">{{ $message }}</span>
                                                @enderror
                                            </dd>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-2">
                            <button type="button" class="btn_print btn btn-success" style="width: 180px; cursor: pointer;">
                                印刷
                            </button>
                        </div>  
                    {{-- </form> --}}
                </div>
            </div>
        </div>
    </div>
</div>