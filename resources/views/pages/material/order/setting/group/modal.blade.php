<div id="modal-Group-{{ $result->id }}" class="modal js-modal modal__bg modalSs">
  @php
      $manufacturerPartNumber = $result?->group?->supply_material_group ?? '';
      $buttonText = ($manufacturerPartNumber == '') ? 'この内容で登録する': '更新';
      $buttonDeleteAttribute = ($manufacturerPartNumber == '') ? "disable": '';
      $buttonDeleteClass = ($manufacturerPartNumber == '') ? "btn-disabled": '';
      $formAction = ($manufacturerPartNumber == '') ? route('material.settingGroup.store') : route('material.settingGroup.update', $result?->group?->id);
  @endphp
  
  
  <div class="modal__content modal_fix_width">
    <button type="button" class="modalCloseBtn js-modal-close">x</button>
    <div class="modalInner">
      <div class="accordion mt-2">
        <h1><span>メーカー情報設定</span></h1>
      </div>
      <div class="pagettlWrap">
        <h1><span>メーカー情報設定</span></h1>
      </div>
    <form method="POST" action="{{ $formAction }}"   
        accept-charset="utf-8"
        class="with-js-validation"
        data-confirmation-message="「グループ名を設定します、よろしいでしょうか？」"
        >
        @csrf
        <input type="hidden" class="w-50 mr-half" name="part_number" value="{{ $result->part_number }}">
        
        @if($manufacturerPartNumber != '')
            @method('PUT')
            <input type="hidden" class="w-50 mr-half" name="updator" value="{{Auth::user()->id }}">
        @else
            <input type="hidden" class="w-50 mr-half" name="creator" value="{{ Auth::user()->id }}">
            <input type="hidden" class="w-50 mr-half" name="updated_at" value="">
        @endif
        
      <div class="section">
        <div class="boxModal mb-1">
          <div class="mr-0">
              <div class="tableWrap borderLesstable inputFormArea mb-4" style="background-color: white;border: 1px solid black;">
                  <label class="form-label dotted indented label_for">グループ</label>
                  <div class="flex searchModal">
                      <input type="text" class="w-50 mr-half" name="supply_material_group"
                      value="{{ $manufacturerPartNumber }}">
                      
                  </div>
              </div>
              <button type="button" class="btn btn-danger {{ $buttonDeleteClass }}" 
                  style="width:48%;"
                  {{  $buttonDeleteAttribute }}
                  data-button-delete
                  data-form-id="manufacturerSettingForm-{{ $result->id }}">削除</button>
              <button type="submit" class="btn btn-success" style="width:48%;">{{ $buttonText }}</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>