<div id="{{ $modalId }}" class="modal js-modal modal__bg modalSs">
    <div class="modal__content">
        <button type="button" class="modalCloseBtn js-modal-close"></button>
        <div class="modalInner">
            <form action="#" accept-charset="utf-8">
                <div class="section">
                    <div class="boxModal mb-1">
                        <div class="mr-0">
                            <label class="form-label dotted indented">{{ $searchLabel }}選択</label>
                            <div class="flex">
                                <input type="text" class="w-100 mr-half"
                                       placeholder="検索キーワードを入力"
                                       name="keyword">
                                <ul class="searchResult"
                                    data-result-value-element="{{ $resultValueElementId }}"
                                    data-result-name-element="{{ $resultNameElementId }}">
                                    <li class="disabled">{{ $searchLabel }}一覧</li>
                                    @foreach($data as $value)
                                        <li data-value="{{ $value->id }}">データ{{ $value->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
    @vite('resources/js/search/index.js')
@endpush
