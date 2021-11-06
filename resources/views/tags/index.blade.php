@extends('layouts.simple')

@section('body')
    <div class="container small">

        <main class="card content-wrap mt-xxl">

            <div class="flex-container-row wrap justify-space-between items-center mb-s">
                <h1 class="list-heading">{{ trans('entities.tags') }}</h1>

                <div>
                    <div class="block inline mr-xs">
                        <form method="get" action="{{ url("/tags") }}">
                            @include('form.request-query-inputs', ['params' => ['page', 'name']])
                            <input type="text"
                                   name="search"
                                   placeholder="{{ trans('common.search') }}"
                                   value="{{ $search }}">
                        </form>
                    </div>
                </div>
            </div>

            @if($nameFilter)
                <div class="mb-m">
                    <span class="mr-xs">{{ trans('common.filter_active') }}</span>
                    @include('entities.tag', ['tag' => new \BookStack\Actions\Tag(['name' => $nameFilter])])
                    <form method="get" action="{{ url("/tags") }}" class="inline block">
                        @include('form.request-query-inputs', ['params' => ['search']])
                        <button class="text-button text-warn">@icon('close'){{ trans('common.filter_clear') }}</button>
                    </form>
                </div>
            @endif


            <table class="table expand-to-padding mt-m">
                @foreach($tags as $tag)
                    <tr>
                        <td>
                            <span class="text-bigger mr-xl">@include('entities.tag', ['tag' => $tag])</span>
                        </td>
                        <td width="60" class="px-xs">
                            <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() }}"
                               title="{{ trans('entities.tags_usages') }}"
                               class="pill text-muted">@icon('leaderboard'){{ $tag->usages }}</a>
                        </td>
                        <td width="60" class="px-xs">
                            <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:page}' }}"
                               title="{{ trans('entities.tags_assigned_pages') }}"
                               class="pill text-page">@icon('page'){{ $tag->page_count }}</a>
                        </td>
                        <td width="60" class="px-xs">
                            <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:chapter}' }}"
                               title="{{ trans('entities.tags_assigned_chapters') }}"
                               class="pill text-chapter">@icon('chapter'){{ $tag->chapter_count }}</a>
                        </td>
                        <td width="60" class="px-xs">
                            <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:book}' }}"
                               title="{{ trans('entities.tags_assigned_books') }}"
                               class="pill text-book">@icon('book'){{ $tag->book_count }}</a>
                        </td>
                        <td width="60" class="px-xs">
                            <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:bookshelf}' }}"
                               title="{{ trans('entities.tags_assigned_shelves') }}"
                               class="pill text-bookshelf">@icon('bookshelf'){{ $tag->shelf_count }}</a>
                        </td>
                        <td class="text-right text-muted">
                            @if($tag->values ?? false)
                                <a href="{{ url('/tags?name=' . urlencode($tag->name)) }}">{{ trans('entities.tags_x_unique_values', ['count' => $tag->values]) }}</a>
                            @elseif(empty($nameFilter))
                                <a href="{{ url('/tags?name=' . urlencode($tag->name)) }}">{{ trans('entities.tags_all_values') }}</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

            <div>
                {{ $tags->links() }}
            </div>
        </main>

    </div>

@stop
