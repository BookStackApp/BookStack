@extends('tri-layout')

@section('container-attrs')
    id="entity-dashboard"
    entity-id="{{ $chapter->id }}"
    entity-type="chapter"
@stop

@section('body')

    <div class="mb-m">
        @include('partials.breadcrumbs', ['crumbs' => [
            $chapter->book,
            $chapter,
        ]])
    </div>

    <div class="content-wrap card">
        <h1 class="break-text" v-pre>{{ $chapter->name }}</h1>
        <div class="chapter-content" v-show="!searching">
            <p v-pre class="text-muted break-text">{!! nl2br(e($chapter->description)) !!}</p>
            @if(count($pages) > 0)
                <div v-pre class="entity-list book-contents">
                    @foreach($pages as $page)
                        @include('pages.list-item', ['page' => $page])
                    @endforeach
                </div>
            @else
                {{--TODO--}}
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
            {{--TODO--}}
            <h3 class="text-muted">{{ trans('entities.search_results') }} <a v-if="searching" @click="clearSearch()" class="text-small">@icon('close'){{ trans('entities.search_clear') }}</a></h3>
            <div v-if="!searchResults">
                @include('partials/loading-icon')
            </div>
            <div v-html="searchResults"></div>
        </div>
    </div>

@stop

@section('right')

    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">

            <div dropdown class="dropdown-container">
                <div dropdown-toggle class="icon-list-item">
                    <span>@icon('export')</span>
                    <span>{{ trans('entities.export') }}</span>
                </div>
                <ul class="wide">
                    <li><a href="{{ $chapter->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                    <li><a href="{{ $chapter->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                    <li><a href="{{ $chapter->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                </ul>
            </div>

            @if(userCan('page-create', $chapter))
                <a href="{{ $chapter->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif
            @if(userCan('chapter-update', $chapter))
                <a href="{{ $chapter->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
            @endif
            @if(userCan('chapter-update', $chapter) && userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/move') }}" class="icon-list-item">
                    <span>@icon('folder')</span>
                    <span>{{ trans('common.move') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $chapter))
                <a href="{{ $chapter->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('chapter-delete', $chapter))
                <a href="{{ $chapter->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif

            {{--@if(userCan('page-create', $book))--}}
                {{--<a href="{{ $book->getUrl('/create-page') }}" class="icon-list-item">--}}
                    {{--<span>@icon('add')</span>--}}
                    {{--<span>{{ trans('entities.pages_new') }}</span>--}}
                {{--</a>--}}
            {{--@endif--}}
        </div>
    </div>

    <div class="col-sm-6 col-xs-9 faded">
        <div class="action-buttons">

        </div>
    </div>
@stop



@section('left')

    @if($chapter->tags->count() > 0)
        <div class="mb-xl">
            @include('components.tag-list', ['entity' => $chapter])
        </div>
    @endif

    <div class="mb-xl">
        <form @submit.prevent="searchBook" class="search-box">
            <input v-model="searchTerm" @change="checkSearchForm()" type="text" name="term" placeholder="{{ trans('entities.chapters_search_this') }}">
            <button type="submit">@icon('search')</button>
            <button v-if="searching" v-cloak class="text-neg" @click="clearSearch()" type="button">@icon('close')</button>
        </form>
    </div>

    <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="blended-links text-small text-muted">
            @include('partials.entity-meta', ['entity' => $chapter])

            @if($book->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.books_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.books_permissions_active') }}
                    @endif
                </div>
            @endif

            @if($chapter->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $chapter))
                        <a href="{{ $chapter->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.chapters_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.chapters_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    @include('partials/book-tree', ['book' => $book, 'sidebarTree' => $sidebarTree])
@stop


