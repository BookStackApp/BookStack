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
            @if (user()->hasAppAccess())
            <form component="global-search" action="{{ url('/search') }}" method="GET" class="search-box" role="search" tabindex="0">
                <button id="header-search-box-button"
                        refs="global-search@button"
                        type="submit"
                        aria-label="{{ trans('common.search') }}"
                        tabindex="-1">@icon('search')</button>
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
                @if (user()->hasAppAccess())
                    <a class="hide-over-l" href="{{ url('/search') }}">@icon('search'){{ trans('common.search') }}</a>
                    @if(userCanOnAny('view', \BookStack\Entities\Models\Bookshelf::class) || userCan('bookshelf-view-all') || userCan('bookshelf-view-own'))
                        <a href="{{ url('/shelves') }}" data-shortcut="shelves_view">@icon('bookshelf'){{ trans('entities.shelves') }}</a>
                    @endif
                    <a href="{{ url('/books') }}" data-shortcut="books_view">@icon('books'){{ trans('entities.books') }}</a>
                    @if(!user()->isGuest() && userCan('settings-manage'))
                        <a href="{{ url('/settings') }}" data-shortcut="settings_view">@icon('settings'){{ trans('settings.settings') }}</a>
                    @endif
                    @if(!user()->isGuest() && userCan('users-manage') && !userCan('settings-manage'))
                        <a href="{{ url('/settings/users') }}" data-shortcut="settings_view">@icon('users'){{ trans('settings.users') }}</a>
                    @endif
                @endif

                @if(user()->isGuest())
                    @if(setting('registration-enabled') && config('auth.method') === 'standard')
                        <a href="{{ url('/register') }}">@icon('new-user'){{ trans('auth.sign_up') }}</a>
                    @endif
                    <a href="{{ url('/login')  }}">@icon('login'){{ trans('auth.log_in') }}</a>
                @endif
            </div>
            @if(!user()->isGuest())
                @include('common.header-user-menu', ['user' => user()])
            @endif
        </nav>

    </div>
</header>
