@extends('simple-layout')

@section('body')
    <div class="container small">
        <div class="breadcrumbs my-l">
            <a href="{{  baseUrl('/books')  }}" class="">
                @icon('book'){{ trans('entities.books') }}
            </a>
            <div class="separator">@icon('chevron-right')</div>
            <a href="{{  baseUrl('/create-book')  }}" class="">
                @icon('add'){{ trans('entities.books_create') }}
            </a>
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