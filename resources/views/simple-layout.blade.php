@extends('base')

@section('body-class', 'shaded')

@section('content')

    <div class="toolbar-container">
        @yield('toolbar')
    </div>


    <div class="flex-fill flex">
        <div class="content flex">
            <div class="scroll-body">
                @yield('body')
            </div>
        </div>
    </div>


@stop
