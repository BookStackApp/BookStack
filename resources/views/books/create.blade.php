@extends('simple-layout')

@section('body')
    <div class="container small">
        <div class="my-s">
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
        </div>

        <div class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.books_create') }}</h1>
            <form action="{{ baseUrl("/books") }}" method="POST" enctype="multipart/form-data">
                @include('books/form')
            </form>
        </div>
    </div>

    @include('components.image-manager', ['imageType' => 'cover'])
@stop