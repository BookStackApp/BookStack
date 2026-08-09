{{--
$draftPages - array
--}}
@if(count($draftPages) > 0)
    <div id="recent-drafts" class="card mb-xl">
        <h3 class="card-title">{{ trans('entities.my_recent_drafts') }}</h3>
        <div class="px-m">
            @include('entities.list', ['entities' => $draftPages, 'style' => 'compact'])
        </div>
    </div>
@endif
