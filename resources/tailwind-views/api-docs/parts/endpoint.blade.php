<h6 class="text-uppercase text-muted float right">{{ $endpoint['controller_method_kebab'] }}</h6>

<h5 id="{{ $endpoint['name'] }}" class="text-mono mb-m">
    <span class="api-method" data-method="{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
    @if($endpoint['controller_method_kebab'] === 'list')
        <a style="color: inherit;" target="_blank" rel="noopener" href="{{ url($endpoint['uri']) }}">{{ url($endpoint['uri']) }}</a>
    @else
        {{ url($endpoint['uri']) }}
    @endif
</h5>

<p class="mb-m">{{ $endpoint['description'] ?? '' }}</p>

@if($endpoint['body_params'] ?? false)
    <details class="mb-m">
        <summary class="text-muted">{{ $endpoint['method'] === 'GET' ? 'Query' : 'Body'  }} Parameters</summary>
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
    <details component="details-highlighter" class="mb-m">
        <summary class="text-muted">Example Request</summary>
        <pre><code class="language-json">{{ $endpoint['example_request'] }}</code></pre>
    </details>
@endif

@if($endpoint['example_response'] ?? false)
    <details component="details-highlighter" class="mb-m">
        <summary class="text-muted">Example Response</summary>
        <pre><code class="language-json">{{ $endpoint['example_response'] }}</code></pre>
    </details>
@endif

@if(!$loop->last)
    <hr>
@endif