@extends('layouts.tri')

@section('body')
    @include('books.parts.list', ['books' => $books, 'view' => $view, 'listOptions' => $listOptions])
@stop

@section('left')
    @foreach($sidebar->getSectionsForLocation('books-index', 'left') as $section)
        @include($section->getView(), $section->withData(get_defined_vars(), request()))
    @endforeach
    @include('books.parts.index-sidebar-section-popular', ['popular' => $popular])
    @include('books.parts.index-sidebar-section-new', ['new' => $new])
@stop

@section('right')
    @include('books.parts.index-sidebar-section-actions', ['view' => $view])
@stop
