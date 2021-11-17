@extends('layouts.simple')

@section('body')

    <div class="container">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $chapter->book,
                $chapter,
                $chapter->getUrl('/sort') => [
                    'text' => trans('entities.chapters_sort'),
                    'icon' => 'sort',
                ]
            ]])
        </div>

        <div class="grid left-focus gap-xl">
            <div>
                <div chapter-sort class="card content-wrap">
                    <h1 class="list-heading mb-l">{{ trans('entities.chapters_sort') }}</h1>
                    <div chapter-sort-boxes>
                        @include('chapters.parts.sort-box', ['chapter' => $chapter, 'chapterChildren' => $chapterChildren])
                    </div>

                    <form action="{{ $chapter->getUrl('/sort') }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="hidden" name="_method" value="PUT">
                        <input chapter-sort-input type="hidden" name="sort-tree">
                        <div class="list text-right">
                            <a href="{{ $chapter->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
                            <button class="button" type="submit">{{ trans('entities.chapters_sort_save') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <main class="card content-wrap">
                    <h2 class="list-heading mb-m">{{ trans('entities.chapters_sort_show_other') }}</h2>

                    @include('entities.selector', ['name' => 'chapterss_list', 'selectorSize' => 'compact', 'entityTypes' => 'chapter', 'entityPermission' => 'update', 'showAdd' => true])

                </main>
            </div>
        </div>

    </div>

@stop