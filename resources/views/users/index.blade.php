@extends('base')


@section('content')

    @include('settings/navbar', ['selected' => 'users'])


    <div class="container small" ng-non-bindable>
        <h1>Users</h1>
        @if($currentUser->can('user-create'))
            <p>
                <a href="/users/create" class="text-pos"><i class="zmdi zmdi-account-add"></i>Add new user</a>
            </p>
        @endif
        <table class="table">
            <tr>
                <th></th>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
            </tr>
            @foreach($users as $user)
                <tr>
                    <td style="line-height: 0;"><img class="avatar med" src="{{$user->getAvatar(40)}}" alt="{{$user->name}}"></td>
                    <td>
                        @if($currentUser->can('user-update') || $currentUser->id == $user->id)
                            <a href="/users/{{$user->id}}">
                                @endif
                                {{ $user->name }}
                                @if($currentUser->can('user-update') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                    <td>
                        @if($currentUser->can('user-update') || $currentUser->id == $user->id)
                            <a href="/users/{{$user->id}}">
                                @endif
                                {{ $user->email }}
                                @if($currentUser->can('user-update') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                    <td>{{ $user->role->display_name }}</td>
                </tr>
            @endforeach
        </table>
    </div>

@stop
