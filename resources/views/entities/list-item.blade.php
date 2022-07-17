@component('entities.list-item-basic', ['entity' => $entity, 'classes' => (($locked ?? false) ? 'disabled ' : '') . ($classes ?? '') ])

<div class="entity-item-snippet">

    @if($locked ?? false)
        <div class="text-warn my-xxs bold">
            @icon('lock'){{ trans('entities.entity_select_lack_permission') }}
        </div>
    @endif

    @if($showPath ?? false)
        @if($entity->relationLoaded('book') && $entity->book)
            <span class="text-book">{{ $entity->book->getShortName(42) }}</span>
            @if($entity->relationLoaded('chapter') && $entity->chapter)
                <span class="text-muted entity-list-item-path-sep">@icon('chevron-right')</span> <span class="text-chapter">{{ $entity->chapter->getShortName(42) }}</span>
            @endif
        @endif
    @endif

    <p class="text-muted break-text">{{ $entity->preview_content ?? $entity->getExcerpt() }}</p>
</div>

@if(($showTags ?? false) && $entity->tags->count() > 0)
    <div class="entity-item-tags mt-xs">
        @include('entities.tag-list', ['entity' => $entity, 'linked' => false ])
    </div>
@endif

@if(($showUpdatedBy ?? false) && $entity->relationLoaded('updatedBy') && $entity->updatedBy)
    <small title="{{ $entity->updated_at->toDayDateTimeString() }}">
        {!! trans('entities.meta_updated_name', [
            'timeLength' => $entity->updated_at->diffForHumans(),
            'user' => e($entity->updatedBy->name)
        ]) !!}
    </small>
@endif

@endcomponent