@extends('base')

@section('head')
    <script src="/libs/tinymce/tinymce.min.js?ver=4.3.7"></script>
@stop

@section('body-class', 'flexbox')

@section('content')

    <div class="flex-fill flex">
        <form action="{{$book->getUrl() . '/page/' . $draft->id}}" method="POST" class="flex flex-fill">
            @include('pages/form', ['model' => $draft])
        </form>
    </div>
    @include('partials/image-manager', ['imageType' => 'gallery', 'uploaded_to' => $draft->id])
@stop