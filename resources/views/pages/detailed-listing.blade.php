@extends('simple-layout')

@section('body')
    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>{{ $title }}</h3>
            @include('partials/entity-list', ['entities' => $pages, 'style' => 'detailed'])
            <div class="body text-center">
                {!! $pages->links() !!}
            </div>
        </div>

    </div>
@stop