@extends('base')

@section('content')

    <h2>{{$book->name}}</h2>
    <p class="text-muted">{{$book->description}}</p>
    <a href="{{$book->getUrl() . '/page/create'}}">+ New Page</a>

    <h4>Pages:</h4>
    @if(count($book->pages) > 0)
        @foreach($book->pages as $page)
            <a href="{{$page->getUrl()}}">{{$page->name}}</a><br>
        @endforeach
    @else
        <p class="text-muted">This book has no pages</p>
    @endif
@stop