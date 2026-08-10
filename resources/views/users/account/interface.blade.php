@extends('users.account.layout')

@section('main')
    <section class="card content-wrap auto-height">
        <form action="{{ url('/my-account/interface') }}" method="post">
            {{ method_field('put') }}
            {{ csrf_field() }}

            <h1 class="list-heading">{{ trans('preferences.interface') }}</h1>
            <p class="text-small text-muted mb-none">{{ trans('preferences.interface_desc') }}</p>

            <div class="setting-list">
                @include('users.parts.language-option-row', ['value' => old('language') ?? user()->getLocale()->appLocale()])
            </div>

            <div class="form-group text-right">
                <button class="button">{{ trans('common.save') }}</button>
            </div>

        </form>
    </section>

    <section class="card content-wrap auto-height">
        <h2 class="list-heading">{{ trans('preferences.layouts') }}</h2>
        <p class="text-small text-muted">{{ trans('preferences.layouts_desc') }}</p>

        <div class="item-list mb-m">
            @foreach($namedLocations as $locationKey => $locationName)
                <div class="flex-container-row justify-space-between item-list-row items-center wrap px-xs">
                    <div class="py-xs px-s min-width-m">
                        <a href="#{{ $locationKey }}">{{ $locationName }}</a>
                    </div>
                    <div class="py-xs min-width-m text-m-right px-m">
                        <a class="button outline small" href="#{{ $locationKey }}">{{ trans('common.configure') }}</a>
                    </div>
                </div>
            @endforeach
        </div>

    </section>

    <section class="card content-wrap auto-height">
        <div class="flex-container-row gap-l items-center wrap">
            <div class="flex">
                <h2 class="list-heading">{{ trans('preferences.shortcuts_interface') }}</h2>
                <p class="text-small text-muted">{{ trans('preferences.shortcuts_overview_desc') }}</p>
            </div>
            <div class="text-m-right">
                <a class="button outline" href="{{ url('/my-account/shortcuts') }}">{{ trans('common.open') }}</a>
            </div>
        </div>
    </section>
@stop
