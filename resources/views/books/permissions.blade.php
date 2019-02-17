@extends('simple-layout')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('/permissions') => [
                    'text' => trans('entities.books_permissions'),
                    'icon' => 'lock',
                ]
            ]])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.books_permissions') }}</h1>
            @include('form.entity-permissions', ['model' => $book])
        </div>
    </div>

@stop
