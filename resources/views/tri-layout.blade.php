@extends('base')

@section('body-class', 'tri-layout')

@section('content')

    <div class="toolbar px-xl py-m">
        @yield('toolbar')
    </div>

    <div class="flex-fill flex" @yield('container-attrs') >

        <div sidebar class="sidebar flex print-hidden tri-layout-left" id="sidebar">
            <div class="sidebar-toggle primary-background-light">@icon('caret-right-circle')
            </div>
            <div class="scroll-body px-xl">
                @yield('left')
            </div>
        </div>

        <div class="flex @yield('body-wrap-classes')">
            @yield('body')
        </div>

        <div class="flex tri-layout-right">
            @yield('right')
        </div>
    </div>


@stop
