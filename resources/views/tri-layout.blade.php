@extends('base')

@section('body-class', 'tri-layout')

@section('content')

    <div class="toolbar px-xl py-m">
        @yield('toolbar')
    </div>

    <div class="tri-layout-container" tri-layout @yield('container-attrs') >

        <div class="tri-layout-left print-hidden " id="sidebar">
            @yield('left')
        </div>

        <div class="@yield('body-wrap-classes') tri-layout-middle">
            @yield('body')
        </div>

        <div class="tri-layout-right print-hidden">
            @yield('right')
        </div>
    </div>

@stop
