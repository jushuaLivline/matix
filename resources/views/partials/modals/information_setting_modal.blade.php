@push('styles')
    @vite('resources/css/modals/index.css')
@endpush
<div id="infoSettingModal" class="modal js-modal modal__bg modalSs">
    <div class="modal__content">
        <button type="button" class="modalCloseBtn js-modal-close"></button>
        <div class="modalInner">
            <div class="pageHeaderBox rounded">
                メーカー情報設定
            </div>

            <div class="section">
                <h1 class="form-label bar indented">メーカー情報設定</h1>
                <div class="box mb-1">
                    <div class="mb-2">
                        <div class="mr-3">
                            <label class="form-label dotted indented">材料メーカー</label>
                            <div class="d-flex">
                                <input type="text" value="" disabled style="width:150px" class="mr-half">
                                <input type="text" value="" disabled class="mr-half">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div>
                            <label class="form-label dotted indented">担当者・連絡先</label>
                            <div class="d-flex">
                                <textarea type="text" rows="5" cols="100" class="mr-half"></textarea>
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
