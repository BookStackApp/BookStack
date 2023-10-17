@extends('layouts.simple')

@section('body')
    <div class="container small my-xl">

        <section class="card content-wrap auto-height items-center justify-space-between gap-m flex-container-row wrap">
            <div class="flex min-width-m">
                <h2 class="list-heading">{{ trans('preferences.shortcuts_interface') }}</h2>
                <p class="text-muted">{{ trans('preferences.shortcuts_overview_desc') }}</p>
            </div>
            <div class="text-right">
                <a href="{{ url('/my-account/shortcuts') }}" class="button outline">{{ trans('common.manage') }}</a>
            </div>
        </section>

        @if(!user()->isGuest() && userCan('receive-notifications'))
            <section class="card content-wrap auto-height items-center justify-space-between gap-m flex-container-row wrap">
                <div class="flex min-width-m">
                    <h2 class="list-heading">{{ trans('preferences.notifications') }}</h2>
                    <p class="text-muted">{{ trans('preferences.notifications_desc') }}</p>
                </div>
                <div class="text-right">
                    <a href="{{ url('/my-account/notifications') }}" class="button outline">{{ trans('common.manage') }}</a>
                </div>
            </section>
        @endif

        @if(!user()->isGuest())
            <section class="card content-wrap auto-height items-center justify-space-between gap-m flex-container-row wrap">
                <div class="flex min-width-m">
                    <h2 class="list-heading">{{ trans('settings.users_edit_profile') }}</h2>
                    <p class="text-muted">{{ trans('preferences.profile_overview_desc') }}</p>
                </div>
                <div class="text-right">
                    <a href="{{ user()->getEditUrl() }}" class="button outline">{{ trans('common.manage') }}</a>
                </div>
            </section>
        @endif

        @if(!user()->isGuest())
            <section class="card content-wrap auto-height items-center flex-container-row gap-m gap-x-l wrap justify-space-between">
                <div class="flex-min-width-m">
                    <h2 class="list-heading">{{ trans('settings.users_mfa') }}</h2>
                    <p class="text-muted">{{ trans('settings.users_mfa_desc') }}</p>
                    <p class="text-muted">
                        @if ($mfaMethods->count() > 0)
                            <span class="text-pos">@icon('check-circle')</span>
                        @else
                            <span class="text-neg">@icon('cancel')</span>
                        @endif
                        {{ trans_choice('settings.users_mfa_x_methods', $mfaMethods->count()) }}
                    </p>
                </div>
                <div class="text-right">
                    <a href="{{ url('/mfa/setup')  }}"
                       class="button outline">{{ trans('common.manage') }}</a>
                </div>
            </section>
        @endif

    </div>
@stop
