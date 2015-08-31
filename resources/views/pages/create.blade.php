@extends('base')

@section('head')
    <script src="/bower/tinymce-dist/tinymce.jquery.min.js"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div class="flex-fill flex">
        <form action="{{$book->getUrl() . '/page'}}" method="POST" class="flex flex-fill">
            @include('pages/form')
            @if($chapter)
                <input type="hidden" name="chapter" value="{{$chapter->id}}">
            @endif
        </form>
    </div>
@stop

@section('bottom')
    @include('pages/image-manager')
    <script src="/js/image-manager.js"></script>
@stop