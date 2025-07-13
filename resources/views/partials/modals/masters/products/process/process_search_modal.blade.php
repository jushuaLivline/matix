<div id="{{ $modalId }}" class="modal js-modal-search modal__bg modalSs">
    <div class="modal__content modal_fix_width">
        <button type="button" class="modalCloseBtn js-modal-close-search">x</button>
        <div class="modalInner">
            <form action="#" accept-charset="utf-8">
                <div class="section">
                    <div class="boxModal mb-1">
                        <div class="mr-0">
                            <label class="form-label dotted indented label_for">工程コード選択</label>
                            <div class="flex searchModal">
                                <input type="hidden" id="model" value="Process">
                                <input type="hidden" id="searchLabel" value="工程コード一覧">
                                <input type="text" class="w-100 mr-half"
                                        placeholder="検索キーワードを入力"
                                        name="keyword">
                                <ul class="searchResultProcess"
                                    id="search-result"
                                    data-result-value-element="{{ $resultValueElementId }}"
                                    data-result-name-element="{{ $resultNameElementId }}">
                                </ul>
                                <div class="clear">
                                    <button
                                        id="clear"
                                        class="clear-button"
                                        data-result-value-element="{{ $resultValueElementId }}"
                                        data-result-name-element="{{ $resultNameElementId }}"
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