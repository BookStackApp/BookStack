@extends('users.account.layout')

@section('main')

    <div class="card content-wrap auto-height">
        <form action="{{ url("/my-account") }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('delete') }}


            <h1 class="list-heading">{{ trans('preferences.delete_my_account') }}</h1>

            <p>{{ trans('preferences.delete_my_account_desc') }}</p>

            @if(userCan('users-manage'))
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
            @endif

            <hr class="my-l">

            <div class="grid half">
                <p class="text-neg"><strong>{{ trans('preferences.delete_my_account_warning') }}</strong></p>
                <div class="text-right">
                    <a href="{{ url("/my-account/profile") }}"
                       class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('common.confirm') }}</button>
                </div>
            </div>

        </form>
    </div>

@stop
