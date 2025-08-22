@extends('layouts.simple')

@section('body')
    <div component="query-manager" class="container small pt-xxl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Start a Query</h1>
            <form action="{{ url('/query') }}"
                  refs="query-manager@form"
                  title="Run Query"
                  method="post">
                <fieldset class="query-form" refs="query-manager@fieldset">
                    <textarea name="query"
                              refs="query-manager@input"
                              class="input-fill-width"
                              rows="5"
                              placeholder="Enter a query"
                              autocomplete="off">{{ $query }}</textarea>
                    <button class="button icon">@icon('search')</button>
                </fieldset>
            </form>
        </div>

        <div class="card content-wrap auto-height pb-xl">
            <h2 class="list-heading">Generated Response</h2>
            <div refs="query-manager@generated-loading" hidden>
                @include('common.loading-icon')
            </div>
            <p refs="query-manager@generated-display">
                <span class="text-muted italic">
                When you run a query, the relevant content found & shown below will be used to help generate a smart machine generated response.
                </span>
            </p>
        </div>


        <div class="card content-wrap auto-height pb-xl">
            <h2 class="list-heading">Relevant Content</h2>
            <div refs="query-manager@content-loading" hidden>
                @include('common.loading-icon')
            </div>
            <div class="book-contents">
                <div refs="query-manager@content-display" class="entity-list">
                    <p class="text-muted italic mx-m">
                        Start a query to find relevant matching content.
                        The items shown here reflect those used to help provide the above response.
                    </p>
                </div>
            </div>
        </div>
    </div>
@stop
