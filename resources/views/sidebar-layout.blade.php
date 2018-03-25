@extends('base')

@section('body-class', 'sidebar-layout')

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


    <div class="flex-fill flex" @yield('container-attrs') >

        <div sidebar class="sidebar flex print-hidden" id="sidebar">
            <div class="sidebar-toggle primary-background-light">@icon('caret-right-circle')
            </div>
            <div class="scroll-body">
                @yield('sidebar')
            </div>
        </div>

        <div class="content flex">
            @yield('body')
        </div>
    </div>


@stop
