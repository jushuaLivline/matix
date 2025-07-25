<div id="{{ $modalId }}" class="modal js-modal modal__bg modalSs">
    <div class="modal__content modal_fix_width">
        <button type="button" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner">
            <form action="#" accept-charset="utf-8">
                <div class="section">
                    <div class="boxModal mb-1">
                        <div class="mr-0">
                            <label class="form-label dotted indented label_for">{{ $searchLabel }}選択</label>
                            <div class="flex searchModal">
                                <input type="hidden" id="model" value="{{ $model ?? '' }}">
                                <input type="hidden" id="searchLabel" value="{{ $searchLabel }}一覧">
                                <input type="hidden" id="query" value="{{ $query ?? '' }}">
                                <input type="hidden" id="reference" value="{{ $reference ?? '' }}">
                                <input type="text" class="w-100 mr-half"
                                       placeholder="検索キーワードを入力"
                                       name="keyword">
                                <ul class="searchResult"
                                    id="search-result"
                                    data-result-value-element="{{ $resultValueElementId }}"
                                    data-result-name-element="{{ $resultNameElementId }}"
                                    data-result-branch_number-element="{{ $resultBranchNumberEle }}"
                                    >
                                </ul>
                                <div class="clear">
                                    <button
                                        type="button"
                                        id="clear"
                                        class="clear-button"
                                        data-result-value-element="{{ $resultValueElementId }}"
                                        data-result-name-element="{{ $resultNameElementId }}"
                                        data-result-branch_number-element="{{ $resultBranchNumberEle }}"
                                    >
                                        選択した値をクリアする
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    @vite('resources/js/master/machine-numbers/_search.js')
@endpush
