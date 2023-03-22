@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', [
                'crumbs' => [
                    $book,
                    $book->getUrl('/copy') => [
                        'text' => trans('entities.books_copy'),
                        'icon' => 'copy',
                    ],
                ],
            ])
        </div>

        <div class="card content-wrap auto-height">

            <h1 class="list-heading">{{ trans('entities.books_copy') }}</h1>

            <form action="{{ $book->getUrl('/copy') }}" method="POST">
                {!! csrf_field() !!}

                <div class="form-group title-input">
                    <label for="name">{{ trans('common.name') }}</label>
                    @include('form.text', ['name' => 'name'])
                </div>

                @include('common.shelf-sort', [
                    'shelf' => null,
                    'books' => $bookshelf,
                    'shelfLabelOne' => 'entities.books_copy_shelves',
                    'shelfLabelOneDrag' => 'entities.book_grag_shelves',
                    'shelfLabelTwo' => 'entities.book_add_shelves',
                ])

                @include('entities.copy-considerations')

                <div class="form-group text-right">
                    <a href="{{ $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('entities.books_copy') }}</button>
                </div>
            </form>

        </div>
    </div>

@stop

