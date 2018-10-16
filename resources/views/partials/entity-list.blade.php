
<div class="entity-list {{ $style ?? '' }}">
    @if(count($entities) > 0)
        @foreach($entities as $index => $entity)
            @include('partials.entity-list-item', ['entity' => $entity])
        @endforeach
    @else
        <p class="text-muted empty-text">
            {{ $emptyText ?? trans('common.no_items') }}
        </p>
    @endif
</div>