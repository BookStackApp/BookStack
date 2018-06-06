@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-6 faded">
        <div class="action-buttons text-left">
            <a expand-toggle=".entity-list.compact .entity-item-snippet" class="text-primary text-button">@icon('expand-text'){{ trans('common.toggle_details') }}</a>
        </div>
    </div>
@stop

@section('sidebar')
    @include('common/home-sidebar')
@stop

@section('body')
    <div class="page-content" page-display="{{ $customHomepage->id }}">
        @include('pages/page-display', ['page' => $customHomepage])
    </div>
@stop
