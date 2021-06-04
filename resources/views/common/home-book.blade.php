@extends('tri-layout')

@section('body')
    @include('books.list', ['books' => $books, 'view' => $view])
@stop

@section('left')
    @include('common.home-sidebar')
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
            @if(user()->can('book-create-all'))
                <a href="{{ url("/create-book") }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.books_create') }}</span>
                </a>
            @endif
            @include('partials.view-toggle', ['view' => $view, 'type' => 'books'])
            @include('components.expand-toggle', ['classes' => 'text-primary', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
            @include('partials.dark-mode-toggle', ['classes' => 'icon-list-item text-primary'])
        </div>
    </div>
@stop
