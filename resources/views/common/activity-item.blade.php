
{{--Requires an Activity item with the name $activity passed in--}}

<div>
    @if($activity->user)
    <img class="avatar" src="{{ $activity->user->getAvatar(30) }}" alt="{{ $activity->user->name }}">
    @endif
</div>

<div>
    @if($activity->user)
        <a href="{{ $activity->user->getProfileUrl() }}">{{ $activity->user->name }}</a>
    @else
        {{ trans('common.deleted_user') }}
    @endif

    {{ $activity->getText() }}

    @if($activity->loggable && is_null($activity->loggable->deleted_at))
        <a href="{{ $activity->loggable->getUrl() }}">{{ $activity->loggable->name }}</a>
    @endif

    @if($activity->loggable && !is_null($activity->loggable->deleted_at))
        "{{ $activity->loggable->name }}"
    @endif

    <br>

    <span class="text-muted"><small>@icon('time'){{ $activity->created_at->diffForHumans() }}</small></span>
</div>
