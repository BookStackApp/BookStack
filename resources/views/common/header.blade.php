<header id="header" component="header-mobile-toggle" class="primary-background">
    <div class="grid mx-l">

        <div>
            <a href="{{ url('/') }}" data-shortcut="home_view" class="logo">
                @if(setting('app-logo', '') !== 'none')
                    <img class="logo-image" src="{{ setting('app-logo', '') === '' ? url('/logo.png') : url(setting('app-logo', '')) }}" alt="Logo">
                @endif
                @if (setting('app-name-header'))
                    <span class="logo-text">{{ setting('app-name') }}</span>
                @endif
            </a>
            <button type="button"
                    refs="header-mobile-toggle@toggle"
                    title="{{ trans('common.header_menu_expand') }}"
                    aria-expanded="false"
                    class="mobile-menu-toggle hide-over-l">@icon('more')</button>
        </div>

        <div class="flex-container-column items-center justify-center hide-under-l">
            @if (hasAppAccess())
            <form component="global-search" action="{{ url('/search') }}" method="GET" class="search-box" role="search">
                <button id="header-search-box-button" type="submit" aria-label="{{ trans('common.search') }}" tabindex="-1">@icon('search') </button>
                <input id="header-search-box-input"
                       refs="global-search@input"
                       type="text"
                       name="term"
                       data-shortcut="global_search"
                       autocomplete="off"
                       aria-label="{{ trans('common.search') }}" placeholder="{{ trans('common.search') }}"
                       value="{{ $searchTerm ?? '' }}">
                <div refs="global-search@suggestions" class="global-search-suggestions card">
                    <div refs="global-search@loading" class="text-center px-m global-search-loading">@include('common.loading-icon')</div>
                    <div refs="global-search@suggestion-results" class="px-m"></div>
                    <button class="text-button card-footer-link" type="submit">{{ trans('common.view_all') }}</button>
                </div>
            </form>
            @endif
        </div>

        <nav refs="header-mobile-toggle@menu" class="header-links">
            <div class="links text-center">
                @if (hasAppAccess())
                    <a class="hide-over-l" href="{{ url('/search') }}">@icon('search'){{ trans('common.search') }}</a>
                    @if(userCanOnAny('view', \BookStack\Entities\Models\Bookshelf::class) || userCan('bookshelf-view-all') || userCan('bookshelf-view-own'))
                        <a href="{{ url('/shelves') }}" data-shortcut="shelves_view">@icon('bookshelf'){{ trans('entities.shelves') }}</a>
                    @endif
                    <a href="{{ url('/books') }}" data-shortcut="books_view">@icon('books'){{ trans('entities.books') }}</a>
                    @if(signedInUser() && userCan('settings-manage'))
                        <a href="{{ url('/settings') }}" data-shortcut="settings_view">@icon('settings'){{ trans('settings.settings') }}</a>
                    @endif
                    @if(signedInUser() && userCan('users-manage') && !userCan('settings-manage'))
                        <a href="{{ url('/settings/users') }}" data-shortcut="settings_view">@icon('users'){{ trans('settings.users') }}</a>
                    @endif
                @endif

                @if(!signedInUser())
                    @if(setting('registration-enabled') && config('auth.method') === 'standard')
                        <a href="{{ url('/register') }}">@icon('new-user'){{ trans('auth.sign_up') }}</a>
                    @endif
                    <a href="{{ url('/login')  }}">@icon('login'){{ trans('auth.log_in') }}</a>
                @endif
            </div>
            @if(signedInUser())
                <?php $currentUser = user(); ?>
                <div class="dropdown-container" component="dropdown" option:dropdown:bubble-escapes="true">
                        <span class="user-name py-s hide-under-l" refs="dropdown@toggle"
                              aria-haspopup="true" aria-expanded="false" aria-label="{{ trans('common.profile_menu') }}" tabindex="0">
                            <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
                            <span class="name">{{ $currentUser->getShortName(9) }}</span> @icon('caret-down')
                        </span>
                    <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                        <li>
                            <a href="{{ url('/favourites') }}" data-shortcut="favourites_view" class="icon-item">
                                @icon('star')
                                <div>{{ trans('entities.my_favourites') }}</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $currentUser->getProfileUrl() }}" data-shortcut="profile_view" class="icon-item">
                                @icon('user')
                                <div>{{ trans('common.view_profile') }}</div>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $currentUser->getEditUrl() }}" class="icon-item">
                                @icon('edit')
                                <div>{{ trans('common.edit_profile') }}</div>
                            </a>
                        </li>
                        <li>
                            <form action="{{ url(config('auth.method') === 'saml2' ? '/saml2/logout' : '/logout') }}"
                                  method="post">
                                {{ csrf_field() }}
                                <button class="icon-item" data-shortcut="logout">
                                    @icon('logout')
                                    <div>{{ trans('auth.logout') }}</div>
                                </button>
                            </form>
                        </li>
                        <li><hr></li>
                        <li>
                            <a href="{{ url('/preferences/shortcuts') }}" class="icon-item">
                                @icon('shortcuts')
                                <div>{{ trans('preferences.shortcuts') }}</div>
                            </a>
                        </li>
                        <li>
                            @include('common.dark-mode-toggle', ['classes' => 'icon-item'])
                        </li>
                    </ul>
                </div>
            @endif
        </nav>

    </div>
</header>
