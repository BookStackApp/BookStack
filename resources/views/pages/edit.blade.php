@extends('base')

@section('head')
    <script src="{{ baseUrl('/libs/tinymce/tinymce.min.js?ver=4.6.3') }}"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div class="flex-fill flex">
        <form action="{{ $page->getUrl() }}" autocomplete="off" data-page-id="{{ $page->id }}" method="POST" class="flex flex-fill">
            @if(!isset($isDraft))
                <input type="hidden" name="_method" value="PUT">
            @endif
            @include('pages/form', ['model' => $page])
            @include('pages/form-toolbox')
        </form>


    </div>
    
    @include('components.image-manager', ['imageType' => 'gallery', 'uploaded_to' => $page->id])
    @include('components.code-editor')
    @include('components.entity-selector-popup')

@stop