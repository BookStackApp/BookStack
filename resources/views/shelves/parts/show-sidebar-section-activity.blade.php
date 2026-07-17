@if(count($activity) > 0)
    <div id="recent-activity" class="mb-xl">
        <h5>{{ trans('entities.recent_activity') }}</h5>
        @include('common.activity-list', ['activity' => $activity])
    </div>
@endif