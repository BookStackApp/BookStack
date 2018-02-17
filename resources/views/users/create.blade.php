@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'users'])
@stop

@section('body')

    <div class="container small" ng-non-bindable>
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('users-add') {{ trans('settings.users_add_new') }}</h3>
            <div class="body">
                <form action="{{ baseUrl("/settings/users/create") }}" method="post">
                    {!! csrf_field() !!}
                    @include('users/forms/' . $authMethod)
                    <div class="form-group text-right">
                        <a href="{{  baseUrl($currentUser->can('users-manage') ? "/settings/users" : "/") }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button class="button pos" type="submit">{{ trans('common.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop
