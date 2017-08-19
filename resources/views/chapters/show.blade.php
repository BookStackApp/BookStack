@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-xs-3 faded" ng-non-bindable>
                    @include('chapters._breadcrumbs', ['chapter' => $chapter])
                </div>
                <div class="col-sm-6 col-xs-9 faded">
                    <div class="action-buttons">
                        <span dropdown class="dropdown-container">
                            <div dropdown-toggle class="text-button text-primary"><i class="zmdi zmdi-open-in-new"></i>{{ trans('entities.export') }}</div>
                            <ul class="wide">
                                <li><a href="{{ $chapter->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                                <li><a href="{{ $chapter->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                                <li><a href="{{ $chapter->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                            </ul>
                        </span>
                        @if(userCan('page-create', $chapter))
                            <a href="{{ $chapter->getUrl('/create-page') }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.pages_new') }}</a>
                        @endif
                        @if(userCan('chapter-update', $chapter))
                            <a href="{{ $chapter->getUrl('/edit') }}" class="text-primary text-button"><i class="zmdi zmdi-edit"></i>{{ trans('common.edit') }}</a>
                        @endif
                        @if(userCan('chapter-update', $chapter) || userCan('restrictions-manage', $chapter) || userCan('chapter-delete', $chapter))
                            <div dropdown class="dropdown-container">
                                <a dropdown-toggle class="text-primary text-button"><i class="zmdi zmdi-more-vert"></i></a>
                                <ul>
                                    @if(userCan('chapter-update', $chapter))
                                        <li><a href="{{ $chapter->getUrl('/move') }}" class="text-primary"><i class="zmdi zmdi-folder"></i>{{ trans('common.move') }}</a></li>
                                    @endif
                                    @if(userCan('restrictions-manage', $chapter))
                                        <li><a href="{{ $chapter->getUrl('/permissions') }}" class="text-primary"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.permissions') }}</a></li>
                                    @endif
                                    @if(userCan('chapter-delete', $chapter))
                                        <li><a href="{{ $chapter->getUrl('/delete') }}" class="text-neg"><i class="zmdi zmdi-delete"></i>{{ trans('common.delete') }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" id="entity-dashboard" ng-non-bindable entity-id="{{ $chapter->id }}" entity-type="chapter">
        <div class="row">
            <div class="col-md-7">
                <h1>{{ $chapter->name }}</h1>
                <div class="chapter-content" v-show="!searching">
                    <p class="text-muted">{!! nl2br(e($chapter->description)) !!}</p>

                    @if(count($pages) > 0)
                        <div class="page-list">
                            <hr>
                            @foreach($pages as $page)
                                @include('pages/list-item', ['page' => $page])
                                <hr>
                            @endforeach
                        </div>
                    @else
                        <hr>
                        <p class="text-muted">{{ trans('entities.chapters_empty') }}</p>
                        <p>
                            @if(userCan('page-create', $chapter))
                                <a href="{{ $chapter->getUrl('/create-page') }}" class="text-page"><i class="zmdi zmdi-file-text"></i>{{ trans('entities.books_empty_create_page') }}</a>
                            @endif
                            @if(userCan('page-create', $chapter) && userCan('book-update', $book))
                                &nbsp;&nbsp;<em class="text-muted">-{{ trans('entities.books_empty_or') }}-</em>&nbsp;&nbsp;&nbsp;
                            @endif
                            @if(userCan('book-update', $book))
                                <a href="{{ $book->getUrl('/sort') }}" class="text-book"><i class="zmdi zmdi-book"></i>{{ trans('entities.books_empty_sort_current_book') }}</a>
                            @endif
                        </p>
                        <hr>
                    @endif

                    @include('partials.entity-meta', ['entity' => $chapter])
                </div>

                <div class="search-results" v-cloak v-show="searching">
                    <h3 class="text-muted">{{ trans('entities.search_results') }} <a v-if="searching" v-on:click="clearSearch()" class="text-small"><i class="zmdi zmdi-close"></i>{{ trans('entities.search_clear') }}</a></h3>
                    <div v-if="!searchResults">
                        @include('partials/loading-icon')
                    </div>
                    <div v-html="searchResults"></div>
                </div>
            </div>
            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large"></div>
                @if($book->restricted || $chapter->restricted)
                    <div class="text-muted">

                        @if($book->restricted)
                            <p class="text-muted">
                                @if(userCan('restrictions-manage', $book))
                                    <a href="{{ $book->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}</a>
                                @else
                                    <i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}
                                @endif
                            </p>
                        @endif

                        @if($chapter->restricted)
                            @if(userCan('restrictions-manage', $chapter))
                                <a href="{{ $chapter->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.chapters_permissions_active') }}</a>
                            @else
                                <i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.chapters_permissions_active') }}
                            @endif
                        @endif
                    </div>
                @endif

                <div class="search-box">
                    <form v-on:submit.prevent="searchBook">
                        <input v-model="searchTerm" v-on:change="checkSearchForm()" type="text" name="term" placeholder="{{ trans('entities.chapters_search_this') }}">
                        <button type="submit"><i class="zmdi zmdi-search"></i></button>
                        <button v-if="searching" v-cloak class="text-neg" v-on:click="clearSearch()" type="button"><i class="zmdi zmdi-close"></i></button>
                    </form>
                </div>

                @include('pages/sidebar-tree-list', ['book' => $book, 'sidebarTree' => $sidebarTree])

            </div>
        </div>
    </div>




@stop
