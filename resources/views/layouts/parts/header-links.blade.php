@include('layouts.parts.header-links-start')

@if (user()->hasAppAccess())
    <a class="hide-over-l" href="{{ url('/search') }}">@icon('search'){{ trans('common.search') }}</a>
    @if(userCanOnAny('view', \BookStack\Entities\Models\Bookshelf::class) || userCan('bookshelf-view-all') || userCan('bookshelf-view-own'))
        <a href="{{ url('/shelves') }}"
           data-shortcut="shelves_view">@icon('bookshelf'){{ trans('entities.shelves') }}</a>
    @endif
    <a href="{{ url('/books') }}" data-shortcut="books_view">@icon('books'){{ trans('entities.books') }}</a>
    @if(!user()->isGuest() && userCan('settings-manage'))
        <a href="{{ url('/settings') }}"
           data-shortcut="settings_view">@icon('settings'){{ trans('settings.settings') }}</a>
    @endif
    @if(!user()->isGuest() && userCan('users-manage') && !userCan('settings-manage'))
        <a href="{{ url('/settings/users') }}"
           data-shortcut="settings_view">@icon('users'){{ trans('settings.users') }}</a>
    @endif
@endif

@if(user()->isGuest())
    @if(setting('registration-enabled') && config('auth.method') === 'standard')
        <a href="{{ url('/register') }}">@icon('new-user'){{ trans('auth.sign_up') }}</a>
    @endif
    <a href="{{ url('/login')  }}">@icon('login'){{ trans('auth.log_in') }}</a>
@endif