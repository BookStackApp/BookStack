
{{--Requires an Activity item with the name $activity passed in--}}

<div>
    @if($activity->user)
    <img class="avatar" src="{{ $activity->user->getAvatar(30) }}" alt="{{ $activity->user->name }}">
    @endif
</div>

<div v-pre>
    @if($activity->user)
        <a href="{{ $activity->user->getProfileUrl() }}">{{ $activity->user->name }}</a>
    @else
        {{ trans('common.deleted_user') }}
    @endif

    {{ $activity->getText() }}

    @if($activity->entity)
        <a href="{{ $activity->entity->getUrl() }}">{{ $activity->entity->name }}</a>
    @endif

    @if($activity->extra) "{{ $activity->extra }}" @endif

    <br>

    <span class="text-muted"><small>@icon('time'){{ $activity->created_at->diffForHumans() }}</small></span>
</div>
