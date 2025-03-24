@extends('layouts.simple')

@section('body')
    <div class="container mt-xl" id="search-system">

        <form action="{{ url('/search/query') }}" method="get">
            <input name="query" type="text">
            <button class="button">Query</button>
        </form>

        @if($results)
            <h2>Results</h2>

            <h3>LLM Output</h3>
            <p>{{ $results['llm_result'] }}</p>

            <h3>Entity Matches</h3>
            @foreach($results['entity_matches'] as $match)
                <div>
                    <div><strong>{{ $match['entity_type'] }}:{{ $match['entity_id'] }}; Distance: {{ $match['distance'] }}</strong></div>
                    <details>
                        <summary>match text</summary>
                         <div>{{ $match['text'] }}</div>
                    </details>
                </div>
            @endforeach
        @endif
    </div>
@stop
