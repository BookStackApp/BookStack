@extends('layouts.tri')

@section('body')
    @include('shelves.parts.list', ['shelves' => $shelves, 'view' => $view, 'listOptions' => $listOptions])
@stop

@section('left')
    @include('common.sidebar-sections', ['location' => 'shelves-index', 'position' => 'left'])
@stop

@section('right')
    @include('common.sidebar-sections', ['location' => 'shelves-index', 'position' => 'right'])
@stop
