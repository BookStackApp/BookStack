@extends('base')

@section('body-class', 'sidebar-layout')

@section('content')

    <div class="toolbar px-l py-m">
        @yield('toolbar')
    </div>


    <div class="flex-fill flex" @yield('container-attrs') >

        <div sidebar class="sidebar flex print-hidden" id="sidebar">
            <div class="sidebar-toggle primary-background-light">@icon('caret-right-circle')
            </div>
            <div class="scroll-body px-m">
                @yield('sidebar')
            </div>
        </div>

        <div class="content mr-m flex @yield('body-wrap-classes')">
            @yield('body')
        </div>
    </div>


@stop
