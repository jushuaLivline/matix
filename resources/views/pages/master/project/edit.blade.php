@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/received_materials_list.css')
    @vite('resources/css/master/project/edit.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', 'プロジェクトマスタ登録・編集')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>プロジェクトマスタ登録・編集</span></h1>
            </div>

            <form id='submit-kanban-form' data-action='{{ isset($data) ? $data->id : 'store' }}' class='overlayedSubmitForm' accept-charset="utf-8">
                @csrf
                <div class="bg-white">
                    <div class="row">
                        <div class="col-2 label-div">
                            プロジェクトNo. &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="project_number" id="project_number"
                            value="{{ isset($data) ? $data->project_number : Request::get('project_number') ?? '' }}"
                            maxlength="8"
                            required
                            >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-2 label-div">
                            プロジェクト名 &nbsp;<span class="others-frame btn-orange badge">必須</span>
                        </div>
                        <div class="col-10">
                            <input type="text" name="project_name" id="project_name"
                            value="{{ isset($data) ? $data->project_name : Request::get('project_name') ?? '' }}"
                            required
                            >
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-2 label-div">
                            無効にする
                        </div>
                        <div class="col-10">
                            <input type="hidden" id="delete_flag" name="delete_flag"
                                value="0">
                            <input type="checkbox" id="delete_flag" name="delete_flag"
                                value="1"
                                {{ isset($data) && $data->delete_flag == 1 ? 'checked' : '' }}
                                >
                        </div>
                    </div>

                </div>
            </form>
            <div class="d-flex justify-content-between mt-4 btn-div">
                <div>
                    @if( isset($data) )
                        <form id="delete-project-form" action="{{ route('master.project.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete" id="update-delete">削除</button>
                        </form>
                    @endif
                </div>
                <div>
                    <button type="button" class="btn btn-success" id="submit-add-update">登録する</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    @vite(['resources/js/master/project/edit.js'])
@endpush