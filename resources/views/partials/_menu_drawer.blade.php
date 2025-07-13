<div class="menu-drawer">
    <input type="checkbox" id="chk">
    <label class="btn pt-1 pb-1" for="chk"></label>
    <label class="other" for="chk"></label>
    <div class="menu-content">
        <div class="sidenavi">
            <div class="sidenaviInner">
                <div class="naviBox">
                    {{-- <ul>
                        <li class="navimenuTop"><a href="/dashboard/">ダッシュボード</a></li>
                    </ul> --}}
                    <div class="navimenuttl">見積管理</div>
                    <ul style="display: none;">
                        <li><a href="/estimate/request">見積依頼作成</a></li>
                        <li><a href="/estimate/search">見積データ一覧</a></li>
                    </ul>
                    <div class="navimenuttl">受注管理</div>
                    <ul style="display: none;">
                        <li>
                            <a href="{{ route('order.kanbanForecast.create', ['year_month' => date('Ym')]) }}">
                                かんばん品内示情報
                            </a>
                        </li>
                        <li><a href="/order/parts/forecast">指示部品内示入力</a></li>
                        <li><a href="/order/data-acquisition">内示データ取込</a></li>
                        <li><a href="/order/forecast">内示データ一覧</a></li>
                        <li><a href="/order/quantity-calculation">所要量計算</a></li>
                        <li><a href="/order/forecast/summary">内示集計</a></li>
                        <li><a href="/order/confirmed/create">確定受注入力</a></li>
                        {{-- <li><a href="/order/confirmed">確定受注一覧</a></li> --}}
                    </ul>
                    <div class="navimenuttl">支給材管理</div>
                    <ul style="display: none;">
                        <li><a href="/material/kanban/create">かんばん入力</a></li>
                        <li><a href="/material/kanban/temporary/create">臨時かんばん入力</a></li>
                        <li><a href="/material/fraction/create">端数指示入力</a></li>
                        <li><a href="/material/procurement">材料調達計画表</a></li>
                        <li>
                            <a href="{{ url('material/order') . '?' . http_build_query([
                                'arrival_day_from' => now()->startOfMonth()->format('Ymd'),
                                'arrival_day_to' => now()->endOfMonth()->format('Ymd'),
                                'instruction_date_start' => now()->startOfMonth()->format('Ymd'),
                                'instruction_date_end' => now()->endOfMonth()->format('Ymd')
                            ]) }}">発注データ一覧</a>
                        </li>
                        <li><a href="/material/order/detail">発注明細書発行</a></li>
                        <li><a href="/material/order/inspection">検収入力</a></li>
                        <li>
                            <a href="{{ route('material.received.materials.index', [
                                'arrival_day_from' => now()->startOfMonth()->format('Ymd'),
                                'arrival_day_to' => now()->endOfMonth()->format('Ymd')
                            ]) }}">入荷実績一覧</a>
                        </li>
                        <li><a href="/material/return/create">返品実績入力</a></li>
                        <li><a href="/material/return">返品実績一覧</a></li>
                    </ul>
                    <div class="navimenuttl">外注管理</div>
                    <ul style="display: none;">
                        <li><a href="/outsource/kanban/create">かんばん入力</a></li>
                        <li><a href="/outsource/kanban/temporary/create">臨時かんばん入力</a></li>
                        <li><a href="/outsource/fraction/create">端数指示入力</a></li>
                        <li><a href="/outsource/order">発注データ一覧</a></li>
                        <li><a href="/outsource/order/slip">発注伝票発行</a></li>
                        <li><a href="/outsource/delivery/reissue">納品書・受領書再発行</a></li>
                        <li><a href="/outsource/delivery/specified">指定納品書発行</a></li>
                        <li><a href="/outsource/supply/kanban/create">支給品かんばん入力</a></li>
                        <li><a href="/outsource/supply/replenishment/create">支給品指示入力</a></li>
                        <li><a href="/outsource/supply">支給品データ一覧</a></li>
                        <li><a href="/outsource/inspection/create">検収入力</a></li>
						<li><a href="/outsource/arrival">入荷実績一覧</a></li>
						<li><a href="/outsource/inspection/cancel">検収取消</a></li>
						<li><a href="/outsource/arrival/pending">未入荷一覧</a></li>
						<li><a href="{{ route('outsource.defect.material.create') }}">材料不良実績入力</a></li>
						<li><a href="/outsource/defect/material">材料不良実績一覧</a></li>
						<li><a href="/outsource/defect/process/create">加工不良実績入力</a></li>
						<li><a href="/outsource/defect/process">加工不良実績一覧</a></li>
                    </ul>
                    <div class="navimenuttl">出荷検収管理</div>
                    <ul style="display: none;">
                        <li><a href="/shipment/actual/create">出荷実績入力</a></li>
                        <li><a href="/shipment/actual">出荷データ一覧</a></li>
                        <li><a href="/shipment/summary">出荷集計</a></li>
                    </ul>
                    <div class="navimenuttl">購買管理</div>
                    <ul style="display: none;">
                        <li><a href="/purchase/requisition/create">購買依頼入力</a></li>
                        <li><a href="{{route('purchase.requisition.index',[
                            'request_date_from' => now()->startOfMonth()->format('Ymd'),
                            'request_date_to' => now()->endOfMonth()->format('Ymd'),
                        ])}}">購買依頼一覧</a></li>
                        <li><a href="/purchase/approval/list">購買依頼承認</a></li>
                        <li><a href="/purchase/order/process">発注処理</a></li>
                        <li><a href="/purchase/order/reissue">注文書再発行</a></li>
                        <li><a href="{{route('purchase.order.index')}}">発注データ一覧</a></li>
                        <li><a href="/purchase/actual/production/create">生産品 購入実績入力</a></li>
                        <li><a href="/purchase/actual/item/create">購買品 購入実績入力</a></li>
                        <li><a href="/purchase/actual">購入実績一覧</a></li>
                        <li><a href="/purchase/supplier">仕入先別購入金額一覧</a></li>
                    </ul>
                    {{-- <div class="navimenuttl">販売管理</div>
                    <ul style="display: none;">
                        <li><a href="/sales/issuance-of-statement-of-order-amount">発注金額明細表発行</a></li>
                        <li><a href="/sales/sale-plan-search">販売計画表検索・一覧</a></li>
                        <li><a href="/sales/sale-performance-table">販売実績表検索・一覧</a></li>
                    </ul>
                    <div class="navimenuttl">設備点検</div>
                    <ul style="display: none;">
                        <li><a href="/admin/equipment-inspection/create">設備点検票 登録</a></li>
                        <!-- <li><a href="#">設備点検票 確認・編集・承認</a></li> -->
                        <li><a href="/admin/equipment-inspection/list">設備点検票 一覧</a></li>
                        <li><a href="/admin/inspection-item-basic-set/list">点検項目基本セット 一覧</a></li>
                        <li><a href="/admin/inspection-item-basic-set/list">点検項目基本セット 詳細</a></li>
                        <li><a href="/admin/inspection-item-basic-set/list">点検項目基本セット 登録</a></li>
                        <li><a href="/admin/inspection-item-basic-set/list">点検項目基本セット 編集</a></li>
                        <!-- <li><a href="#">【タブレット】 設備点検票 入力</a></li> -->
                    </ul>
                    <div class="navimenuttl">日々生産管理</div>
                    <ul style="display: none;">
                        <li><a href="/admin/daily-production-control-table/edit">日々生産管理表 入力</a></li>
                        <li><a href="/admin/daily-production-control-table/reference">日々生産管理表 参照</a></li>
                    </ul>
                    <div class="navimenuttl">月次管理</div>
                    <ul style="display: none;">
                        <li><a href="/monthly/collect-ai-purchase-data">AI買入明細データ取込</a></li>
                        <li><a href="/monthly/sales-closing-process">売上締め処理</a></li>
                        <li><a href="/monthly/toyobilling-data-import">東陽請求データ取込</a></li>
                        <li><a href="/monthly/list-unmatched-purchase-results">購入実績アンマッチ検索・一覧</a></li>
                        <li><a href="/monthly/toyo-billing-data-output">訂正東陽請求データ出力</a></li>
                        <li><a href="/monthly/purchasing-closing-process">購買締め処理</a></li>
                        <li><a href="/monthly/payment-schedule-list">支払予定検索・一覧</a></li>
                        <li><a href="/monthly/accounting-closing">経理締め処理</a></li>
                    </ul>
                    <div class="navimenuttl">原価管理</div>
                    <ul style="display: none;">
                        <li><a href="/cost/list">原価表検索・一覧</a></li>
                        <li><a href="/cost/purchase-breakdown">費目別・仕入内訳検索・一覧</a></li>
                        <li><a href="/cost/purchase-data">費目別・仕入データリスト検索・一覧</a></li>
                    </ul>
                    <div class="navimenuttl">在庫管理</div>
                    <ul style="display: none;">
                        <li><a href="/stock-inventory/list">製品在庫検索・一覧</a></li>
                    </ul> --}}
                    <div class="navimenuttl">マスタ管理</div>
                    <ul style="display: none;">
                        <li><a href="/master/supplier">取引先マスタ一覧</a></li>
                        <li><a href="/master/line">ラインマスタ一覧</a></li>
                        <li><a href="/master/part">品番マスタ一覧</a></li>
                        {{-- <li><a href="/master/processes">工程マスタ一覧</a></li> --}}
                        <li><a href="/master/project">プロジェクトマスタ一覧</a></li>
                        <li><a href="/master/machine">機番マスタ一覧</a></li>
                        <li><a href="/master/kanban">かんばんマスタ一覧</a></li>
                        <li><a href="/master/employee">社員マスタ一覧</a></li>
                        {{-- <li><a href="#">在庫管理品番マスタ検索・一覧</a></li>
                        <li><a href="#">置場マスタ検索・一覧</a></li>
                        <li><a href="#">図面マスタ検索・一覧</a></li>
                        <li><a href="#">財務連携用データ出力</a></li>
                        <li><a href="#">カレンダーマスタ設定</a></li> --}}
                        {{-- <li><a href="/master/departments">部門マスタ一覧</a></li> --}}
                        <li><a href="/master/calendar">休日一覧</a></li>
                        {{-- <li><a href="/admin/facility-management-master/create">設備管理マスタ 登録・編集</a></li>
                        <li><a href="/admin/facility-management-master/list">設備管理マスタ一覧</a></li> --}}
                    </ul>
                    <!-- <div class="navimenuttl">ADMIN</div>
                    <ul style="display: none;">
                        <li><a href="/admin/equipment-inspection/create">設備点検票 登録</a></li>
                        <li><a href="/admin/equipment-inspection/list">設備点検票 一覧</a></li>
                        <li><a href="/admin/inspection-item-basic-set/list">点検項目基本セット 一覧</a></li>
                        <li><a href="/admin/daily-production-control-table/edit">日々生産管理表 入力</a></li>
                        <li><a href="/admin/daily-production-control-table/reference">日々生産管理表 参照</a></li>
                        <li><a href="/admin/facility-management-master/create">設備管理マスタ 登録・編集</a></li>
                        <li><a href="/admin/facility-management-master/list">設備管理マスタ 一覧</a></li>
                    </ul> -->
                </div>
            </div>
        </div>

    </div>
</div>
