@extends('layouts.tri')

@section('body')
    @include('records.parts.list', ['records' => $records, 'view' => $view])
@stop

@section('left')
    @include('home.parts.sidebar')
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-link">
            @if(user()->can('record-create-all'))
                <a href="{{ url("/create-record") }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.records_create') }}</span>
                </a>
            @endif
            @include('entities.view-toggle', ['view' => $view, 'type' => 'records'])
            <a href="{{ url('/tags') }}" class="icon-list-item">
                <span>@icon('tag')</span>
                <span>{{ trans('entities.tags_view_tags') }}</span>
            </a>
            @include('home.parts.expand-toggle', ['classes' => 'text-link', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
            @include('common.dark-mode-toggle', ['classes' => 'icon-list-item text-link'])
        </div>
    </div>
@stop

