@extends('simple-layout')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'users'])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.users_delete') }}</h1>

            <p>{{ trans('settings.users_delete_warning', ['userName' => $user->name]) }}</p>

            <div class="grid half">
                <p class="text-neg"><strong>{{ trans('settings.users_delete_confirm') }}</strong></p>
                <div>
                    <form action="{{ url("/settings/users/{$user->id}") }}" method="POST" class="text-right">
                        {!! csrf_field() !!}

                        <input type="hidden" name="_method" value="DELETE">
                        <a href="{{ url("/settings/users/{$user->id}") }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button primary">{{ trans('common.confirm') }}</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@stop
