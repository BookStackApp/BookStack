<div>
    {{-- @if($activity->user)
        <a href="{{ $activity->user->getProfileUrl() }}">{{ $activity->user->name }}</a>
    @else
        {{ trans('common.deleted_user') }}
    @endif --}}

    {{ Str::ucfirst($activity->getText()) }}

    @if($activity->entity && is_null($activity->entity->deleted_at))
        <a href="{{ $activity->entity->getUrl() }}">{{ $activity->entity->name }}</a>
    @endif

    @if($activity->entity && !is_null($activity->entity->deleted_at))
        "{{ $activity->entity->name }}"
    @endif

    <br>

    <span class="text-muted"><small>@icon('time'){{ $activity->created_at->diffForHumans() }}</small></span>
</div>