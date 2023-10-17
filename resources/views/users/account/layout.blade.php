@extends('layouts.simple')

@section('body')
    <div class="container medium">

        <div class="grid gap-xxl right-focus my-xl">

            <div>
                <div class="sticky-top-m">
                    <h5>{{ trans('preferences.my_account') }}</h5>
                    <nav class="active-link-list in-sidebar">
                        <a href="{{ url('/my-account/shortcuts') }}" class="{{ 'shortcuts' === 'shortcuts' ? 'active' : '' }}">@icon('shortcuts') {{ trans('preferences.shortcuts_interface') }}</a>
                        <a href="{{ url('/my-account/notifications') }}" class="{{ '' === 'notifications' ? 'active' : '' }}">@icon('notifications') {{ trans('preferences.notifications') }}</a>
                        <a href="{{ url('/my-account/auth') }}" class="{{ '' === 'auth' ? 'active' : '' }}">@icon('lock') {{ 'Access & Security' }}</a>
                    </nav>
                </div>
            </div>

            <div>
                @yield('main')
            </div>

        </div>

    </div>
@stop