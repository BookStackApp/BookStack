<div class="dropdown-container" dropdown>
    <span class="user-name" dropdown-toggle>
        <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
        <span class="name" ng-non-bindable>{{ $currentUser->getShortName(9) }}</span> <i class="zmdi zmdi-caret-down"></i>
    </span>
    <ul>
        <li>
            <a href="{{ baseUrl("/user/{$currentUser->id}") }}" class="text-primary"><i class="zmdi zmdi-account zmdi-hc-fw zmdi-hc-lg"></i>{{ trans('common.view_profile') }}</a>
        </li>
        <li>
            <a href="{{ baseUrl("/settings/users/{$currentUser->id}") }}" class="text-primary"><i class="zmdi zmdi-edit zmdi-hc-fw zmdi-hc-lg"></i>{{ trans('common.edit_profile') }}</a>
        </li>
        <li>
            <a href="{{ baseUrl('/logout') }}" class="text-neg"><i class="zmdi zmdi-run zmdi-hc-fw zmdi-hc-lg"></i>{{ trans('auth.logout') }}</a>
        </li>
    </ul>
</div>