@extends('simple-layout')

@section('body')
    <div class="container mt-m">
        <div class="grid right-focus gap-xl">
            <div>

                <div class="actions mb-xl">
                    <h5>{{ trans('common.actions') }}</h5>
                    <div class="icon-list text-primary">
                        @include('partials.view-toggle', ['view' => $view, 'type' => 'shelf'])
                        @include('components.expand-toggle', ['target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
                    </div>
                </div>

                @include('common.home-sidebar')
            </div>
            <div>
                @include('shelves.list', ['shelves' => $shelves, 'view' => $view])
            </div>
        </div>
    </div>
@stop