@if(count($draftPages) > 0)
    <div id="recent-drafts" class="mb-xl">
        <h5>{{ trans('entities.my_recent_drafts') }}</h5>
        @include('entities.list', ['entities' => $draftPages, 'style' => 'compact'])
    </div>
@endif

@if(count($favourites) > 0)
    <div id="top-favourites" class="mb-xl">
        <h5>
            <a href="{{ url('/favourites') }}" class="no-color">{{ trans('entities.my_most_viewed_favourites') }}</a>
        </h5>
        @include('entities.list', [
            'entities' => $favourites,
            'style' => 'compact',
        ])
    </div>
@endif

<div class="mb-xl">
    <h5>{{ trans('entities.' . (auth()->check() ? 'my_recently_viewed' : 'books_recent')) }}</h5>
    @include('entities.list', [
        'entities' => $recents,
        'style' => 'compact',
        'emptyText' => auth()->check() ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
        ])
</div>

<div class="mb-xl">
    <h5><a class="no-color" href="{{ url("/pages/recently-updated") }}">{{ trans('entities.recently_updated_pages') }}</a></h5>
    <div id="recently-updated-pages">
        @include('entities.list', [
        'entities' => $recentlyUpdatedPages,
        'style' => 'compact',
        'emptyText' => trans('entities.no_pages_recently_updated')
        ])
    </div>
</div>

<div id="recent-activity" class="mb-xl">
    <h5>{{ trans('entities.recent_activity') }}</h5>
    @include('common.activity-list', ['activity' => $activity])
</div>