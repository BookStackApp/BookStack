@extends('base')


@section('content')

    <div class="container small" ng-non-bindable>
        <h1>Create User</h1>

        <form action="{{ baseUrl("/settings/users/create") }}" method="post">
            {!! csrf_field() !!}
            @include('users/forms/' . $authMethod)
        </form>
    </div>

@stop
