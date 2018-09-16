@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('shelves._breadcrumbs', ['shelf' => $shelf])
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('edit') {{ trans('entities.shelves_edit') }}</h3>
            <div class="body">
                <form action="{{ $shelf->getUrl() }}" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    @include('shelves/form', ['model' => $shelf])
                </form>
            </div>
        </div>
    </div>
@include('components.image-manager', ['imageType' => 'cover'])
@stop