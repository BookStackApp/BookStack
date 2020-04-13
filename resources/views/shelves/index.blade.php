@extends('tri-layout')

@section('body')
    @include('shelves.list', ['shelves' => $shelves, 'view' => $view])
@stop

@section('right')

    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
            @if($currentUser->can('bookshelf-create-all'))
                <a href="{{ url("/create-shelf") }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.shelves_new_action') }}</span>
                </a>
            @endif
            @include('partials.view-toggle', ['view' => $view, 'type' => 'shelves'])
        </div>
    </div>

@stop

@section('left')
    @if($recents)
        <div id="recents" class="mb-xl">
            <h5>{{ trans('entities.recently_viewed') }}</h5>
            @include('partials.entity-list', ['entities' => $recents, 'style' => 'compact'])
        </div>
    @endif

    <div id="popular" class="mb-xl">
        <h5>{{ trans('entities.shelves_popular') }}</h5>
        @if(count($popular) > 0)
            @include('partials.entity-list', ['entities' => $popular, 'style' => 'compact'])
        @else
            <div class="text-muted">{{ trans('entities.shelves_popular_empty') }}</div>
        @endif
    </div>

    <div id="new" class="mb-xl">
        <h5>{{ trans('entities.shelves_new') }}</h5>
        @if(count($new) > 0)
            @include('partials.entity-list', ['entities' => $new, 'style' => 'compact'])
        @else
            <div class="text-muted">{{ trans('entities.shelves_new_empty') }}</div>
        @endif
    </div>
@stop