@extends('layouts.simple')

@section('body')

    <div class="container small">
        <div class="py-m">
            @include('settings.parts.navbar', ['selected' => 'roles'])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('settings.role_edit') }}</h1>

            <form action="{{ url("/settings/roles/{$role->id}") }}" method="POST">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                @include('settings.roles.parts.form', ['role' => $role])

                <div class="form-group text-right">
                    <a href="{{ url("/settings/roles") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <a href="{{ url("/settings/roles/new?copy_from={$role->id}") }}" class="button outline">{{ trans('common.copy') }}</a>
                    <a href="{{ url("/settings/roles/delete/{$role->id}") }}" class="button outline">{{ trans('settings.role_delete') }}</a>
                    <button type="submit" class="button">{{ trans('settings.role_save') }}</button>
                </div>
            </form>

        </div>


        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.role_users') }}</h2>
            @if(count($role->users ?? []) > 0)
                <div class="grid third">
                    @foreach($role->users as $user)
                        <div class="user-list-item">
                            <div>
                                <img class="avatar small" src="{{ $user->getAvatar(40) }}" alt="{{ $user->name }}">
                            </div>
                            <div>
                                @if(userCan('users-manage') || user()->id == $user->id)
                                    <a href="{{ url("/settings/users/{$user->id}") }}">
                                        @endif
                                        {{ $user->name }}
                                        @if(userCan('users-manage') || user()->id == $user->id)
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">
                    {{ trans('settings.role_users_none') }}
                </p>
            @endif
        </div>
    </div>

@stop
