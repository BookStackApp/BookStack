@extends('layouts.tri')

@section('body')
    @include('shelves.parts.list', ['shelves' => $shelves, 'view' => $view, 'listOptions' => $listOptions])
@stop

@section('left')
    @include('common.view-blocks', ['location' => 'shelves-index', 'position' => 'left'])
@stop

@section('right')
    @include('common.view-blocks', ['location' => 'shelves-index', 'position' => 'right'])
@stop
