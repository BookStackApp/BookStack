@extends('base')

@section('head')
    <script src="/libs/tinymce/tinymce.min.js"></script>
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
    <image-manager></image-manager>
@stop