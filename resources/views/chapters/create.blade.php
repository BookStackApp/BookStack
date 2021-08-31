@extends('layouts.simple')

@section('body')
    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $book,
                $book->getUrl('create-chapter') => [
                    'text' => trans('entities.chapters_create'),
                    'icon' => 'add',
                ]
            ]])
        </div>

        <main class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.chapters_create') }}</h1>
            <form action="{{ $book->getUrl('/create-chapter') }}" method="POST">
                @include('chapters.parts.form')
            </form>
        </main>

    </div>
@stop