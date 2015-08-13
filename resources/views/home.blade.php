@extends('base')

@section('content')
    <div id="container"></div>
@stop


@section('bottom')
    @include('pages/image-manager')
    <div id="image-manager-container"></div>
    <script src="/js/image-manager.js"></script>
    <script>
        //window.ImageManager.show();
    </script>
@stop