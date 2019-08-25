@extends('tri-layout')

@section('body')
    <div class="mt-m">
        <main class="content-wrap card">
            <div class="page-content" page-display="{{ $customHomepage->id }}">
                @include('pages.page-display', ['page' => $customHomepage])
            </div>
        </main>
    </div>
@stop

@section('left')
    @include('common.home-sidebar')
@stop

@section('right')
    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
            @include('components.expand-toggle', ['target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
        </div>
    </div>
@stop