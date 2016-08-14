@extends('base')

@section('content')

    <div class="container">
        <div class="row">

            <div class="col-sm-7">
                <h1>{{ $title }} <small>{{ $searchTerm }}</small></h1>
                @include('partials.entity-list', ['entities' => $entities, 'style' => 'detailed'])
                {!! $entities->links() !!}
            </div>

            <div class="col-sm-4 col-sm-offset-1"></div>

        </div>
    </div>
@stop