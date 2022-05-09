@extends('layouts.simple')

@section('body')

    <div class="container pt-xl">

        <div class="grid right-focus reverse-collapse">
            <div>

                <div class="sticky-sidebar">
                    <p class="text-uppercase text-muted mb-xm mt-l"><strong>Getting Started</strong></p>

                    <div class="text-mono">
                        <div class="mb-xs"><a href="#authentication">Authentication</a></div>
                        <div class="mb-xs"><a href="#request-format">Request Format</a></div>
                        <div class="mb-xs"><a href="#listing-endpoints">Listing Endpoints</a></div>
                        <div class="mb-xs"><a href="#error-handling">Error Handling</a></div>
                        <div class="mb-xs"><a href="#rate-limits">Rate Limits</a></div>
                    </div>

                    @foreach($docs as $model => $endpoints)
                        <p class="text-uppercase text-muted mb-xm mt-l"><strong>{{ $model }}</strong></p>

                        @foreach($endpoints as $endpoint)
                            <div class="mb-xs">
                                <a href="#{{ $endpoint['name'] }}" class="text-mono inline block mr-s">
                                    <span class="api-method" data-method="{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                                </a>
                                <a href="#{{ $endpoint['name'] }}" class="text-mono">
                                    {{ $endpoint['controller_method_kebab'] }}
                                </a>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div style="overflow: auto;">

                <section code-highlighter class="card content-wrap auto-height">
                    @include('api-docs.parts.getting-started')
                </section>

                @foreach($docs as $model => $endpoints)
                    <section class="card content-wrap auto-height">
                        <h1 class="list-heading text-capitals">{{ $model }}</h1>

                        @foreach($endpoints as $endpoint)
                            @include('api-docs.parts.endpoint', ['endpoint' => $endpoint, 'loop' => $loop])
                        @endforeach
                    </section>
                @endforeach
            </div>

        </div>


    </div>
@stop