@extends('base')

@section('content')

    <a href="{{$page->getUrl() . '/edit'}}" class="button primary float right">Edit Page</a>

    <h1>{{$page->name}}</h1>

    <div class="page-content">
        {!! $page->html !!}
    </div>
@stop
