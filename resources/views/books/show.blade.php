@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 faded">
                    @include('books._breadcrumbs', ['book' => $book])
                </div>
                <div class="col-sm-6">
                    <div class="action-buttons faded">
                        <span dropdown class="dropdown-container">
                            <div dropdown-toggle class="text-button text-primary"><i class="zmdi zmdi-open-in-new"></i>{{ trans('entities.export') }}</div>
                            <ul class="wide">
                                <li><a href="{{ $book->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                                <li><a href="{{ $book->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                                <li><a href="{{ $book->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                            </ul>
                        </span>
                        @if(userCan('page-create', $book))
                            <a href="{{ $book->getUrl('/page/create') }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.pages_new') }}</a>
                        @endif
                        @if(userCan('chapter-create', $book))
                            <a href="{{ $book->getUrl('/chapter/create') }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.chapters_new') }}</a>
                        @endif
                        @if(userCan('book-update', $book))
                            <a href="{{$book->getEditUrl()}}" class="text-primary text-button"><i class="zmdi zmdi-edit"></i>{{ trans('common.edit') }}</a>
                        @endif
                        @if(userCan('book-update', $book) || userCan('restrictions-manage', $book) || userCan('book-delete', $book))
                            <div dropdown class="dropdown-container">
                                <a dropdown-toggle class="text-primary text-button"><i class="zmdi zmdi-more-vert"></i></a>
                                <ul>
                                    @if(userCan('book-update', $book))
                                        <li><a href="{{ $book->getUrl('/sort') }}" class="text-primary"><i class="zmdi zmdi-sort"></i>{{ trans('common.sort') }}</a></li>
                                    @endif
                                    @if(userCan('restrictions-manage', $book))
                                        <li><a href="{{ $book->getUrl('/permissions') }}" class="text-primary"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.permissions') }}</a></li>
                                    @endif
                                    @if(userCan('book-delete', $book))
                                        <li><a href="{{ $book->getUrl('/delete') }}" class="text-neg"><i class="zmdi zmdi-delete"></i>{{ trans('common.delete') }}</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" id="entity-dashboard" entity-id="{{ $book->id }}" entity-type="book">
        <div class="row">
            <div class="col-md-7">

                <h1>{{$book->name}}</h1>
                <div class="book-content" v-if="!searching">
                    <p class="text-muted" v-pre>{!! nl2br($book->description) !!}</p>

                    <div class="page-list" v-pre>
                        <hr>
                        @if(count($bookChildren) > 0)
                            @foreach($bookChildren as $childElement)
                                @if($childElement->isA('chapter'))
                                    @include('chapters/list-item', ['chapter' => $childElement])
                                @else
                                    @include('pages/list-item', ['page' => $childElement])
                                @endif
                                <hr>
                            @endforeach
                        @else
                            <p class="text-muted">{{ trans('entities.books_empty_contents') }}</p>
                            <p>
                                @if(userCan('page-create', $book))
                                <a href="{{ $book->getUrl('/page/create') }}" class="text-page"><i class="zmdi zmdi-file-text"></i>{{ trans('entities.books_empty_create_page') }}</a>
                                @endif
                                @if(userCan('page-create', $book) && userCan('chapter-create', $book))
                                &nbsp;&nbsp;<em class="text-muted">-{{ trans('entities.books_empty_or') }}-</em>&nbsp;&nbsp;&nbsp;
                                @endif
                                @if(userCan('chapter-create', $book))
                                <a href="{{ $book->getUrl('/chapter/create') }}" class="text-chapter"><i class="zmdi zmdi-collection-bookmark"></i>{{ trans('entities.books_empty_add_chapter') }}</a>
                                @endif
                            </p>
                            <hr>
                        @endif
                        @include('partials.entity-meta', ['entity' => $book])
                    </div>
                </div>
                <div class="search-results" v-cloak v-if="searching">
                    <h3 class="text-muted">{{ trans('entities.search_results') }} <a v-if="searching" v-on:click="clearSearch()" class="text-small"><i class="zmdi zmdi-close"></i>{{ trans('entities.search_clear') }}</a></h3>
                    <div v-if="!searchResults">
                        @include('partials/loading-icon')
                    </div>
                    <div v-html="searchResults"></div>
                </div>


            </div>

            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large"></div>

                @if($book->restricted)
                    <p class="text-muted">
                        @if(userCan('restrictions-manage', $book))
                            <a href="{{ $book->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}</a>
                        @else
                            <i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}
                        @endif
                    </p>
                @endif

                <div class="search-box">
                    <form v-on:submit="searchBook">
                        <input v-model="searchTerm" v-on:change="checkSearchForm()" type="text" name="term" placeholder="{{ trans('entities.books_search_this') }}">
                        <button type="submit"><i class="zmdi zmdi-search"></i></button>
                        <button v-if="searching" v-cloak class="text-neg" v-on:click="clearSearch()" type="button"><i class="zmdi zmdi-close"></i></button>
                    </form>
                </div>

                <div class="activity">
                    <h3>{{ trans('entities.recent_activity') }}</h3>
                    @include('partials/activity-list', ['activity' => Activity::entityActivity($book, 20, 0)])
                </div>
            </div>
        </div>
    </div>

@stop
