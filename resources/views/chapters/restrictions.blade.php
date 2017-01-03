@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    @include('chapters._breadcrumbs', ['chapter' => $chapter])
                </div>
            </div>
        </div>
    </div>

    <div class="container" ng-non-bindable>
        <h1>{{ trans('entities.chapters_permissions') }}</h1>
        @include('form/restriction-form', ['model' => $chapter])
    </div>

@stop
