
@if(count($activity) > 0)
    <div class="activity-list">
        @foreach($activity as $activityItem)
            <div class="activity-list-item">
                @include('common.activity-item', ['activity' => $activityItem])
            </div>
        @endforeach
    </div>
@else
    <p class="text-muted empty-text mb-none pb-l">{{ trans('common.no_activity') }}</p>
@endif