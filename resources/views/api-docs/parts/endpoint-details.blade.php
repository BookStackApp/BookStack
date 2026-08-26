<div class="mb-m">
    @foreach(explode("\n", $endpoint['description'] ?? '') as $descriptionBlock)
        <p class="mb-xxs">{{ $descriptionBlock }}</p>
    @endforeach
</div>

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