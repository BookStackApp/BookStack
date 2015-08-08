@extends('base')


@section('content')


    <div class="row faded-small">
        <div class="col-md-6"></div>
        <div class="col-md-6 faded">
            <div class="action-buttons">
                <a href="/users/create" class="text-pos"><i class="zmdi zmdi-account-add"></i>New User</a>
            </div>
        </div>
    </div>


    <div class="page-content">
        <h1>Users</h1>
        <table class="table">
            <tr>
                <th>Name</th>
                <th>Email</th>
            </tr>
            @foreach($users as $user)
                <tr>
                    <td><a href="/users/{{$user->id}}">{{$user->name}}</a></td>
                    <td>{{$user->email}}</td>
                </tr>
            @endforeach
        </table>
    </div>

@stop
