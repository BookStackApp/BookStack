<div class="dropdown-container" component="dropdown" option:dropdown:bubble-escapes="true">
    <button class="user-name py-s hide-under-l" refs="dropdown@toggle"
          aria-haspopup="menu"
          aria-expanded="false"
          aria-label="{{ trans('common.profile_menu') }}">
        <img class="avatar" src="{{$user->getAvatar(30)}}" alt="{{ $user->name }}">
        <span class="name">{{ $user->getShortName(9) }}</span> @icon('caret-down')
    </button>
    <ul refs="dropdown@menu" class="dropdown-menu" role="menu" aria-label="{{ trans('common.profile_menu') }}">
        <li>
            <a href="{{ url('/favourites') }}"
               role="menuitem"
               data-shortcut="favourites_view"
               class="icon-item">
                @icon('star')
                <div>{{ trans('entities.my_favourites') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ $user->getProfileUrl() }}"
               role="menuitem"
               data-shortcut="profile_view"
               class="icon-item">
                @icon('user')
                <div>{{ trans('common.view_profile') }}</div>
            </a>
        </li>
        <li>
            <a href="{{ url('/my-account') }}"
               role="menuitem"
               class="icon-item">
                @icon('user-preferences')
                <div>{{ trans('preferences.my_account') }}</div>
            </a>
        </li>
        <li role="presentation"><hr></li>
        <li>
            @include('common.dark-mode-toggle', ['classes' => 'icon-item', 'buttonRole' => 'menuitem'])
        </li>
        <li role="presentation"><hr></li>
        <li>
            @php
                $logoutPath = match (config('auth.method')) {
                    'saml2' => '/saml2/logout',
                    'oidc' => '/oidc/logout',
                    default => '/logout',
                }
            @endphp
            <form action="{{ url($logoutPath) }}" method="post">
                {{ csrf_field() }}
                <button class="icon-item" role="menuitem" data-shortcut="logout">
                    @icon('logout')
                    <div>{{ trans('auth.logout') }}</div>
                </button>
            </form>
        </li>
    </ul>
</div>