@extends('base')


@section('content')

    <div class="page-content">
        <h1>Create User</h1>

        <form action="/users/create" method="post">
            {!! csrf_field() !!}
            @include('users/form')
        </form>
    </div>

@stop
