@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-6 col-xs-1  faded">
        @include('books._breadcrumbs', ['book' => $book])
    </div>
    <div class="col-sm-6 col-xs-11">
        <div class="action-buttons faded">
            <span dropdown class="dropdown-container">
                <div dropdown-toggle class="text-button text-primary">@icon('export'){{ trans('entities.export') }}</div>
                <ul class="wide">
                    <li><a href="{{ $book->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                    <li><a href="{{ $book->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                    <li><a href="{{ $book->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                </ul>
            </span>
            @if(userCan('page-create', $book))
                <a href="{{ $book->getUrl('/create-page') }}" class="text-pos text-button">@icon('add'){{ trans('entities.pages_new') }}</a>
            @endif
            @if(userCan('chapter-create', $book))
                <a href="{{ $book->getUrl('/create-chapter') }}" class="text-pos text-button">@icon('add'){{ trans('entities.chapters_new') }}</a>
            @endif
            @if(userCan('book-update', $book) || userCan('restrictions-manage', $book) || userCan('book-delete', $book))
                <div dropdown class="dropdown-container">
                    <a dropdown-toggle class="text-primary text-button">@icon('more'){{ trans('common.more') }}</a>
                    <ul>
                        @if(userCan('book-update', $book))
                            <li><a href="{{$book->getEditUrl()}}" class="text-primary">@icon('edit'){{ trans('common.edit') }}</a></li>
                            <li><a href="{{ $book->getUrl('/sort') }}" class="text-primary">@icon('sort'){{ trans('common.sort') }}</a></li>
                        @endif
                        @if(userCan('restrictions-manage', $book))
                            <li><a href="{{ $book->getUrl('/permissions') }}" class="text-primary">@icon('lock'){{ trans('entities.permissions') }}</a></li>
                        @endif
                        @if(userCan('book-delete', $book))
                            <li><a href="{{ $book->getUrl('/delete') }}" class="text-neg">@icon('delete'){{ trans('common.delete') }}</a></li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>
    </div>
@stop

@section('sidebar')

    <div class="card">
        <div class="body">
            <form v-on:submit.prevent="searchBook" class="search-box">
                <input v-model="searchTerm" v-on:change="checkSearchForm()" type="text" name="term" placeholder="{{ trans('entities.books_search_this') }}">
                <button type="submit">@icon('search')</button>
                <button v-if="searching" v-cloak class="text-neg" v-on:click="clearSearch()" type="button">@icon('close')</button>
            </form>
        </div>
    </div>

    @if($book->restricted)
        <div class="card">
            <h3>@icon('permission') {{ trans('entities.permissions') }}</h3>
            <div class="body">
                <p class="text-muted">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.books_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.books_permissions_active') }}
                    @endif
                </p>
            </div>
        </div>
    @endif

    @if(count($activity) > 0)
        <div class="activity card">
            <h3>@icon('time') {{ trans('entities.recent_activity') }}</h3>
            @include('partials/activity-list', ['activity' => $activity])
        </div>
    @endif

    <div class="card">
        <h3>@icon('info') {{ trans('common.details') }}</h3>
        <div class="body">
            @include('partials.entity-meta', ['entity' => $book])
        </div>
    </div>
@stop

@section('container-attrs')
    id="entity-dashboard"
    entity-id="{{ $book->id }}"
    entity-type="book"
@stop

@section('body')

    <div class="container small nopad">
        <h1 class="break-text" v-pre>{{$book->name}}</h1>
        <div class="book-content" v-show="!searching">
            <p class="text-muted" v-pre>{!! nl2br(e($book->description)) !!}</p>
            @if(count($bookChildren) > 0)
            <div class="page-list" v-pre>
                <hr>
                @foreach($bookChildren as $childElement)
                    @if($childElement->isA('chapter'))
                        @include('chapters/list-item', ['chapter' => $childElement])
                    @else
                        @include('pages/list-item', ['page' => $childElement])
                    @endif
                    <hr>
                @endforeach
            </div>
            @else
                <div class="well">
                    <p class="text-muted italic">{{ trans('entities.books_empty_contents') }}</p>
                        @if(userCan('page-create', $book))
                            <a href="{{ $book->getUrl('/create-page') }}" class="button outline page">@icon('page'){{ trans('entities.books_empty_create_page') }}</a>
                        @endif
                        @if(userCan('page-create', $book) && userCan('chapter-create', $book))
                            &nbsp;&nbsp;<em class="text-muted">-{{ trans('entities.books_empty_or') }}-</em>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if(userCan('chapter-create', $book))
                            <a href="{{ $book->getUrl('/create-chapter') }}" class="button outline chapter">@icon('chapter'){{ trans('entities.books_empty_add_chapter') }}</a>
                        @endif
                </div>
            @endif

        </div>
        <div class="search-results" v-cloak v-show="searching">
            <h3 class="text-muted">{{ trans('entities.search_results') }} <a v-if="searching" v-on:click="clearSearch()" class="text-small">@icon('close'){{ trans('entities.search_clear') }}</a></h3>
            <div v-if="!searchResults">
                @include('partials/loading-icon')
            </div>
            <div v-html="searchResults"></div>
        </div>
    </div>

@stop
