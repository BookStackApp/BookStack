@extends('layouts.simple')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('/permissions') => [
                    'text' => trans('entities.books_permissions'),
                    'icon' => 'lock',
                ]
            ]])
        </div>

        <main class="card content-wrap auto-height">
            @include('form.entity-permissions', ['model' => $book, 'title' => trans('entities.books_permissions')])
        </main>
    </div>

@stop
