<a href="#" class="flex-container-row items-center dropdown-search-item" data-id="">
    <span>{{ trans('settings.users_none_selected') }}</span>
</a>
@foreach($users as $user)
    <a href="#" class="flex-container-row items-center dropdown-search-item" data-id="{{ $user->id }}">
        <img class="avatar mr-m" src="{{ $user->getAvatar(30) }}" alt="{{ $user->name }}">
        <span>{{ $user->name }}</span>
    </a>
@endforeach