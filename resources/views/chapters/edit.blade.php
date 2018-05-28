@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('chapters._breadcrumbs', ['chapter' => $chapter])
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('edit') {{ trans('entities.chapters_edit') }}</h3>
            <div class="body">
                <form action="{{  $chapter->getUrl() }}" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    @include('chapters/form', ['model' => $chapter])
                </form>
            </div>
        </div>
    </div>

@stop