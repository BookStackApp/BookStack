@extends('layouts.simple')

@section('body')
    <div class="container small">
        <main class="content-wrap card">
            <h1 class="list-heading">{{ trans('entities.records_sort') }}</h1>
            {{-- Sorting UI for records goes here --}}
        </main>
    </div>
@stop
