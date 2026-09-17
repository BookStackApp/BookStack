@extends('layouts.simple')

@section('body')

    <div class="container px-xl py-s flex-container-row gap-l wrap justify-space-between">
        <div class="icon-list inline block">
            @include('home.parts.expand-toggle', ['classes' => 'text-muted text-link', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
        </div>
        <div>
            <div class="icon-list inline block">
                @include('common.dark-mode-toggle', ['classes' => 'text-muted icon-list-item text-link'])
            </div>
        </div>
    </div>

    <div class="container" id="home-default">
        <div class="grid third gap-x-xxl no-row-gap">
            <div>
                @include('common.view-blocks', ['location' => 'home-default', 'position' => 'left'])
            </div>

            <div>
                @include('common.view-blocks', ['location' => 'home-default', 'position' => 'center'])
            </div>

            <div>
                @include('common.view-blocks', ['location' => 'home-default', 'position' => 'right'])
            </div>
        </div>
    </div>

@stop
