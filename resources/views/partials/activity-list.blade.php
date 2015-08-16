
{{--Requires an entity to be passed with the name $entity--}}

@if(count($entity->recentActivity()) > 0)
    <div class="activity-list">
        @foreach($entity->recentActivity() as $activity)
            <div class="activity-list-item">
                @include('partials/activity-item')
            </div>
        @endforeach
    </div>
@endif