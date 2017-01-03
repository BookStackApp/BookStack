@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{ baseUrl("/search/all?term={$searchTerm}") }}" class="text-button"><i class="zmdi zmdi-search"></i>{{ $searchTerm }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">

            <div class="col-sm-7">
                <h1>{{ $title }}</h1>
                @include('partials.entity-list', ['entities' => $entities, 'style' => 'detailed'])
                {!! $entities->links() !!}
            </div>

        </div>
    </div>
@stop