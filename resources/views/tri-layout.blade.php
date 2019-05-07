@extends('base')

@section('body-class', 'tri-layout')

@section('content')

    <div class="tri-layout-mobile-tabs text-primary" >
        <div class="grid half no-break no-gap">
            <div class="tri-layout-mobile-tab px-m py-s" tri-layout-mobile-tab="info">
                {{ trans('common.tab_info') }}
            </div>
            <div class="tri-layout-mobile-tab px-m py-s active" tri-layout-mobile-tab="content">
                {{ trans('common.tab_content') }}
            </div>
        </div>
    </div>

    <div class="tri-layout-container" tri-layout @yield('container-attrs') >

        <div class="tri-layout-left print-hidden pt-m" id="sidebar">
            <div class="tri-layout-left-contents">
                @yield('left')
            </div>
        </div>

        <div class="@yield('body-wrap-classes') tri-layout-middle">
            <div class="tri-layout-middle-contents">
                @yield('body')
            </div>
        </div>

        <div class="tri-layout-right print-hidden pt-m">
            <div class="tri-layout-right-contents">
                @yield('right')
            </div>
        </div>
    </div>

@stop
