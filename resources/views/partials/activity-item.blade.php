
{{--Requires an Activity item with the name $activity passed in--}}

@if($activity->user)
    <div class="left">
        <img class="avatar" src="{{ $activity->user->getAvatar(30) }}" alt="{{$activity->user->name}}">
    </div>
@endif

<div class="right">
    @if($activity->user)
        {{$activity->user->name}}
    @else
        A deleted user
    @endif

    {{ $activity->getText() }}

    @if($activity->entity())
        <a href="{{ $activity->entity()->getUrl() }}">{{ $activity->entity()->name }}</a>
    @endif

    @if($activity->extra) "{{$activity->extra}}" @endif

    <br>

    <span class="text-muted"><small><i class="zmdi zmdi-time"></i>{{ $activity->created_at->diffForHumans() }}</small></span>
</div>
