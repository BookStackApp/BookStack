@extends('layouts.simple')

@section('body')

    <div class="container small">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $record,
                $record->getUrl('/edit') => [
                    'text' => trans('entities.records_edit'),
                    'icon' => 'edit',
                ]
            ]])
        </div>

        <main class="content-wrap card auto-height">
            <h1 class="list-heading">{{ trans('entities.records_edit') }}</h1>
            <form action="{{ $record->getUrl() }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                @include('records.parts.form', [
                    'model' => $record,
                    'returnLocation' => $record->getUrl()
                ])
            </form>
        </main>

        @if(userCan('record-delete', $record) && userCan('record-create-all'))
            @include('records.parts.convert-to-shelf', ['record' => $record])
        @endif
    </div>
@stop
