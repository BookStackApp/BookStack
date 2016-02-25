@extends('base')

@section('content')

    <div class="container small" ng-non-bindable>
        <h1>Delete User</h1>
        <p>This will fully delete this user with the name '<span class="text-neg">{{$user->name}}</span>' from the system.</p>
        <p class="text-neg">Are you sure you want to delete this user?</p>

        <form action="/settings/users/{{$user->id}}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="/users/{{$user->id}}" class="button muted">Cancel</a>
            <button type="submit" class="button neg">Confirm</button>
        </form>
    </div>

@stop
