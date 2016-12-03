@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('books._breadcrumbs', ['book' => $book])
                </div>
            </div>
        </div>
    </div>

    <div class="container small" ng-non-bindable>
        <h1>{{ trans('entities.books_delete') }}</h1>
        <p>{{ trans('entities.books_delete_explain', ['bookName' => $book->name]) }}</p>
        <p class="text-neg">{{ trans('entities.books_delete_confirmation') }}</p>

        <form action="{{$book->getUrl()}}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="{{$book->getUrl()}}" class="button">{{ trans('common.cancel') }}</a>
            <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
        </form>
    </div>

@stop