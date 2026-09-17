@extends('layouts.tri')

@section('body')
    @include('books.parts.list', ['books' => $books, 'view' => $view])
@stop

@section('left')
    @include('common.view-blocks', ['location' => 'home-non-default', 'position' => 'left'])
@stop

@section('right')
    @include('common.view-blocks', ['location' => 'home-non-default', 'position' => 'right'])
@stop
