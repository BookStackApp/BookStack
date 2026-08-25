{{--
$recents - array
--}}
<div class="mb-xl">
    <h5>{{ trans('entities.' . (auth()->check() ? 'my_recently_viewed' : 'books_recent')) }}</h5>
    @include('entities.list', [
        'entities' => $recents,
        'style' => 'compact',
        'emptyText' => auth()->check() ? trans('entities.no_pages_viewed') : trans('entities.books_empty')
        ])
</div>