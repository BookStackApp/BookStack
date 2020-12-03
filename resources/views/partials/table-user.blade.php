{{--
$user - User mode to display, Can be null.
$user_id - Id of user to show. Must be provided.
--}}
@if($user)
    <a href="{{ $user->getEditUrl() }}" class="table-user-item">
        <div><img class="avatar block" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}"></div>
        <div>{{ $user->name }}</div>
    </a>
@else
    [ID: {{ $user_id }}] {{ trans('common.deleted_user') }}
@endif