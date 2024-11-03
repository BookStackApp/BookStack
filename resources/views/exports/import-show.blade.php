@extends('layouts.simple')

@section('body')

    <div class="container small">

        <main class="card content-wrap auto-height mt-xxl">
            <h1 class="list-heading">{{ trans('entities.import_continue') }}</h1>
            <form action="{{ url('/import') }}" enctype="multipart/form-data" method="POST">
                {{ csrf_field() }}
            </form>

            <div class="text-right">
                <a href="{{ url('/import') }}" class="button outline">{{ trans('common.cancel') }}</a>
                <div component="dropdown" class="inline block mx-s">
                    <button refs="dropdown@toggle"
                            type="button"
                            title="{{ trans('common.delete') }}"
                            class="button outline">{{ trans('common.delete') }}</button>
                    <div refs="dropdown@menu" class="dropdown-menu">
                        <p class="text-neg bold small px-m mb-xs">{{ trans('entities.import_delete_confirm') }}</p>
                        <p class="small px-m mb-xs">{{ trans('entities.import_delete_desc') }}</p>
                        <button type="submit" form="import-delete-form" class="text-link small text-item">{{ trans('common.confirm') }}</button>
                    </div>
                </div>
                <button type="submit" class="button">{{ trans('entities.import_run') }}</button>
            </div>
        </main>
    </div>

    <form id="import-delete-form"
          action="{{ $import->getUrl() }}"
          method="post">
        {{ method_field('DELETE') }}
        {{ csrf_field() }}
    </form>

@stop
