@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('chapters._breadcrumbs', ['chapter' => $chapter])
    </div>
@stop

@section('body')

    <div class="container" ng-non-bindable>
        <p>&nbsp;</p>
        <div class="card">
            <h3><i class="zmdi zmdi-lock-outline"></i> {{ trans('entities.chapters_permissions') }}</h3>
            <div class="body">
                @include('form/restriction-form', ['model' => $chapter])
            </div>
        </div>
    </div>

@stop
