@extends('tri-layout')

@section('container-attrs')
    id="entity-dashboard"
    entity-id="{{ $book->id }}"
    entity-type="book"
@stop

@section('body')

    <div class="content-wrap card">
        <h1 class="break-text" v-pre>{{$book->name}}</h1>
        <div class="book-content" v-show="!searching">
            <p class="text-muted" v-pre>{!! nl2br(e($book->description)) !!}</p>
            @if(count($bookChildren) > 0)
                <div class="entity-list book-contents" v-pre>
                    @foreach($bookChildren as $childElement)
                        @if($childElement->isA('chapter'))
                            @include('chapters.list-item', ['chapter' => $childElement])
                        @else
                            @include('pages.list-item', ['page' => $childElement])
                        @endif
                    @endforeach
                </div>
            @else
                <div class="well">
                    {{--TODO--}}
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
            {{--TODO--}}
            <h3 class="text-muted">{{ trans('entities.search_results') }} <a v-if="searching" v-on:click="clearSearch()" class="text-small">@icon('close'){{ trans('entities.search_clear') }}</a></h3>
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
                    <li><a href="{{ $book->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                    <li><a href="{{ $book->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                    <li><a href="{{ $book->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                </ul>
            </div>

            @if(userCan('page-create', $book))
                <a href="{{ $book->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif
            @if(userCan('chapter-create', $book))
                <a href="{{ $book->getUrl('/create-chapter') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.chapters_new') }}</span>
                </a>
            @endif
            @if(userCan('book-update', $book))
                <a href="{{ $book->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
                <a href="{{ $book->getUrl('/sort') }}" class="icon-list-item">
                    <span>@icon('sort')</span>
                    <span>{{ trans('common.sort') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $book))
                <a href="{{ $book->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('book-delete', $book))
                <a href="{{ $book->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif
        </div>
    </div>

@stop

@section('left')

    @if($book->tags->count() > 0)
        <div class="mb-xl">
            @include('components.tag-list', ['entity' => $book])
        </div>
    @endif

    <div class="mb-xl">
        <form v-on:submit.prevent="searchBook" class="search-box">
            <input v-model="searchTerm" v-on:change="checkSearchForm()" type="text" name="term" placeholder="{{ trans('entities.books_search_this') }}">
            <button type="submit">@icon('search')</button>
            <button v-if="searching" v-cloak class="text-neg" v-on:click="clearSearch()" type="button">@icon('close')</button>
        </form>
    </div>

    <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="text-small text-muted blended-links">
            @include('partials.entity-meta', ['entity' => $book])
            @if($book->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.books_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.books_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if(count($activity) > 0)
        <div class="mb-xl">
            <h5>{{ trans('entities.recent_activity') }}</h5>
            @include('partials.activity-list', ['activity' => $activity])
        </div>
    @endif
@stop

