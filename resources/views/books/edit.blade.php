@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('/edit') => [
                    'text' => trans('entities.books_edit'),
                    'icon' => 'edit',
                ]
            ]])
        </div>

        <main class="content-wrap card auto-height">
            <h1 class="list-heading">{{ trans('entities.books_edit') }}</h1>
            <form action="{{ $book->getUrl() }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                @include('books.parts.form', ['model' => $book, 'returnLocation' => $book->getUrl()])
            </form>
        </main>


        @if(userCan('book-delete', $book) && userCan('book-create-all') && userCan('bookshelf-create-all'))
            @include('books.parts.convert-to-shelf', ['book' => $book])
        @endif
    </div>
@stop