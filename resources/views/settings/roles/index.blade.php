@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'roles'])
        </div>

        <div class="card content-wrap auto-height">

            <div class="grid half v-center">
                <h1 class="list-heading">{{ trans('settings.role_user_roles') }}</h1>

                <div class="text-right">
                    <a href="{{ baseUrl("/settings/roles/new") }}" class="button outline">{{ trans('settings.role_create') }}</a>
                </div>
            </div>

            <table class="table">
                <tr>
                    <th>{{ trans('settings.role_name') }}</th>
                    <th></th>
                    <th class="text-center">{{ trans('settings.users') }}</th>
                </tr>
                @foreach($roles as $role)
                    <tr>
                        <td><a href="{{ baseUrl("/settings/roles/{$role->id}") }}">{{ $role->display_name }}</a></td>
                        <td>{{ $role->description }}</td>
                        <td class="text-center">{{ $role->users->count() }}</td>
                    </tr>
                @endforeach
            </table>


        </div>
    </div>

@stop
