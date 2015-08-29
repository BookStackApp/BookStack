@extends('base')


@section('content')


    <div class="row faded-small">
        <div class="col-md-6"></div>
        <div class="col-md-6 faded">
            <div class="action-buttons">
                @if($currentUser->can('user-create'))
                    <a href="/users/create" class="text-pos"><i class="zmdi zmdi-account-add"></i>New User</a>
                @endif
            </div>
        </div>
    </div>


    <div class="page-content">
        <h1>Users</h1>
        <table class="table">
            <tr>
                <th></th>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
            </tr>
            @foreach($users as $user)
                <tr>
                    <td style="line-height: 0;"><img class="avatar" src="{{$user->getAvatar(40)}}" alt="{{$user->name}}"></td>
                    <td>
                        @if($currentUser->can('user-update') || $currentUser->id == $user->id)
                            <a href="/users/{{$user->id}}">
                        @endif
                                {{$user->name}}
                        @if($currentUser->can('user-update') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                    <td>{{$user->email}}</td>
                    <td>{{ $user->role->display_name }}</td>
                </tr>
            @endforeach
        </table>
    </div>

@stop
