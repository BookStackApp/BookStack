@if(count($entities) > 0)
    <div class="entity-list {{ $style ?? '' }}">
        @foreach($entities as $index => $entity)
            @include('entities.list-item', ['entity' => $entity, 'showPath' => $showPath ?? false, 'showTags' => $showTags ?? false])
        @endforeach
    </div>
@else
    <p class="text-muted empty-text pb-l mb-none">
        {{ $emptyText ?? trans('common.no_items') }}
    </p>
@endif