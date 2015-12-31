@extends('base')

@section('head')
    <script src="/libs/tinymce/tinymce.min.js?ver=4.3.2"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div class="flex-fill flex" ng-non-bindable>
        <form action="{{$book->getUrl() . '/page'}}" method="POST" class="flex flex-fill">
            @include('pages/form')
            @if($chapter)
                <input type="hidden" name="chapter" value="{{$chapter->id}}">
            @endif
        </form>
    </div>
    @include('partials/image-manager', ['imageType' => 'gallery'])
@stop