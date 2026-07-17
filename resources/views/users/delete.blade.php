@extends('layouts.simple')

@section('body')
    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'users'])

        <form action="{{ url("/settings/users/{$user->id}") }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('delete') }}

            <div class="card content-wrap auto-height">
                <h1 class="list-heading">{{ trans('settings.users_delete') }}</h1>

                <p>{{ trans('settings.users_delete_warning', ['userName' => $user->name]) }}</p>

                <hr class="my-l">

                <div class="grid half gap-xl v-center">
                    <div>
                        <label class="setting-list-label">{{ trans('settings.users_migrate_ownership') }}</label>
                        <p class="small">{{ trans('settings.users_migrate_ownership_desc') }}</p>
                    </div>
                    <div>
                        @include('form.user-select', ['name' => 'new_owner_id', 'user' => null])
                    </div>
                </div>

                <hr class="my-l">

                <div class="grid half">
                    <p class="text-neg"><strong>{{ trans('settings.users_delete_confirm') }}</strong></p>
                    <div class="text-right">
                        <a href="{{ url("/settings/users/{$user->id}") }}" class="button outline">{{ trans('common.cancel') }}</a>
                        <button type="submit" class="button">{{ trans('common.confirm') }}</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
@stop
