@extends('simple-layout')

@section('body')

    <div class="container pt-xl">

        <div class="grid right-focus reverse-collapse">

            <div>
                <div id="recent-user-activity" class="mb-xl">
                    <h5>{{ trans('entities.recent_activity') }}</h5>
                    @include('partials.activity-list', ['activity' => $activity])
                </div>
            </div>

            <div>
                <div class="card content-wrap auto-height">
                    <div class="grid half v-center">
                        <div>
                            <div class="mr-m float left">
                                <img class="avatar square huge" src="{{ $user->getAvatar(120) }}" alt="{{ $user->name }}">
                            </div>
                            <div>
                                <h4 class="mt-md">{{ $user->name }}</h4>
                                <p class="text-muted">
                                    {{ trans('entities.profile_user_for_x', ['time' => $user->created_at->diffForHumans(null, true)]) }}
                                </p>
                            </div>
                        </div>
                        <div id="content-counts">
                            <div class="text-muted">{{ trans('entities.profile_created_content') }}</div>
                            <div class="grid half v-center no-row-gap">
                                <div class="icon-list">
                                    <a href="#recent-pages" class="text-page icon-list-item">
                                        <span>@icon('page')</span>
                                        <span>{{ trans_choice('entities.x_pages', $assetCounts['pages']) }}</span>
                                    </a>
                                    <a href="#recent-chapters" class="text-chapter icon-list-item">
                                        <span>@icon('chapter')</span>
                                        <span>{{ trans_choice('entities.x_chapters', $assetCounts['chapters']) }}</span>
                                    </a>
                                </div>
                                <div class="icon-list">
                                    <a href="#recent-books" class="text-book icon-list-item">
                                        <span>@icon('book')</span>
                                        <span>{{ trans_choice('entities.x_books', $assetCounts['books']) }}</span>
                                    </a>
                                    <a href="#recent-shelves" class="text-bookshelf icon-list-item">
                                        <span>@icon('bookshelf')</span>
                                        <span>{{ trans_choice('entities.x_shelves', $assetCounts['shelves']) }}</span>
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card content-wrap auto-height book-contents">
                    <h2 id="recent-pages" class="list-heading">
                        {{ trans('entities.recently_created_pages') }}
                        @if (count($recentlyCreated['pages']) > 0)
                            <a href="{{ baseUrl('/search?term=' . urlencode('{created_by:'.$user->id.'} {type:page}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>
                        @endif
                    </h2>
                    @if (count($recentlyCreated['pages']) > 0)
                        @include('partials.entity-list', ['entities' => $recentlyCreated['pages'], 'showPath' => true])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_pages', ['userName' => $user->name]) }}</p>
                    @endif
                </div>

                <div class="card content-wrap auto-height book-contents">
                    <h2 id="recent-chapters" class="list-heading">
                        {{ trans('entities.recently_created_chapters') }}
                        @if (count($recentlyCreated['chapters']) > 0)
                            <a href="{{ baseUrl('/search?term=' . urlencode('{created_by:'.$user->id.'} {type:chapter}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>
                        @endif
                    </h2>
                    @if (count($recentlyCreated['chapters']) > 0)
                        @include('partials.entity-list', ['entities' => $recentlyCreated['chapters'], 'showPath' => true])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_chapters', ['userName' => $user->name]) }}</p>
                    @endif
                </div>

                <div class="card content-wrap auto-height book-contents">
                    <h2 id="recent-books" class="list-heading">
                        {{ trans('entities.recently_created_books') }}
                        @if (count($recentlyCreated['books']) > 0)
                            <a href="{{ baseUrl('/search?term=' . urlencode('{created_by:'.$user->id.'} {type:book}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>
                        @endif
                    </h2>
                    @if (count($recentlyCreated['books']) > 0)
                        @include('partials.entity-list', ['entities' => $recentlyCreated['books'], 'showPath' => true])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_books', ['userName' => $user->name]) }}</p>
                    @endif
                </div>

                <div class="card content-wrap auto-height book-contents">
                    <h2 id="recent-shelves" class="list-heading">
                        {{ trans('entities.recently_created_shelves') }}
                        @if (count($recentlyCreated['shelves']) > 0)
                            <a href="{{ baseUrl('/search?term=' . urlencode('{created_by:'.$user->id.'} {type:bookshelf}') ) }}" class="text-small ml-s">{{ trans('common.view_all') }}</a>
                        @endif
                    </h2>
                    @if (count($recentlyCreated['shelves']) > 0)
                        @include('partials.entity-list', ['entities' => $recentlyCreated['shelves'], 'showPath' => true])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_shelves', ['userName' => $user->name]) }}</p>
                    @endif
                </div>
            </div>

        </div>


    </div>


@stop