@if(count($shelves) > 0)
    <div class="actions mb-xl">
        <h5>{{ trans('entities.shelves') }}</h5>
        @include('entities.list', ['entities' => $shelves, 'style' => 'compact'])
    </div>
@endif