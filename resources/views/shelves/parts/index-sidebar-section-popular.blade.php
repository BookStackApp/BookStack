<div id="popular" class="mb-xl">
    <h5>{{ trans('entities.shelves_popular') }}</h5>
    @if(count($popular) > 0)
        @include('entities.list', ['entities' => $popular, 'style' => 'compact'])
    @else
        <p class="text-muted pb-l mb-none">{{ trans('entities.shelves_popular_empty') }}</p>
    @endif
</div>