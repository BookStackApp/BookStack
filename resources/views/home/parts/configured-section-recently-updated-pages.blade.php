{{--
$recentlyUpdatedPages - array
--}}
<div class="mb-xl">
    <h5>{{ trans('entities.recently_updated_pages') }}</h5>
    <div id="recently-updated-pages">
        @include('entities.list', [
        'entities' => $recentlyUpdatedPages,
        'style' => 'compact',
        'emptyText' => trans('entities.no_pages_recently_updated')
        ])
    </div>
    <a href="{{ url('/pages/recently-updated')  }}" class="text-muted block py-xs">{{ trans('common.view_all') }}</a>
</div>