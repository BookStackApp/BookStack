{{--
$activity - array
--}}
<div id="recent-activity" class="card mb-xl">
    <h3 class="card-title">{{ trans('entities.recent_activity') }}</h3>
    <div class="px-m">
        @include('common.activity-list', ['activity' => $activity])
    </div>
</div>