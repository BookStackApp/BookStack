@extends('public')

@section('body-class', 'image-cover login')

@section('sidebar')


    {{--<div class="row faded-small">--}}
        {{--<div class="col-md-6"></div>--}}
        {{--<div class="col-md-6 faded">--}}
            {{--<div class="action-buttons">--}}
                {{--<a href="/books/create" class="text-pos"><i class="zmdi zmdi-plus"></i>Add new book</a>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

    <div class="text-center">
        <div class="center-box text-left">
            <h1>Login</h1>

            <form action="/login" method="POST">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">Email</label>
                    @include('form/text', ['name' => 'email'])
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    @include('form/password', ['name' => 'password'])
                </div>

                <div class="from-group">
                    <button class="button block pos">Login</button>
                </div>
            </form>
        </div>
    </div>

@stop