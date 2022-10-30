{{--
$user - User mode to display, Can be null.
$user_id - Id of user to show. Must be provided.
--}}
@if($user)
    <a href="{{ $user->getEditUrl() }}" class="flex-container-row inline gap-s items-center">
        <div class="flex-none"><img width="40" height="40" class="avatar block" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}"></div>
        <div class="flex">{{ $user->name }}</div>
    </a>
@else
    [ID: {{ $user_id }}] {{ trans('common.deleted_user') }}
@endif