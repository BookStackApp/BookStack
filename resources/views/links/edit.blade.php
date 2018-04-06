@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        <div class="breadcrumbs">
            <a href="{{ $book->getUrl() }}" class="text-book text-button">@icon('book'){{ $book->getShortName() }}</a>
            <span class="sep">&raquo;</span>
            <span class="text-button">{{ $link->name }}</span>
        </div>
    </div>
@stop

@section('body')

    <div class="container small" ng-non-bindable>
        <div class="card">
            <h3>{{ trans('entities.link_edit') }}</h3>
            <div class="body">
                <form action="{{ $link->getUrl() }}" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    @include('links/form', ['model' => $link])
                </form>
            </div>
        </div>
    </div>

@stop