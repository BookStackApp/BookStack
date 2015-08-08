@extends('base')


@section('content')

    <div class="row faded-small">
        <div class="col-md-6"></div>
        <div class="col-md-6 faded">
            <div class="action-buttons">
                <a href="/users/{{$user->id}}/delete" class="text-neg"><i class="zmdi zmdi-delete"></i>Delete User</a>
            </div>
        </div>
    </div>

    <div class="page-content">
        <h1>Edit User</h1>

        <form action="/users/{{$user->id}}" method="post">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="put">
            @include('users/form', ['model' => $user])
        </form>
    </div>

@stop
