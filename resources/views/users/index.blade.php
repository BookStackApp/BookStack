@extends('base')


@section('content')

    @include('settings/navbar', ['selected' => 'users'])


    <div class="container small" ng-non-bindable>
        <h1>Users</h1>
        @if(userCan('users-manage'))
            <p>
                <a href="/settings/users/create" class="text-pos"><i class="zmdi zmdi-account-add"></i>Add new user</a>
            </p>
        @endif
        <table class="table">
            <tr>
                <th></th>
                <th>Name</th>
                <th>Email</th>
                <th>User Roles</th>
            </tr>
            @foreach($users as $user)
                <tr>
                    <td style="line-height: 0;"><img class="avatar med" src="{{$user->getAvatar(40)}}" alt="{{$user->name}}"></td>
                    <td>
                        @if(userCan('users-manage') || $currentUser->id == $user->id)
                            <a href="/settings/users/{{$user->id}}">
                                @endif
                                {{ $user->name }}
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                    <td>
                        @if(userCan('users-manage') || $currentUser->id == $user->id)
                            <a href="/settings/users/{{$user->id}}">
                                @endif
                                {{ $user->email }}
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                    <td>
                       <small> {{ $user->roles->implode('display_name', ', ') }}</small>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>

@stop
