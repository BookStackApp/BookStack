@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('pages._breadcrumbs', ['page' => $page])
    </div>
@stop

@section('body')
    <div class="container">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('lock') {{ trans('entities.pages_permissions') }}</h3>
            <div class="body">
                @include('form.entity-permissions', ['model' => $page])
            </div>
        </div>
    </div>
@stop
