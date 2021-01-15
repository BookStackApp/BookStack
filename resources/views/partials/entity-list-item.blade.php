@component('partials.entity-list-item-basic', ['entity' => $entity, 'showTags' => $showTags])
<div class="entity-item-snippet">

    @if($showPath ?? false)
        @if($entity->book_id)
            <span class="text-book">{{ $entity->book->getShortName(42) }}</span>
            @if($entity->chapter_id)
                <span class="text-muted entity-list-item-path-sep">@icon('chevron-right')</span> <span class="text-chapter">{{ $entity->chapter->getShortName(42) }}</span>
            @endif
        @endif
    @endif

    <p class="text-muted break-text">{{ $entity->getExcerpt() }}</p>
</div>
@endcomponent