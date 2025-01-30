@extends('layouts.simple')

@section('body')
    <div class="container medium">

        @include('settings.parts.navbar', ['selected' => 'settings'])

        <div class="grid gap-xxl right-focus">

            <div>
                <h5>{{ trans('settings.categories') }}</h5>
                <nav class="active-link-list in-sidebar">
                    <a href="{{ url('/settings/features') }}" class="{{ $category === 'features' ? 'active' : '' }}">@icon('star') {{ trans('settings.app_features_security') }}</a>
                    <a href="{{ url('/settings/customization') }}" class="{{ $category === 'customization' ? 'active' : '' }}">@icon('palette') {{ trans('settings.app_customization') }}</a>
                    <a href="{{ url('/settings/registration') }}" class="{{ $category === 'registration' ? 'active' : '' }}">@icon('security') {{ trans('settings.reg_settings') }}</a>
                    <a href="{{ url('/settings/sorting') }}" class="{{ $category === 'sorting' ? 'active' : '' }}">@icon('sort') {{ trans('settings.sorting') }}</a>
                </nav>

                <h5 class="mt-xl">{{ trans('settings.system_version') }}</h5>
                <div class="py-xs">
                    <a target="_blank" rel="noopener noreferrer" href="https://github.com/BookStackApp/BookStack/releases">
                        BookStack @if(!str_starts_with($version, 'v')) version @endif {{ $version }}
                    </a>
                    <br>
                    <a target="_blank" href="{{ url('/licenses') }}" class="text-muted">{{ trans('settings.license_details') }}</a>
                </div>
            </div>

            <div>
                <div class="card content-wrap auto-height">
                    @yield('card')
                </div>
                @yield('after-card')
            </div>

        </div>

    </div>

    @yield('after-content')
@stop
