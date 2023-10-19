@extends('layouts.simple')

@section('body')
    <div class="container medium">

        <div class="grid gap-xxl right-focus my-xl">

            <div>
                <div class="sticky-top-m">
                    <h5>{{ trans('preferences.my_account') }}</h5>
                    <nav class="active-link-list in-sidebar">
                        <a href="{{ url('/my-account/profile') }}" class="{{ $category === 'profile' ? 'active' : '' }}">@icon('user') {{ trans('preferences.profile') }}</a>
                        <a href="{{ url('/my-account/auth') }}" class="{{ $category === 'auth' ? 'active' : '' }}">@icon('security') {{ trans('preferences.auth') }}</a>
                        <a href="{{ url('/my-account/shortcuts') }}" class="{{ $category === 'shortcuts' ? 'active' : '' }}">@icon('shortcuts') {{ trans('preferences.shortcuts_interface') }}</a>
                        @if(userCan('receive-notifications'))
                            <a href="{{ url('/my-account/notifications') }}" class="{{ $category === 'notifications' ? 'active' : '' }}">@icon('notifications') {{ trans('preferences.notifications') }}</a>
                        @endif
                    </nav>
                </div>
            </div>

            <div>
                @yield('main')
            </div>

        </div>

    </div>
@stop