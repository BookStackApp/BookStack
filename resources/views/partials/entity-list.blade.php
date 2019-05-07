@if(count($entities) > 0)
    <div class="entity-list {{ $style ?? '' }}">
        @foreach($entities as $index => $entity)
            @include('partials.entity-list-item', ['entity' => $entity, 'showPath' => $showPath ?? false])
        @endforeach
    </div>
@else
    <p class="text-muted empty-text">
        {{ $emptyText ?? trans('common.no_items') }}
    </p>
@endif