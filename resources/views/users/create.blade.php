@extends('base')


@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{ baseUrl('/settings/users') }}" class="text-button"><i class="zmdi zmdi-accounts"></i>{{ trans('settings.users') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container small" ng-non-bindable>
        <h1>{{ trans('settings.users_add_new') }}</h1>

        <form action="{{ baseUrl("/settings/users/create") }}" method="post">
            {!! csrf_field() !!}
            @include('users/forms/' . $authMethod)
            <div class="form-group">
                <a href="{{  baseUrl($currentUser->can('users-manage') ? "/settings/users" : "/") }}" class="button muted">{{ trans('common.cancel') }}</a>
                <button class="button pos" type="submit">{{ trans('common.save') }}</button>
            </div>
        </form>
    </div>

@stop
