@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        <div class="breadcrumbs">
            <a href="{{ $book->getUrl() }}" class="text-book text-button">@icon('book'){{ $book->getShortName() }}</a>
            <span class="sep">&raquo;</span>
            <a href="{{ $book->getUrl('/create-link')}}" class="text-button">@icon('add'){{ trans('entities.link_create') }}</a>
        </div>
    </div>
@stop

@section('body')

    <div class="container small" ng-non-bindable>
        <div class="card">
            <h3>@icon('add') {{ trans('entities.link_create') }}</h3>
            <div class="body">
                <form action="{{ $parent->getUrl('/create-link') }}" method="POST">
                    @include('links/form')
                </form>
            </div>
        </div>
    </div>

@stop