@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'users'])
@stop

@section('body')

    <div class="container small" ng-non-bindable>
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('delete') {{ trans('settings.users_delete') }}</h3>
            <div class="body">
                <p>{{ trans('settings.users_delete_warning', ['userName' => $user->name]) }}</p>
                <p class="text-neg">{{ trans('settings.users_delete_confirm') }}</p>

                <form action="{{ baseUrl("/settings/users/{$user->id}") }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="DELETE">
                    <a href="{{ baseUrl("/settings/users/{$user->id}") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button neg">{{ trans('common.confirm') }}</button>
                </form>
            </div>
        </div>
    </div>

@stop
