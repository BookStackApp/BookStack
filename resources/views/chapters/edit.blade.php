@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $book,
                $chapter,
                $chapter->getUrl('/edit') => [
                    'text' => trans('entities.chapters_edit'),
                    'icon' => 'edit'
                ]
            ]])
        </div>

        <main class="content-wrap card auto-height">
            <h1 class="list-heading">{{ trans('entities.chapters_edit') }}</h1>
            <form action="{{  $chapter->getUrl() }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                @include('chapters.parts.form', ['model' => $chapter])
            </form>
        </main>

        @if(userCan('chapter-delete', $chapter) && userCan('book-create-all'))
            @include('chapters.parts.convert-to-book')
        @endif

    </div>

@stop