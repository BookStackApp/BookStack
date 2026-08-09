{{--
$recentlyUpdatedPages - array
--}}
<div id="recent-pages" class="card mb-xl">
    <h3 class="card-title">{{ trans('entities.recently_updated_pages') }}</h3>
    <div id="recently-updated-pages" class="px-m">
        @include('entities.list', [
        'entities' => $recentlyUpdatedPages,
        'style' => 'compact',
        'emptyText' => trans('entities.no_pages_recently_updated'),
        ])
    </div>
    @if(count($recentlyUpdatedPages) > 0)
        <a href="{{ url("/pages/recently-updated") }}" class="card-footer-link">{{ trans('common.view_all') }}</a>
    @endif
</div>