@extends('simple-layout')

@section('body')
    <div class="container small">
        <div class="my-s">
            @if (isset($bookshelf))
                @include('partials.breadcrumbs', ['crumbs' => [
                    $bookshelf,
                    $bookshelf->getUrl('/create-book') => [
                        'text' => trans('entities.books_create'),
                        'icon' => 'add'
                    ]
                ]])
            @else
                @include('partials.breadcrumbs', ['crumbs' => [
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
                @include('books.form')
            </form>
        </main>
    </div>

@stop