@extends('base')

@section('content')

    <div class="flex-fill flex">
        <div class="content flex">
            <div class="scroll-body">
                @yield('body')
            </div>
        </div>
    </div>

@stop
