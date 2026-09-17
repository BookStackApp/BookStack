@extends('layouts.simple')

@section('body')

    <div component="api-nav" class="container api-docs">

        <div class="grid right-focus reverse-collapse">
            <div>

                <div refs="api-nav@sidebar" class="sticky-sidebar">

                    <div class="sticky-sidebar-header pt-xl pb-m">
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

                    <div class="flex-container-row wrap gap-xs mb-s">
                        <a href="{{ url('/api/docs/download?format=html') }}" title="Download API docs as HTML" class="button outline small">@icon('download')<span>HTML</span></a>
                        <a href="{{ url('/api/docs/download?format=json') }}" title="Download API docs as JSON" class="button outline small">@icon('download')<span>JSON</span></a>
                    </div>

                    <div class="mb-xl">
                        <p id="sidebar-header-getting-started" class="text-uppercase text-muted mb-xm"><strong>Getting Started</strong></p>
                        <div class="text-mono">
                            @foreach($gettingStartedSections as $id => $label)
                                <div class="mb-xs"><a href="#{{ $id }}">{{ $label }}</a></div>
                            @endforeach
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
                        <h2 class="list-heading text-capitals">{{ $model }}</h2>
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