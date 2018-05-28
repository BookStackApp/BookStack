<div class="dropdown-container" dropdown>
    <span class="user-name" dropdown-toggle>
        <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
        <span class="name">{{ $currentUser->getShortName(9) }}</span> @icon('caret-down')
    </span>
    <ul>
        <li>
            <a href="{{ baseUrl("/user/{$currentUser->id}") }}" class="text-primary">@icon('user') {{ trans('common.view_profile') }}</a>
        </li>
        <li>
            <a href="{{ baseUrl("/settings/users/{$currentUser->id}") }}" class="text-primary">@icon('edit') {{ trans('common.edit_profile') }}</a>
        </li>
        <li>
            <a href="{{ baseUrl('/logout') }}" class="text-neg">@icon('logout') {{ trans('auth.logout') }}</a>
        </li>
    </ul>
</div>