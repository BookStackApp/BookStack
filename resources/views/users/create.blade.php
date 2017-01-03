@extends('base')


@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{ baseUrl('/settings/users') }}" class="text-button"><i class="zmdi zmdi-accounts"></i>Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container small" ng-non-bindable>
        <h1>Create User</h1>

        <form action="{{ baseUrl("/settings/users/create") }}" method="post">
            {!! csrf_field() !!}
            @include('users/forms/' . $authMethod)
        </form>
    </div>

@stop
