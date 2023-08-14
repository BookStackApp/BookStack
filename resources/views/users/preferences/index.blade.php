@extends('layouts.simple')

@section('body')
    <div class="container small my-xl">

        <section class="card content-wrap auto-height items-center justify-space-between gap-m flex-container-row">
            <div>
                <h2 class="list-heading">{{ trans('preferences.shortcuts_interface') }}</h2>
                <p class="text-muted">{{ trans('preferences.shortcuts_overview_desc') }}</p>
            </div>
            <div class="text-right">
                <a href="{{ url('/preferences/shortcuts') }}" class="button outline">{{ trans('common.manage') }}</a>
            </div>
        </section>

        @if(userCan('receive-notifications'))
            <section class="card content-wrap auto-height items-center justify-space-between gap-m flex-container-row">
                <div>
                    <h2 class="list-heading">{{ trans('preferences.notifications') }}</h2>
                    <p class="text-muted">{{ trans('preferences.notifications_desc') }}</p>
                </div>
                <div class="text-right">
                    <a href="{{ url('/preferences/notifications') }}" class="button outline">{{ trans('common.manage') }}</a>
                </div>
            </section>
        @endif

        @if(signedInUser())
            <section class="card content-wrap auto-height items-center justify-space-between gap-m flex-container-row">
                <div>
                    <h2 class="list-heading">{{ trans('settings.users_edit_profile') }}</h2>
                    <p class="text-muted">{{ trans('preferences.profile_overview_desc') }}</p>
                </div>
                <div class="text-right">
                    <a href="{{ user()->getEditUrl() }}" class="button outline">{{ trans('common.manage') }}</a>
                </div>
            </section>
        @endif

    </div>
@stop
