@push('styles')
    @vite('resources/css/modals/index.css')
@endpush
<div id="createApprovalModal" class="modal js-modal modal__bg modalSs" style="z-index: 3000 !important">
    <div class="modal__content content-modal" style="width: 60% !important">
        <button id="btnModalClose" type="button" class="modalCloseBtn js-modal-close">X</button>
        <div class="mx-3">
            <div class="pageHeaderBox rounded">
                承認ルート設定
            </div>

            <form id="createApprovalRouteForm" class="with-js-validation-modal">
            <div class="section">
                <h1 class="form-label bar indented">承認ルート詳細・編集</h1>
                <div class="box mb-1">
                    <div class="mb-2 formBody">
                            <label class="form-label dotted indented">承認ルート名</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex mb-2" id="target">
                                <input name="approval_route_name" id="approval_route_name" type="text" value="" style="width:300px" class="mr-half" required>
                            </div>
                            <div class="error_msg"></div>
                            <table class="table table-bordered text-center table-striped-custom">
                                <thead>
                                    <tr>
                                        <th rowspan="2">承認順</th>
                                        <th rowspan="2">承認者</th>
                                        <th rowspan="2">操作</th>
                                    </tr>
                                </thead>
                                <tbody id="create-approval-route-body"> 
                                    
                                </tbody>
                            </table>
                        <!-- <div class="mr-3"> -->
                            
                        <!-- </div> -->
                    </div>
                </div>
            </div>
            <div class="center">
                <div>
                    <a href="#" class="btn btn-blue js-modal-close" style="width: 100px">閉じる</a>
                </div>
                <div class="ml-3">
                    <button type="submit" class="btn btn-green" style="width: 120px">登録</button>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
