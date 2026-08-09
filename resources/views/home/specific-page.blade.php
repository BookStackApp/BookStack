@extends('layouts.tri')

@section('body')
    <div class="mt-m">
        <main class="content-wrap card">
            <div component="page-display"
                 option:page-display:page-id="{{ $customHomepage->id }}"
                 class="page-content">
                @include('pages.parts.page-display', ['page' => $customHomepage])
            </div>
        </main>
    </div>
@stop

@section('left')
    @include('common.view-blocks', ['location' => 'home-non-default', 'position' => 'left'])
@stop

@section('right')
    @include('common.view-blocks', ['location' => 'home-non-default', 'position' => 'right'])
@stop