@extends('layouts.simple')

@section('body')

    <div class="container small">

        <main class="card content-wrap auto-height mt-xxl">
            <div class="grid half left-focus v-end gap-m wrap">
                <div>
                    <h1 class="list-heading">{{ trans('entities.import') }}</h1>
                    <p class="text-muted mb-s">
                        TODO - Desc
{{--                        {{ trans('entities.permissions_desc') }}--}}
                    </p>
                </div>
            </div>
            <form action="{{ url('/import') }}" method="POST">
                {{ csrf_field() }}
                <div class="flex-container-row justify-flex-end">
                    <div class="form-group mb-m">
                        @include('form.checkbox', ['name' => 'images', 'label' => 'Include Images'])
                        @include('form.checkbox', ['name' => 'attachments', 'label' => 'Include Attachments'])
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ url('/books') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button type="submit" class="button">{{ trans('entities.import') }}</button>
                </div>
            </form>
        </main>
    </div>

@stop
