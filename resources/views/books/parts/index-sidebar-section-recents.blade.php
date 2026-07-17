@if($recents)
    <div id="recents" class="mb-xl">
        <h5>{{ trans('entities.recently_viewed') }}</h5>
        @include('entities.list', ['entities' => $recents, 'style' => 'compact'])
    </div>
@endif