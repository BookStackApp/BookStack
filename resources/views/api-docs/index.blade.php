@extends('simple-layout')

@section('body')

    <div class="container pt-xl">

        <div class="grid right-focus reverse-collapse">

            <div>
                @foreach($docs as $model => $endpoints)
                    <p class="text-uppercase text-muted mb-xm mt-l"><strong>{{ $model }}</strong></p>

                    @foreach($endpoints as $endpoint)
                        <div class="mb-xs">
                            <a href="#{{ $endpoint['name'] }}" class="text-mono inline block mr-s">
                                <span class="api-method" data-method="{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                            </a>
                            <a href="#{{ $endpoint['name'] }}" class="text-mono">
                                {{ $endpoint['controller_method'] }}
                            </a>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div style="overflow: auto;">
                @foreach($docs as $model => $endpoints)
                    <section class="card content-wrap auto-height">
                        <h1 class="list-heading text-capitals">{{ $model }}</h1>

                        @foreach($endpoints as $endpoint)
                            <h6 class="text-uppercase text-muted float right">{{ $endpoint['controller_method'] }}</h6>
                            <h5 id="{{ $endpoint['name'] }}" class="text-mono mb-m">
                                <span class="api-method" data-method="{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                                {{ url($endpoint['uri']) }}
                            </h5>
                            <p class="mb-m">{{ $endpoint['description'] ?? '' }}</p>
                            @if($endpoint['body_params'] ?? false)
                                <details class="mb-m">
                                    <summary class="text-muted">Body Parameters</summary>
                                    <table class="table">
                                        <tr>
                                            <th>Param Name</th>
                                            <th>Value Rules</th>
                                        </tr>
                                        @foreach($endpoint['body_params'] as $paramName => $rules)
                                        <tr>
                                            <td>{{ $paramName }}</td>
                                            <td>
                                                @foreach($rules as $rule)
                                                    <code class="mr-xs">{{ $rule }}</code>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </details>
                            @endif
                            @if($endpoint['example_request'] ?? false)
                                <details details-highlighter class="mb-m">
                                    <summary class="text-muted">Example Request</summary>
                                    <pre><code class="language-json">{{ $endpoint['example_request'] }}</code></pre>
                                </details>
                            @endif
                            @if($endpoint['example_response'] ?? false)
                                <details details-highlighter class="mb-m">
                                    <summary class="text-muted">Example Response</summary>
                                    <pre><code class="language-json">{{ $endpoint['example_response'] }}</code></pre>
                                </details>
                            @endif
                            @if(!$loop->last)
                            <hr>
                            @endif
                        @endforeach
                    </section>
                @endforeach
            </div>

        </div>


    </div>
@stop