@push('styles')
    @vite('resources/css/modals/index.css')
@endpush
<div id="approvalModal" class="modal js-modal modal__bg modalSs" style="z-index: 2999 !important">
    <div class="modal__content" style="width: 60% !important">
        <button type="button" class="modalCloseBtn js-modal-close">X</button>
        <div class="modalInner">
            <div class="pageHeaderBox rounded">
                承認ルート設定
            </div>

            <div class="section">
                <h1 class="form-label bar indented">承認ルート一覧</h1>
                <div class="box mb-1">
                    <div class="mb-2">
                        <!-- <div class="mr-3"> -->
                            <!-- <label class="form-label dotted indented">グループ</label>
                            <div class="d-flex">
                                <input type="text" value="" style="width:150px" class="mr-half">
                            </div> -->
                            <table class="table table-bordered text-center table-striped-custom">
                                <thead>
                                    <tr>
                                        <th rowspan="2">表示順</th>
                                        <th rowspan="2">承認ルート名</th>
                                        <th rowspan="2">承認者数</th>
                                        <th rowspan="2">操作</th>
                                    </tr>
                                </thead>
                                <tbody id="approval-route-body">

                                </tbody>
                            </table>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
            <div class="center">
                <div>
                    <a href="#" class="btn btn-blue js-modal-close" style="width: 100px">閉じる</a>
                </div>
                <div class="ml-3">
                    <a href="javascript:void(0)" class="btn btn-green js-modal-open"
                                        data-target="createApprovalModal" id="createApprovalRouteButton" style="width: 100px" >ルート追加</a>
                </div>
            </div>
        </div>
    </div>
</div>
