@extends('layouts.base')

@section('head')
    <script src="{{ url('/libs/tinymce/tinymce.min.js?ver=5.10.2') }}" nonce="{{ $cspNonce }}"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div id="main-content" class="flex-fill flex fill-height">
        <form action="{{ $page->getUrl() }}" autocomplete="off" data-page-id="{{ $page->id }}" method="POST" class="flex flex-fill">
            {{ csrf_field() }}

            @if(!isset($isDraft))
                <input type="hidden" name="_method" value="PUT">
            @endif
            @include('pages.parts.form', ['model' => $page])
            @include('pages.parts.editor-toolbox')
        </form>
    </div>
    
    @include('pages.parts.image-manager', ['uploaded_to' => $page->id])
    @include('pages.parts.code-editor')
    @include('entities.selector-popup')
@stop