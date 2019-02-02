@extends('simple-layout')

{{--TODO - Include links to search based on this user being the creator for each entity type--}}
{{--Linking either the "Created content" items or as "View All" links next to headers--}}
{{--TODO - Add shelves?--}}

@section('body')

    <div class="container pt-xl">

        <div class="grid right-focus reverse-collapse">

            <div>
                <div id="recent-user-activity" class="mb-xl">
                    <h5>{{ trans('entities.recent_activity') }}</h5>
                    @include('partials/activity-list', ['activity' => $activity])
                </div>
            </div>

            <div>
                <div class="card content-wrap auto-height">
                    <div class="grid left-focus v-center">
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
                            <div class="icon-list">
                                <a href="#recent-books" class="text-book icon-list-item">
                                    <span class="icon">@icon('book')</span>
                                    <span>{{ trans_choice('entities.x_books', $assetCounts['books']) }}</span>
                                </a>
                                <a href="#recent-chapters" class="text-chapter icon-list-item">
                                    <span class="icon">@icon('chapter')</span>
                                    <span>{{ trans_choice('entities.x_chapters', $assetCounts['chapters']) }}</span>
                                </a>
                                <a href="#recent-pages" class="text-page icon-list-item">
                                    <span class="icon">@icon('page')</span>
                                    <span>{{ trans_choice('entities.x_pages', $assetCounts['pages']) }}</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card content-wrap auto-height book-contents">
                    <h2 id="recent-pages" class="list-heading">{{ trans('entities.recently_created_pages') }}</h2>
                    @if (count($recentlyCreated['pages']) > 0)
                        @include('partials/entity-list', ['entities' => $recentlyCreated['pages']])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_pages', ['userName' => $user->name]) }}</p>
                    @endif
                </div>

                <div class="card content-wrap auto-height book-contents">
                    <h2 id="recent-chapters" class="list-heading">{{ trans('entities.recently_created_chapters') }}</h2>
                    @if (count($recentlyCreated['chapters']) > 0)
                        @include('partials/entity-list', ['entities' => $recentlyCreated['chapters']])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_chapters', ['userName' => $user->name]) }}</p>
                    @endif
                </div>

                <div class="card content-wrap auto-height book-contents">
                    <h2 id="recent-books" class="list-heading">{{ trans('entities.recently_created_books') }}</h2>
                    @if (count($recentlyCreated['books']) > 0)
                        @include('partials/entity-list', ['entities' => $recentlyCreated['books']])
                    @else
                        <p class="text-muted">{{ trans('entities.profile_not_created_books', ['userName' => $user->name]) }}</p>
                    @endif
                </div>
            </div>

        </div>


    </div>


@stop