@extends('simple-layout')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('/sort') => [
                    'text' => trans('entities.books_sort'),
                    'icon' => 'sort',
                ]
            ]])
        </div>

        <div class="grid left-focus gap-xl">
            <div>
                <div book-sort class="card content-wrap">
                    <h1 class="list-heading mb-l">{{ trans('entities.books_sort') }}</h1>
                    <div book-sort-boxes>
                        @include('books.sort-box', ['book' => $book, 'bookChildren' => $bookChildren])
                    </div>

                    <form action="{{ $book->getUrl('/sort') }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input book-sort-input type="hidden" name="sort-tree">
                        <div class="list text-right">
                            <a href="{{ $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                            <button class="button" type="submit">{{ trans('entities.books_sort_save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <main class="card content-wrap">
                    <h2 class="list-heading mb-m">{{ trans('entities.books_sort_show_other') }}</h2>

                    @include('components.entity-selector', ['name' => 'books_list', 'selectorSize' => 'compact', 'entityTypes' => 'book', 'entityPermission' => 'update', 'showAdd' => true])

                </main>
            </div>
        </div>

    </div>

@stop
