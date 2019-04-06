@extends('simple-layout')

@section('body')
    <div class="container mt-m">
        <div class="grid right-focus gap-xl">
            <div>

                <div class="actions mb-xl">
                    <h5>{{ trans('common.actions') }}</h5>
                    <div class="icon-list text-primary">
                        @include('partials.view-toggle', ['view' => $view, 'type' => 'book'])
                        <a expand-toggle=".entity-list.compact .entity-item-snippet" class="icon-list-item">
                            <span>@icon('expand-text')</span>
                            <span>{{ trans('common.toggle_details') }}</span>
                        </a>
                    </div>
                </div>

                @include('common.home-sidebar')
            </div>
            <div>
                @include('books.list', ['books' => $books, 'view' => $view])
            </div>
        </div>
    </div>
@stop