{{--
$draftPages - array
--}}
@if(count($draftPages) > 0)
    <div id="recent-drafts" class="mb-xl">
        <h5>{{ trans('entities.my_recent_drafts') }}</h5>
        @include('entities.list', ['entities' => $draftPages, 'style' => 'compact'])
    </div>
@endif