@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('pages._breadcrumbs', ['page' => $page])
                </div>
            </div>
        </div>
    </div>

    <div class="container" ng-non-bindable>
        <h1>{{ trans('entities.pages_permissions') }}</h1>
        @include('form.restriction-form', ['model' => $page])
    </div>

@stop
