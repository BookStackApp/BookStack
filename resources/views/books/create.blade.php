@extends('simple-layout')

@section('body')
    <div class="container small">
        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                '/books' => trans('entities.books'),
                '/create-book' => trans('entities.books_create')
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