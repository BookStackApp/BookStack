@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-6 col-xs-3 faded" v-pre>
        @include('chapters._breadcrumbs', ['chapter' => $chapter])
    </div>
    <div class="col-sm-6 col-xs-9 faded">
        <div class="action-buttons">
            <span dropdown class="dropdown-container">
                <div dropdown-toggle class="text-button text-primary">@icon('export'){{ trans('entities.export') }}</div>
                <ul class="wide">
                    <li><a href="{{ $chapter->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                    <li><a href="{{ $chapter->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                    <li><a href="{{ $chapter->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                </ul>
            </span>
            @if(userCan('page-create', $chapter))
                <a href="{{ $chapter->getUrl('/create-page') }}" class="text-pos text-button">@icon('add'){{ trans('entities.pages_new') }}</a>
            @endif
            @if(userCan('chapter-update', $chapter))
                <a href="{{ $chapter->getUrl('/edit') }}" class="text-primary text-button">@icon('edit'){{ trans('common.edit') }}</a>
            @endif
            @if(userCan('chapter-update', $chapter) || userCan('restrictions-manage', $chapter) || userCan('chapter-delete', $chapter))
                <div dropdown class="dropdown-container">
                    <a dropdown-toggle class="text-primary text-button">@icon('more') {{ trans('common.more') }}</a>
                    <ul>
                        @if(userCan('chapter-update', $chapter))
                            <li><a href="{{ $chapter->getUrl('/move') }}" class="text-primary">@icon('folder'){{ trans('common.move') }}</a></li>
                        @endif
                        @if(userCan('restrictions-manage', $chapter))
                            <li><a href="{{ $chapter->getUrl('/permissions') }}" class="text-primary">@icon('lock'){{ trans('entities.permissions') }}</a></li>
                        @endif
                        @if(userCan('chapter-delete', $chapter))
                            <li><a href="{{ $chapter->getUrl('/delete') }}" class="text-neg">@icon('delete'){{ trans('common.delete') }}</a></li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
@stop

@section('container-attrs')
    id="entity-dashboard"
    entity-id="{{ $chapter->id }}"
    entity-type="chapter"
@stop

@section('sidebar')
    <div class="card">
        <div class="body">
            <form @submit.prevent="searchBook" class="search-box">
                <input v-model="searchTerm" @change="checkSearchForm()" type="text" name="term" placeholder="{{ trans('entities.chapters_search_this') }}">
                <button type="submit">@icon('search')</button>
                <button v-if="searching" v-cloak class="text-neg" @click="clearSearch()" type="button">@icon('close')</button>
            </form>
        </div>
    </div>

    @if($book->restricted || $chapter->restricted)
        <div class="card">
            <h3>@icon('permission') {{ trans('entities.permissions') }}</h3>
            <div class="body">
                @if($book->restricted)
                    <p class="text-muted">
                        @if(userCan('restrictions-manage', $book))
                            <a href="{{ $book->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.books_permissions_active') }}</a>
                        @else
                            @icon('lock'){{ trans('entities.books_permissions_active') }}
                        @endif
                    </p>
                @endif

                @if($chapter->restricted)
                    <p class="text-muted">
                        @if(userCan('restrictions-manage', $chapter))
                            <a href="{{ $chapter->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.chapters_permissions_active') }}</a>
                        @else
                            @icon('lock'){{ trans('entities.chapters_permissions_active') }}
                        @endif
                    </p>
                @endif
            </div>
        </div>
    @endif


    @if($chapter->tags->count() > 0)
        <div class="card tag-display">
            <h3>@icon('tag') {{ trans('entities.chapter_tags') }}</h3>
            <div class="body">
                @include('components.tag-list', ['entity' => $chapter])
            </div>
        </div>
    @endif

    <div class="card">
        <h3>@icon('info') {{ trans('common.details') }}</h3>
        <div class="body">
            @include('partials.entity-meta', ['entity' => $chapter])
        </div>
    </div>

    @include('partials/book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])
@stop

@section('body')

    <div class="container small nopad">
        <h1 class="break-text" v-pre>{{ $chapter->name }}</h1>
        <div class="chapter-content" v-show="!searching">
            <p v-pre class="text-muted break-text">{!! nl2br(e($chapter->description)) !!}</p>

            @if(count($pages) > 0)
                <div v-pre class="page-list">
                    <hr>
                    @foreach($pages as $page)
                        @include('pages/list-item', ['page' => $page])
                        <hr>
                    @endforeach
                </div>
            @else
                <div v-pre class="well">
                    <p class="text-muted italic">{{ trans('entities.chapters_empty') }}</p>
                    <p>
                        @if(userCan('page-create', $chapter))
                            <a href="{{ $chapter->getUrl('/create-page') }}" class="button outline page">@icon('page'){{ trans('entities.books_empty_create_page') }}</a>
                        @endif
                        @if(userCan('page-create', $chapter) && userCan('book-update', $book))
                            &nbsp;&nbsp;<em class="text-muted">-{{ trans('entities.books_empty_or') }}-</em>&nbsp;&nbsp; &nbsp;
                        @endif
                        @if(userCan('book-update', $book))
                            <a href="{{ $book->getUrl('/sort') }}" class="button outline book">@icon('book'){{ trans('entities.books_empty_sort_current_book') }}</a>
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <div class="search-results" v-cloak v-show="searching">
            <h3 class="text-muted">{{ trans('entities.search_results') }} <a v-if="searching" @click="clearSearch()" class="text-small">@icon('close'){{ trans('entities.search_clear') }}</a></h3>
            <div v-if="!searchResults">
                @include('partials/loading-icon')
            </div>
            <div v-html="searchResults"></div>
        </div>
    </div>

@stop
