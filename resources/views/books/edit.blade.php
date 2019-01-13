@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl() => trans('entities.books_edit')
            ]])
        </div>

        <div class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.books_edit') }}</h1>
            <form action="{{ $book->getUrl() }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                @include('books/form', ['model' => $book])
            </form>
        </div>
    </div>

    @include('components.image-manager', ['imageType' => 'cover'])
@stop