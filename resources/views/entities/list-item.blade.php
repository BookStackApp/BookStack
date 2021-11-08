@component('entities.list-item-basic', ['entity' => $entity])

<div class="entity-item-snippet">

    @if($showPath ?? false)
        @if($entity->relationLoaded('book') && $entity->book)
            <span class="text-book">{{ $entity->book->getShortName(42) }}</span>
            @if($entity->relationLoaded('chapter') && $entity->chapter)
                <span class="text-muted entity-list-item-path-sep">@icon('chevron-right')</span> <span class="text-chapter">{{ $entity->chapter->getShortName(42) }}</span>
            @endif
        @endif
    @endif

    <p class="text-muted break-text">{{ $entity->getExcerpt() }}</p>
</div>

@if(($showTags ?? false) && $entity->tags->count() > 0)
    <div class="entity-item-tags mt-xs">
        @include('entities.tag-list', ['entity' => $entity, 'linked' => false ])
    </div>
@endif

@endcomponent