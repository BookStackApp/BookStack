@extends('layouts.simple')

@section('body')
    <div component="query-manager" class="container small pt-xxl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Start a Query</h1>
            <form action="{{ url('/query') }}"
                  refs="query-manager@form"
                  title="Run Query"
                  method="post"
                  class="query-form">
                <textarea name="query"
                          refs="query-manager@input"
                          class="input-fill-width"
                          rows="5"
                          placeholder="Enter a query"
                          autocomplete="off">{{ $query }}</textarea>
                <button class="button icon">@icon('search')</button>
            </form>
        </div>

        <div class="card content-wrap auto-height pb-xl">
            <h2 class="list-heading">Generated Response</h2>
            <div refs="query-manager@generated-loading">
                @include('common.loading-icon')
            </div>
            <p refs="query-manager@generated-display">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ad adipisci aliquid architecto cupiditate dolor doloribus eligendi et expedita facilis fugiat fugit illo, ipsa laboriosam maiores, molestias mollitia non obcaecati porro quasi quis quos reprehenderit rerum sunt tenetur ullam unde voluptate voluptates! Distinctio et eum id molestiae nisi quisquam sed ut.</p>
        </div>


        <div class="card content-wrap auto-height pb-xl">
            <h2 class="list-heading">Relevant Content</h2>
            <div refs="query-manager@content-loading">
                @include('common.loading-icon')
            </div>
            <div class="book-contents">
                <div refs="query-manager@content-display" class="entity-list">
                    @include('entities.list', ['entities' => $entities, 'showPath' => true, 'showTags' => true])
                </div>
            </div>
        </div>

{{--        @if($results)--}}
{{--            <h2>Results</h2>--}}

{{--            <h3>LLM Output</h3>--}}
{{--            <p>{{ $results['llm_result'] }}</p>--}}

{{--            <h3>Entity Matches</h3>--}}
{{--            @foreach($results['entity_matches'] as $match)--}}
{{--                <div>--}}
{{--                    <div><strong>{{ $match['entity_type'] }}:{{ $match['entity_id'] }}; Distance: {{ $match['distance'] }}</strong></div>--}}
{{--                    <details>--}}
{{--                        <summary>match text</summary>--}}
{{--                         <div>{{ $match['text'] }}</div>--}}
{{--                    </details>--}}
{{--                </div>--}}
{{--            @endforeach--}}
{{--        @endif--}}
    </div>
@stop
