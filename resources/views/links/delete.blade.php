@extends('simple-layout')

@section('toolbar')
<div class="col-sm-12 col-xs-5 faded">
        <div class="breadcrumbs">
            <a href="{{ $book->getUrl() }}" class="text-book text-button">@icon('book'){{ $book->name }}</a>
            <span class="sep">&raquo;</span>
            <span class="text-button">{{ $link->name }}</span>
        </div>
    </div>
@stop

@section('body')

    <div class="container small" ng-non-bindable>
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('delete') {{ trans('entities.link_delete') }}</h3>
            <div class="body">
                <p class="text-neg">{{ trans('entities.link_delete_confirm') }}</p>

                <form action="{{ $link->getUrl() }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <div class="form-group">
                        <a href="{{ $link->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop