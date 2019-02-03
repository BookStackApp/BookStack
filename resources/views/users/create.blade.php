@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'users'])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('settings.users_add_new') }}</h1>

            <form action="{{ baseUrl("/settings/users/create") }}" method="post">
                {!! csrf_field() !!}

                <div class="setting-list">
                    @include('users.form')
                </div>

                <div class="form-group text-right">
                    <a href="{{  baseUrl($currentUser->can('users-manage') ? "/settings/users" : "/") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button primary" type="submit">{{ trans('common.save') }}</button>
                </div>

            </form>

        </div>
    </div>

@stop
