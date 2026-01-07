{{--
$user - User to display.
--}}
<a href="{{ $user->getEditUrl() }}" class="flex-container-row inline gap-s items-center">
    <div class="flex-none"><img width="40" height="40" class="avatar block" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}"></div>
    <div class="flex">{{ $user->name }}</div>
</a>