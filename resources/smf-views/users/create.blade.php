@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'users'])

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('settings.users_add_new') }}</h1>

            <form action="{{ url("/settings/users/create") }}" method="post">
                {!! csrf_field() !!}

                <div class="setting-list">
                    @include('users.parts.form')
                    @include('users.parts.language-option-row', ['value' => old('setting.language') ?? config('app.default_locale')])
                </div>

                <div class="form-group text-right">
                    <a href="{{  url(userCan('users-manage') ? "/settings/users" : "/") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button" type="submit">{{ trans('common.save') }}</button>
                </div>

            </form>

        </main>
    </div>

@stop
