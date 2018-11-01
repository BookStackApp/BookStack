@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('chapters._breadcrumbs', ['chapter' => $chapter])
    </div>
@stop

@section('body')

    <div class="container">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('lock') {{ trans('entities.chapters_permissions') }}</h3>
            <div class="body">
                @include('form/restriction-form', ['model' => $chapter])
            </div>
        </div>
    </div>

@stop
