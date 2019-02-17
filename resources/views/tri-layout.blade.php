@extends('base')

@section('body-class', 'tri-layout')

@section('content')

    {{--<div class="toolbar px-xl">--}}
        {{--@yield('toolbar')--}}
    {{--</div>--}}

    <div class="tri-layout-container mt-m" tri-layout @yield('container-attrs') >

        <div class="tri-layout-left print-hidden pt-m" id="sidebar">
            <div class="tri-layout-left-contents">
                @yield('left')
            </div>
        </div>

        <div class="@yield('body-wrap-classes') tri-layout-middle">
            @yield('body')
        </div>

        <div class="tri-layout-right print-hidden pt-m">
            <div class="tri-layout-right-contents">
                @yield('right')
            </div>
        </div>
    </div>

@stop
