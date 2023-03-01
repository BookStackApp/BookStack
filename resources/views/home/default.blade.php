@extends('layouts.simple')

@section('body')
    <div class="container px-xl py-s">
        <div class="grid half no-gap">
            <div>
                <div class="icon-list inline block">
                    @include('home.parts.expand-toggle', [
                        'classes' => 'text-muted text-link',
                        'target' => '.entity-list.compact .entity-item-snippet',
                        'key' => 'home-details',
                    ])
                </div>
            </div>
            {{-- <div class="text-m-right">
                <div class="icon-list inline block">
                    @include('common.dark-mode-toggle', [
                        'classes' => 'text-muted icon-list-item text-link',
                    ])
                </div>
            </div> --}}
        </div>
    </div>
    <div class="container" id="home-default">
        <div class="grid third gap-x-xxl no-row-gap">
            <div>
                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">Quick Links ⚡️</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                            'entities' => $quickLinks,
                            'style' => 'compact',
                            'emptyText' => trans('entities.no_pages_recently_updated'),
                            'showQuickAdd' => true
                        ])
                    </div>
                </div>
                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">Symbol Types 📚</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                            'entities' => $symbolTypesList,
                            'style' => 'compact',
                            'emptyText' => trans('entities.no_pages_recently_updated'),
                            'showQuickAdd' => false
                        ])
                    </div>
                </div>
            </div>
            <div>
                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">Newest Symbols 🌅</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                            'entities' => $newSymbols,
                            'style' => 'compact',
                            'emptyText' => trans('entities.no_pages_recently_updated'),
                        ])
                    </div>
                    <a href="/pages/newest-symbols"
                    class="card-footer-link">{{ trans('common.view_all') }}</a>
                </div>
                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">Ready For Review 🤓</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                            'entities' => $latestCommunityReviews,
                            'style' => 'compact',
                            'emptyText' => trans('entities.no_pages_recently_updated'),
                        ])
                    </div>
                    <a href="{{ url('/books/community-review') }}"
                        class="card-footer-link">{{ trans('common.view_all') }}</a>
                </div>
            </div>
            <div>
                <div id="recent-activity">
                    <div class="card mb-xl">
                        <h3 class="card-title">Recently Active Users 👤</h3>
                        @include('common.activity-list', ['activity' => $activeUsers])
                        <a href="{{ url('/users/all') }}"
                            class="card-footer-link">{{ trans('common.view_all') }}</a>
                    </div>
                </div>
                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">Latest Drafts 📝</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                            'entities' => $latestDrafts,
                            'style' => 'compact',
                            'emptyText' => trans('entities.no_pages_recently_updated'),
                        ])
                    </div>
                    <a href="{{ url('/pages/drafts-recently-updated') }}"
                        class="card-footer-link">{{ trans('common.view_all') }}</a>
                </div>
                <div id="recent-pages" class="card mb-xl">
                    <h3 class="card-title">Recently Edited Symbols ✏️</h3>
                    <div id="recently-updated-pages" class="px-m">
                        @include('entities.list', [
                            'entities' => $recentUpdates,
                            'style' => 'compact',
                            'showPath' => true,
                            'showUpdatedBy' => true,
                            'showExcerpt' => false,
                            'emptyText' => trans('entities.no_pages_recently_updated'),
                        ])
                    </div>
                    <a href="{{ url('/pages/symbols-recently-updated') }}"
                        class="card-footer-link">{{ trans('common.view_all') }}</a>
                </div>
            </div>
        </div>
    </div>
@stop
