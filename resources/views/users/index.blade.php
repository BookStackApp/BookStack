@extends('base')


@section('content')

    @include('settings/navbar', ['selected' => 'users'])


    <div class="container small" ng-non-bindable>
        <div class="row action-header">
            <div class="col-sm-8">
                <h1>{{ trans('settings.users') }}</h1>
            </div>
            <div class="col-sm-4">
                <p></p>
                @if(userCan('users-manage'))
                    <a href="{{ baseUrl("/settings/users/create") }}" class="pos button float right"><i class="zmdi zmdi-account-add"></i>{{ trans('settings.users_add_new') }}</a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8">
                <div class="compact">
                    {{ $users->links() }}
                </div>
            </div>
            <div class="col-sm-4">
                <form method="get" class="float right" action="{{ baseUrl("/settings/users") }}">
                    @foreach(collect($listDetails)->except('search') as $name => $val)
                        <input type="hidden" name="{{ $name }}" value="{{ $val }}">
                    @endforeach
                    <input type="text" name="search" placeholder="{{ trans('settings.users_search') }}" @if($listDetails['search']) value="{{$listDetails['search']}}" @endif>
                </form>
            </div>
        </div>

        <table class="table">
            <tr>
                <th></th>
                <th><a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'name']) }}">{{ trans('auth.name') }}</a></th>
                <th><a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'email']) }}">{{ trans('auth.email') }}</a></th>
                <th>{{ trans('settings.role_user_roles') }}</th>
            </tr>
            @foreach($users as $user)
                <tr>
                    <td style="line-height: 0;"><img class="avatar med" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}"></td>
                    <td>
                        @if(userCan('users-manage') || $currentUser->id == $user->id)
                            <a href="{{ baseUrl("/settings/users/{$user->id}") }}">
                                @endif
                                {{ $user->name }}
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                    <td>
                        @if(userCan('users-manage') || $currentUser->id == $user->id)
                            <a href="{{ baseUrl("/settings/users/{$user->id}") }}">
                                @endif
                                {{ $user->email }}
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                    <td>
                        @foreach($user->roles as $index => $role)
                            <small><a href="{{ baseUrl("/settings/roles/{$role->id}") }}">{{$role->display_name}}</a>@if($index !== count($user->roles) -1),@endif</small>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </table>

        <div>
            {{ $users->links() }}
        </div>
    </div>

@stop
