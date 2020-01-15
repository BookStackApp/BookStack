@extends('simple-layout')

@section('body')

    <div class="container pt-xl">

        <div class="grid right-focus reverse-collapse">

            <div>
                @foreach($docs as $model => $endpoints)
                    <p class="text-uppercase text-muted mb-xm mt-l"><strong>{{ $model }}</strong></p>

                    @foreach($endpoints as $endpoint)
                        <div class="mb-xs">
                            <a href="#{{ $endpoint['name'] }}" class="text-mono">
                                <span class="api-method" data-method="{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                                /{{ $endpoint['uri'] }}
                            </a>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div>
                @foreach($docs as $model => $endpoints)
                    <section class="card content-wrap auto-height">
                        <h1 class="list-heading text-capitals">{{ $model }}</h1>

                        @foreach($endpoints as $endpoint)
                            <h5 id="{{ $endpoint['name'] }}" class="text-mono mb-m">
                                <span class="api-method" data-method="{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                                {{ url($endpoint['uri']) }}
                            </h5>
                            <p class="mb-m">{{ $endpoint['description'] ?? '' }}</p>
                            @if($endpoint['example_response'] ?? false)
                                <details details-highlighter>
                                    <summary class="text-muted">Example Response</summary>
                                    <pre><code class="language-json">{{ $endpoint['example_response'] }}</code></pre>
                                </details>
                                <hr class="mt-m">
                            @endif
                        @endforeach
                    </section>
                @endforeach
            </div>

        </div>


    </div>
@stop