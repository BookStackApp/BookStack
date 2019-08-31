@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('/delete') => [
                    'text' => trans('entities.books_delete'),
                    'icon' => 'delete',
                ]
            ]])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('entities.books_delete') }}</h1>
            <p>{{ trans('entities.books_delete_explain', ['bookName' => $book->name]) }}</p>
            <p class="text-neg"><strong>{{ trans('entities.books_delete_confirmation') }}</strong></p>

            <form action="{{$book->getUrl()}}" method="POST" class="text-right">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="DELETE">
                <a href="{{$book->getUrl()}}" class="button outline">{{ trans('common.cancel') }}</a>
                <button type="submit" class="button">{{ trans('common.confirm') }}</button>
            </form>
        </div>

    </div>

@stop