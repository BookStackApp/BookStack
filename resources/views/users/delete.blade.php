@extends('layouts.simple')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'users'])
        </div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.users_delete') }}</h1>

            <p>{{ trans('settings.users_delete_warning', ['userName' => $user->name]) }}</p>

            @if(userCan('users-manage'))
                <hr class="my-l">

                <div class="grid half gap-xl v-center">
                    <div>
                        <label class="setting-list-label">{{ trans('settings.users_migrate_ownership') }}</label>
                        <p class="small">{{ trans('settings.users_migrate_ownership_desc') }}</p>
                    </div>
                    <div>
                        @include('form.user-select', ['name' => 'new_owner_id', 'user' => null, 'compact' => false])
                    </div>
                </div>
            @endif

            <hr class="my-l">

            <div class="grid half">
                <p class="text-neg"><strong>{{ trans('settings.users_delete_confirm') }}</strong></p>
                <div>
                    <form action="{{ url("/settings/users/{$user->id}") }}" method="POST" class="text-right">
                        {!! csrf_field() !!}

                        <input type="hidden" name="_method" value="DELETE">
                        <a href="{{ url("/settings/users/{$user->id}") }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button">{{ trans('common.confirm') }}</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@stop
