@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $page->book,
                $page->chapter,
                $page,
                $page->getUrl('/move') => [
                    'text' => trans('entities.pages_move'),
                    'icon' => 'folder',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.pages_move') }}</h1>

            <form action="{{ $page->getUrl('/move') }}" method="POST">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="PUT">

                @include('entities.selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book,chapter', 'entityPermission' => 'page-create', 'autofocus' => true])

                <div class="form-group text-right">
                    <a href="{{ $page->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('entities.pages_move') }}</button>
                </div>
            </form>

        </main>
    </div>

@stop
