@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'settings'])

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('settings.sort_set_edit') }}</h1>

            <form action="{{ $set->getUrl() }}" method="POST">
                {{ method_field('PUT') }}
                {{ csrf_field() }}

                @include('settings.sort-sets.parts.form', ['model' => $set])

                <div class="form-group text-right">
                    <a href="{{ url("/settings/sorting") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('common.save') }}</button>
                </div>
            </form>
        </div>

        <div class="card content-wrap auto-height">
            <div class="flex-container-row items-center gap-l">
                <div>
                    <h2 class="list-heading">{{ trans('settings.sort_set_delete') }}</h2>
                    <p class="text-muted">{{ trans('settings.sort_set_delete_desc') }}</p>
                </div>
                <div class="flex">
                    <form action="{{ $set->getUrl() }}" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <div class="text-right">
                            <button type="submit" class="button outline">{{ trans('common.delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop
