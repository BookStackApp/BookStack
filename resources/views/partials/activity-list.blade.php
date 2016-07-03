
@if(count($activity) > 0)
    <div class="activity-list">
        @foreach($activity as $activityItem)
            <div class="activity-list-item">
                @include('partials/activity-item', ['activity' => $activityItem])
            </div>
        @endforeach
    </div>
@else
    <p class="text-muted">No activity to show</p>
@endif