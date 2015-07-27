@extends('base')

@section('content')

    <div class="page-content">
        <h1>Create New Chapter</h1>
        <form action="{{$book->getUrl()}}/chapter/create" method="POST">
            @include('chapters/form')
        </form>
    </div>

@stop