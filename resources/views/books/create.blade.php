@extends('base')

@section('content')

    <div class="row">

        <div class="col-md-3 page-menu">
            <h4>You are creating a new book.</h4>
        </div>

        <div class="col-md-9 page-content">
            <form action="/books" method="POST">
                @include('books/form')
            </form>
        </div>

    </div>

@stop