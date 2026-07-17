<div id="new" class="mb-xl">
    <h5>{{ trans('entities.shelves_new') }}</h5>
    @if(count($new) > 0)
        @include('entities.list', ['entities' => $new, 'style' => 'compact'])
    @else
        <p class="text-muted pb-l mb-none">{{ trans('entities.shelves_new_empty') }}</p>
    @endif
</div>