<div class="flex-container-row items-center gap-m">
    <span class="api-method text-mono" data-method="{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
    <h5 id="{{ $endpoint['name'] }}" class="text-mono pb-xs">
        @if(str_starts_with($endpoint['controller_method_kebab'], 'list') && !str_contains($endpoint['uri'], '{'))
            <a style="color: inherit;" target="_blank" rel="noopener" href="{{ url($endpoint['uri']) }}">{{ url($endpoint['uri']) }}</a>
        @else
            <span>{{ url($endpoint['uri']) }}</span>
        @endif
    </h5>
    <h6 class="text-uppercase text-muted text-mono ml-auto">{{ $endpoint['controller_method_kebab'] }}</h6>
</div>

@include('api-docs.parts.endpoint-details')

@if(!$loop->last)
    <hr>
@endif