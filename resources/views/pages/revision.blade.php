@extends('sidebar-layout')

@section('sidebar')
    <div class="card">
        <h3><i class="zmdi zmdi-info-outline"></i> {{ trans('common.details') }}</h3>
        <div class="body">
            @include('partials.entity-meta', ['entity' => $revision])
        </div>
    </div>
@stop

@section('body')

    <div class="container" ng-non-bindable>
        <div class="row">
            <div class="col-md-9">
                <div class="page-content">
                    @include('pages.page-display')
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        setupPageShow(null);
    </script>
@stop