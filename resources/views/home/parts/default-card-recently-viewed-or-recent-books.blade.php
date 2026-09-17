{{--
$recents - array
--}}

<div id="{{ auth()->check() ? 'recently-viewed' : 'recent-books' }}" class="card mb-xl">
    <h3 class="card-title">{{ trans('entities.' . (auth()->check() ? 'my_recently_viewed' : 'books_recent')) }}</h3>
    <div class="px-m">
        @include('entities.list', [
        'entities' => $recents,
        'style' => 'compact',
        'emptyText' => auth()->check() ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
        ])
    </div>
</div>