@extends('base')

@section('content')


    <div class="container" ng-non-bindable>
        <div class="row">
            <div class="col-md-9">
                <div class="page-content anim fadeIn">
                    @include('pages.page-display')
                </div>
            </div>
        </div>
    </div>


    @include('partials.highlight')
@stop
