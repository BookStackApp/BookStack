@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{ baseUrl("/settings/users") }}" class="text-button"><i class="zmdi zmdi-accounts"></i>Users</a>
                        <span class="sep">&raquo;</span>
                        <a href="{{ baseUrl("/settings/users/{$user->id}") }}" class="text-button"><i class="zmdi zmdi-account"></i>{{ $user->name }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container small" ng-non-bindable>
        <h1>{{ trans('settings.users_delete') }}</h1>
        <p>{{ trans('settings.users_delete_warning', ['userName' => $user->name]) }}</p>
        <p class="text-neg">{{ trans('settings.users_delete_confirm') }}</p>

        <form action="{{ baseUrl("/settings/users/{$user->id}") }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="{{ baseUrl("/settings/users/{$user->id}") }}" class="button muted">{{ trans('common.cancel') }}</a>
            <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
        </form>
    </div>

@stop
