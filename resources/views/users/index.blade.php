@extends('base')


@section('content')

    @include('settings/navbar', ['selected' => 'users'])


    <div class="container small" ng-non-bindable>
        <div class="row action-header">
            <div class="col-sm-8">
                <h1>Users</h1>
            </div>
            <div class="col-sm-4">
                <p></p>
                @if(userCan('users-manage'))
                    <a href="/settings/users/create" class="pos button float right"><i class="zmdi zmdi-account-add"></i>Add new user</a>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8">
                <div class="compact">
                    {!! $users->links() !!}
                </div>
            </div>
            <div class="col-sm-4">
                <form method="get" class="float right" action="/settings/users">
                    @foreach(collect($listDetails)->except('search') as $name => $val)
                        <input type="hidden" name="{{$name}}" value="{{$val}}">
                    @endforeach
                    <input type="text" name="search" placeholder="Search Users" @if($listDetails['search']) value="{{$listDetails['search']}}" @endif>
                </form>
            </div>
        </div>
        <div class="text-center">

        </div>

        <table class="table">
            <tr>
                <th></th>
                <th><a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'name']) }}">Name</a></th>
                <th><a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'email']) }}">Email</a></th>
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
                        @foreach($user->roles as $index => $role)
                            <small><a href="/settings/roles/{{$role->id}}">{{$role->display_name}}</a>@if($index !== count($user->roles) -1),@endif</small>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </table>

        <div>
            {!! $users->links() !!}
        </div>
    </div>

@stop
