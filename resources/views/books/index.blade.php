@extends('layouts.tri')

@section('body')
    @include('books.parts.list', ['books' => $books, 'view' => $view, 'listOptions' => $listOptions])
@stop

@section('left')
    @include('books.parts.index-sidebar-section-recents', ['recents' => $recents])
    @include('books.parts.index-sidebar-section-popular', ['popular' => $popular])
    @include('books.parts.index-sidebar-section-new', ['new' => $new])
@stop

@section('right')
    @include('books.parts.index-sidebar-section-actions', ['view' => $view])
@stop
