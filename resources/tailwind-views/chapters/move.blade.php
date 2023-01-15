@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $chapter->book,
                $chapter,
                $chapter->getUrl('/move') => [
                    'text' => trans('entities.chapters_move'),
                    'icon' => 'folder',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.chapters_move') }}</h1>

            <form action="{{ $chapter->getUrl('/move') }}" method="POST">

                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="PUT">

                @include('entities.selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book', 'entityPermission' => 'chapter-create'])

                <div class="form-group text-right">
                    <a href="{{ $chapter->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('entities.chapters_move') }}</button>
                </div>
            </form>

        </main>



    </div>

@stop
