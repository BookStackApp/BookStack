@extends('layouts.tri')

@section('body')
    @include('books.parts.list', ['books' => $books, 'view' => $view, 'listOptions' => $listOptions])
@stop

@section('left')
    @include('common.sidebar-sections', ['location' => 'books-index', 'position' => 'left'])
@stop

@section('right')
    @include('common.sidebar-sections', ['location' => 'books-index', 'position' => 'right'])
@stop
