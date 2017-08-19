@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container fluid">
            <div class="row">
                @yield('toolbar')
            </div>
        </div>
    </div>


    <div class="flex-fill flex">

        <div class="sidebar flex print-hidden">
            <div class="scroll-body">
                @yield('sidebar')
            </div>
        </div>

        <div class="content flex">
            <div class="scroll-body">
                @yield('body')
            </div>
        </div>
    </div>


@stop
