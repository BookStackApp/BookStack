@extends('layouts.tri')

@section('body')
    @include('shelves.parts.list', ['shelves' => $shelves, 'view' => $view, 'listOptions' => $listOptions])
@stop

@section('right')
    @include('shelves.parts.index-sidebar-section-actions', ['view' => $view])
@stop

@section('left')
    @include('shelves.parts.index-sidebar-section-recents', ['recents' => $recents])
    @include('shelves.parts.index-sidebar-section-popular', ['popular' => $popular])
    @include('shelves.parts.index-sidebar-section-new', ['new' => $new])
@stop