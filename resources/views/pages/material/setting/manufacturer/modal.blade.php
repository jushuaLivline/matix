<div id="manufacturer_modal_info" class="modal js-modal modal__bg modalSs">
  <div class="modal__content modal_fix_width">
    <button type="button" class="modalCloseBtn js-modal-close">x</button>
    <div class="modalInner">
      <div class="accordion mt-2">
          <h1>
              <span>
                  メーカー情報設定
              </span>
          </h1>
      </div>
      <div class="pagettlWrap">
          <h1><span>メーカー情報設定</span></h1>
      </div>
      <div class="section">
        <div class="boxModal mb-1">
          @php
            $registerButtonClass = Request::get('process_name') ?? "btn-disabled";
            $registerAttrr = Request::get('process_name') ?? "disabled";
            $registerButton = $manufacturerInfo
                    ? "更新"
                    : "この内容で更新する";
            $formAction = ($manufacturerInfo == '') 
                    ? route('material.settingManufacturer.store') 
                    : route('material.settingManufacturer.update', $manufacturerInfo?->id);
          @endphp
          <form method="POST" 
                action="{{ $formAction }}" 
                method="POST"
                class="with-js-validation"
                data-confirmation-message="担当者・連絡先情報を設定します、よろしいでしょうか？">
            @csrf
            <input type="hidden" class="w-50 mr-half" name="updated_at" value="{{ ($manufacturerInfo == '') ? '' : now()}}">
            @if($manufacturerInfo == '')
                <input type="hidden" class="w-50 mr-half" name="creator_code" value="{{Auth::user()->id }}">
            @else
            @method('PUT')
                <input type="hidden" class="w-50 mr-half" name="updator_code" value="{{ Auth::user()->id }}">
            @endif
            <div class="mr-0">
                <div class="tableWrap borderLesstable inputFormArea mb-4" style="background-color: white;border: 1px solid black;">
                    <dl class="formsetBox mb-3">
                        <dt>材料メーカー</dt>
                        <dd>
                            <p class="formPack fixedWidth fpfw25p">
                                <input type="text" name="material_manufacturer_code"
                                        id="material_maker_code" readonly
                                        value="{{ request()->get('process_code') }}">
                            </p>
                            <p class="formPack fixedWidth fpfw50p box-middle-name">
                                <input type="text" readonly
                                        name="material_maker_name"
                                        id="material_maker_name"
                                        value="{{ request()->get('process_name') }}"
                                        class="middle-name text-left">
                            </p>
                        </dd>
                    </dl>
                    <label class="form-label dotted indented label_for">担当者・連絡先</label>
                    <div class="flex searchModal">
                        <textarea class="customScrollbarSelect" rows="10" name="person_in_charge" style="width:100%;">{{ trim($manufacturerInfo?->person_in_charge ?? '') }}</textarea>
                    </div>
                </div>
                <button
                    type="submit"
                    class="btn btn-success {{ $registerButtonClass }}" style="width:48%;"
                    {{ $registerAttrr }}>
                    {{$registerButton}}
                </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>