<?php $type = $entity->getType(); ?>
<div class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item" data-entity-type="{{$type}}" data-entity-id="{{$entity->id}}">
    <a href="{{ $entity->getUrl() }}"></a>
    <span role="presentation" class="icon text-{{$type}}">@icon($type)</span>
    <div class="content">
            <h4 class="entity-list-item-name break-text">
                <span>{{ $entity->preview_name ?? $entity->name }}</span>
                @if (($showQuickAdd ?? false) &&
                    $entity instanceof \BookStack\Entities\Models\Book &&
                    userCan('page-create', $entity))
                    <a href="{{ $entity->getUrl('/create-page') }}" class="add-shortcut">+ Add</a>
                @endif
            </h4>
            {{ $slot ?? '' }}
    </div>
</div>