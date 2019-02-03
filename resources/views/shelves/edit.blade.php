@extends('simple-layout')

@section('body')

    <div class="container small">

        <div class="my-l">
            @include('partials.breadcrumbs', ['crumbs' => [
                $shelf,
                $shelf->getUrl('/edit') => trans('entities.shelves_edit')
            ]])
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.shelves_edit') }}</h1>
            <form action="{{ $shelf->getUrl() }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                @include('shelves/form', ['model' => $shelf])
            </form>
        </div>
    </div>

    @include('components.image-manager', ['imageType' => 'cover'])
@stop