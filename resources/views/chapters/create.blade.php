@extends('simple-layout')

@section('body')
    <div class="container small">

        <div class="my-s">
            @include('partials.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('create-chapter') => [
                    'text' => trans('entities.chapters_create'),
                    'icon' => 'add',
                ]
            ]])
        </div>

        <div class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.chapters_create') }}</h1>
            <form action="{{ $book->getUrl('/create-chapter') }}" method="POST">
                @include('chapters.form')
            </form>
        </div>

    </div>
@stop