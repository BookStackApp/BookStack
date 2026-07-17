@extends('layouts.simple')

@section('body')

    <div component="api-nav" class="container">

        <div class="grid right-focus reverse-collapse">
            <div>

                <div refs="api-nav@sidebar" class="sticky-sidebar">

                    <div class="sticky-sidebar-header py-xl">
                        <select refs="api-nav@select" name="navigation" id="navigation">
                            <option value="getting-started" selected>Jump To Section</option>
                            <option value="getting-started">Getting Started</option>
                            @foreach($docs as $model => $endpoints)
                                <option value="{{ str_replace(' ', '-', $model) }}">{{ ucfirst($model) }}</option>
                                @if($model === 'docs' || $model === 'shelves')
                                    <hr>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-xl">
                        <p id="sidebar-header-getting-started" class="text-uppercase text-muted mb-xm"><strong>Getting Started</strong></p>
                        <div class="text-mono">
                            <div class="mb-xs"><a href="#authentication">Authentication</a></div>
                            <div class="mb-xs"><a href="#request-format">Request Format</a></div>
                            <div class="mb-xs"><a href="#listing-endpoints">Listing Endpoints</a></div>
                            <div class="mb-xs"><a href="#error-handling">Error Handling</a></div>
                            <div class="mb-xs"><a href="#rate-limits">Rate Limits</a></div>
                            <div class="mb-xs"><a href="#content-security">Content Security</a></div>
                        </div>
                    </div>

                    @foreach($docs as $model => $endpoints)
                        <div class="mb-xl">
                            <p id="sidebar-header-{{ str_replace(' ', '-', $model) }}" class="text-uppercase text-muted mb-xm"><strong>{{ $model }}</strong></p>

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
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-xl" style="overflow: auto;">

                <section id="section-getting-started" component="code-highlighter" class="card content-wrap auto-height">
                    @include('api-docs.parts.getting-started')
                </section>

                @foreach($docs as $model => $endpoints)
                    <section id="section-{{ str_replace(' ', '-', $model) }}" class="card content-wrap auto-height">
                        <h1 class="list-heading text-capitals">{{ $model }}</h1>
                        @if($endpoints[0]['model_description'])
                            <p>{{ $endpoints[0]['model_description'] }}</p>
                        @endif
                        @foreach($endpoints as $endpoint)
                            @include('api-docs.parts.endpoint', ['endpoint' => $endpoint, 'loop' => $loop])
                        @endforeach
                    </section>
                @endforeach
            </div>

        </div>


    </div>
@stop