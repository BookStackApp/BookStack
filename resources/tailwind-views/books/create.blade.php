@extends('layouts.simple')

@section('body')
    <div class="container small">
        <div class="my-s">
            @if (isset($bookshelf))
                @include('entities.breadcrumbs', ['crumbs' => [
                    $bookshelf,
                    $bookshelf->getUrl('/create-book') => [
                        'text' => trans('entities.books_create'),
                        'icon' => 'add'
                    ]
                ]])
            @else
                @include('entities.breadcrumbs', ['crumbs' => [
                    '/books' => [
                        'text' => trans('entities.books'),
                        'icon' => 'book'
                    ],
                    '/create-book' => [
                        'text' => trans('entities.books_create'),
                        'icon' => 'add'
                    ]
                ]])
            @endif
        </div>

        <main class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.books_create') }}</h1>
            <form action="{{ isset($bookshelf) ? $bookshelf->getUrl('/create-book') : url('/books') }}" method="POST" enctype="multipart/form-data">
                @include('books.parts.form', ['returnLocation' => isset($bookshelf) ? $bookshelf->getUrl() : url('/books')])
            </form>
        </main>
    </div>

@stop