@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('books._breadcrumbs', ['book' => $book])
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('delete') {{ trans('entities.books_delete') }}</h3>
            <div class="body">
                <p>{{ trans('entities.books_delete_explain', ['bookName' => $book->name]) }}</p>
                <p class="text-neg">{{ trans('entities.books_delete_confirmation') }}</p>

                <form action="{{$book->getUrl()}}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <a href="{{$book->getUrl()}}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
                </form>
            </div>
        </div>

    </div>

@stop