@extends('base')

@section('body-class', 'shaded')

@section('content')

    <div class="toolbar-container">
        <div class="faded-small toolbar">
            <div class="container fluid">
                <div class="row">
                    @yield('toolbar')
                </div>
            </div>
        </div>
    </div>


    <div class="flex-fill flex">
        <div class="content flex">
            <div class="scroll-body">
                @yield('body')
            </div>
        </div>
    </div>


@stop
