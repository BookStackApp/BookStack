
{{--Requires an entity to be passed with the name $entity--}}

@if(count($activity) > 0)
    <div class="activity-list">
        @foreach($activity as $activityItem)
            <div class="activity-list-item">
                @include('partials/activity-item', ['activity' => $activityItem])
            </div>
        @endforeach
    </div>
@endif