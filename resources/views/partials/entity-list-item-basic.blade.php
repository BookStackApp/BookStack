<?php $type = $entity->getType(); ?>
<a href="{{ $entity->getUrl() }}" class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item" data-entity-type="{{$type}}" data-entity-id="{{$entity->id}}">
    <div class="entity-icon text-{{$type}}">@icon($type)</div>
    <div class="content">
            <h4 class="entity-list-item-name break-text">{{ $entity->name }}</h4>
            {{ $slot ?? '' }}
    </div>
</a>