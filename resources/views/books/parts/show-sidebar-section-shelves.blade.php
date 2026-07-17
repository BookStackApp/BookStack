@if(count($bookParentShelves) > 0)
    <div class="actions mb-xl">
        <h5>{{ trans('entities.shelves') }}</h5>
        @include('entities.list', ['entities' => $bookParentShelves, 'style' => 'compact'])
    </div>
@endif