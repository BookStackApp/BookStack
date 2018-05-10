@extends('sidebar-layout')

@section('toolbar')
    <div class="col-xs-6">
        <div class="action-buttons text-left">
            <form action="{{ baseUrl("/settings/users/{$currentUser->id}/switch-book-view") }}" method="POST" class="inline">
                {!! csrf_field() !!}
                {!! method_field('PATCH') !!}
                <input type="hidden" value="{{ $booksViewType === 'list'? 'grid' : 'list' }}" name="book_view_type">
                @if ($booksViewType === 'list')
                    <button type="submit" class="text-pos text-button">@icon('grid'){{ trans('common.grid_view') }}</button>
                @else
                    <button type="submit" class="text-pos text-button">@icon('list'){{ trans('common.list_view') }}</button>
                @endif
            </form>
        </div>
    </div>
    <div class="col-xs-6 faded">
        <div class="action-buttons">
            @if($currentUser->can('book-create-all'))
                <a href="{{ baseUrl("/create-book") }}" class="text-pos text-button">@icon('add'){{ trans('entities.books_create') }}</a>
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
        <h3>@icon('popular') {{ trans('entities.books_popular') }}</h3>
        @if(count($popular) > 0)
            @include('partials/entity-list', ['entities' => $popular, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.books_popular_empty') }}</div>
        @endif
    </div>

    <div id="new" class="card">
        <h3>@icon('star-circle') {{ trans('entities.books_new') }}</h3>
        @if(count($popular) > 0)
            @include('partials/entity-list', ['entities' => $new, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.books_new_empty') }}</div>
        @endif
    </div>
@stop

@section('body')
    @include('books/list', ['books' => $books, 'bookViewType' => $booksViewType])
@stop