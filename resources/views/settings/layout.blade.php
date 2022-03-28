@extends('layouts.simple')

@section('body')
    <div class="container medium">

        @include('settings.parts.navbar', ['selected' => 'settings'])

        <div class="grid gap-xxl right-focus">

            <div>
                <h5>{{ trans('settings.categories') }}</h5>
                <nav class="active-link-list in-sidebar">
                    <a href="{{ url('/settings/features') }}" class="{{ $category === 'features' ? 'active' : '' }}">@icon('star') Features & Security</a>
                    <a href="{{ url('/settings/customization') }}" class="{{ $category === 'customization' ? 'active' : '' }}">@icon('palette') Customization</a>
                    <a href="{{ url('/settings/registration') }}" class="{{ $category === 'registration' ? 'active' : '' }}">@icon('lock') Registration</a>
                </nav>

                <h5 class="mt-xl">{{ trans('settings.system_version') }}</h5>
                <div class="py-xs">
                    <a target="_blank" rel="noopener noreferrer" href="https://github.com/BookStackApp/BookStack/releases">
                        BookStack @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
                    </a>
                </div>
            </div>

            <div>
                <div class="card content-wrap auto-height">
                    @yield('card')
                </div>
            </div>

        </div>

    </div>

    @yield('after-content')
@stop
