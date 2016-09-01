@extends('base')

@section('head')
    <script src="{{ baseUrl('/libs/tinymce/tinymce.min.js?ver=4.3.7') }}"></script>
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
    @include('partials/image-manager', ['imageType' => 'gallery', 'uploaded_to' => $page->id])

    <div id="entity-selector-wrap">
        <div class="overlay" entity-link-selector>
            <div class="popup-body small flex-child">
                <div class="popup-header primary-background">
                    <div class="popup-title">Entity Select</div>
                    <button type="button" class="corner-button neg button">x</button>
                </div>
                @include('partials/entity-selector', ['name' => 'entity-selector'])
                <div class="popup-footer">
                    <button type="button" disabled="true" class="button entity-link-selector-confirm pos corner-button">Select</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {

        })();
    </script>

@stop