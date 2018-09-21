@extends('sidebar-layout')

@section('toolbar')
    <div class="col-xs-6 faded">
        <div class="action-buttons text-left">
            @include('shelves/view-toggle', ['shelvesViewType' => $shelvesViewType])
        </div>
    </div>
    <div class="col-xs-6 faded">
        <div class="action-buttons">
            @if($currentUser->can('bookshelf-create-all'))
                <a href="{{ baseUrl("/create-shelf") }}" class="text-pos text-button">@icon('add'){{ trans('entities.shelves_create') }}</a>
            @endif
        </div>
    </div>
@stop

@section('sidebar')
    @if($recents)
        <div id="recents" class="card">
            <h3>@icon('view') {{ trans('entities.recently_viewed') }}</h3>
            @include('partials/entity-list', ['entities' => $recents, 'style' => 'compact'])
        </div>
    @endif

    <div id="popular" class="card">
        <h3>@icon('popular') {{ trans('entities.shelves_popular') }}</h3>
        @if(count($popular) > 0)
            @include('partials/entity-list', ['entities' => $popular, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.shelves_popular_empty') }}</div>
        @endif
    </div>

    <div id="new" class="card">
        <h3>@icon('star-circle') {{ trans('entities.shelves_new') }}</h3>
        @if(count($new) > 0)
            @include('partials/entity-list', ['entities' => $new, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.shelves_new_empty') }}</div>
        @endif
    </div>
@stop

@section('body')
    @include('shelves/list', ['shelves' => $shelves, 'shelvesViewType' => $shelvesViewType])
    <p><br></p>
@stop