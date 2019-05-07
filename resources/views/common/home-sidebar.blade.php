@if(count($draftPages) > 0)
    <div id="recent-drafts" class="mb-xl">
        <h5>{{ trans('entities.my_recent_drafts') }}</h5>
        @include('partials.entity-list', ['entities' => $draftPages, 'style' => 'compact'])
    </div>
@endif

<div class="mb-xl">
    <h5>{{ trans('entities.' . ($signedIn ? 'my_recently_viewed' : 'books_recent')) }}</h5>
    @include('partials.entity-list', [
        'entities' => $recents,
        'style' => 'compact',
        'emptyText' => $signedIn ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
        ])
</div>

<div class="mb-xl">
    <h5><a class="no-color" href="{{ baseUrl("/pages/recently-updated") }}">{{ trans('entities.recently_updated_pages') }}</a></h5>
    <div id="recently-updated-pages">
        @include('partials.entity-list', [
        'entities' => $recentlyUpdatedPages,
        'style' => 'compact',
        'emptyText' => trans('entities.no_pages_recently_updated')
        ])
    </div>
</div>

<div id="recent-activity" class="mb-xl">
    <h5>{{ trans('entities.recent_activity') }}</h5>
    @include('partials.activity-list', ['activity' => $activity])
</div>