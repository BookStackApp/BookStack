@extends('layouts.simple')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('/sort') => [
                    'text' => trans('entities.books_sort'),
                    'icon' => 'sort',
                ]
            ]])
        </div>

        <div class="grid left-focus gap-xl">
            <div>
                <div component="book-sort" class="card content-wrap auto-height">
                    <h1 class="list-heading">{{ trans('entities.books_sort') }}</h1>
                    <p class="text-muted">{{ trans('entities.books_sort_desc') }}</p>

                    <div refs="book-sort@sortContainer">
                        @include('books.parts.sort-box', ['book' => $book, 'bookChildren' => $bookChildren])
                    </div>

                    <form action="{{ $book->getUrl('/sort') }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input refs="book-sort@input" type="hidden" name="sort-tree">
                        <div class="list text-right">
                            <a href="{{ $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                            <button class="button" type="submit">{{ trans('entities.books_sort_save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <main class="card content-wrap auto-height sticky-top-m">
                    <h2 class="list-heading">{{ trans('entities.books_sort_show_other') }}</h2>
                    <p class="text-muted">{{ trans('entities.books_sort_show_other_desc') }}</p>

                    @include('entities.selector', ['name' => 'books_list', 'selectorSize' => 'compact', 'entityTypes' => 'book', 'entityPermission' => 'update'])

                </main>
            </div>
        </div>

    </div>

@stop
