@push('styles')
    @vite('resources/css/modals/index.css')
@endpush
<div id="groupSettingModal" class="modal js-modal modal__bg modalSs">
    <div class="modal__content">
        <button type="button" class="modalCloseBtn js-modal-close"></button>
        <div class="modalInner">
            <div class="pageHeaderBox rounded">
                グループ設定
            </div>

            <div class="section">
                <h1 class="form-label bar indented">グループ設定</h1>
                <div class="box mb-1">
                    <div class="mb-2">
                        <div class="mr-3">
                            <label class="form-label dotted indented">グループ</label>
                            <div class="d-flex">
                                <input type="text" value="" style="width:150px" class="mr-half">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="center">
                <div>
                    <a href="#" class="btn btn-orange" style="width: 10rem"> 削除 </a>
                </div>
                <div class="ml-3">
                    <a href="#" class="btn btn-green" style="width: 12rem"> この内容で登録する </a>
                </div>
            </div>
        </div>
    </div>
</div>
