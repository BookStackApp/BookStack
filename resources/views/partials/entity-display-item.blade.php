<?php $type = $entity->getType(); ?>
<div class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item no-hover">
    <span role="presentation" class="icon text-{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}}">@icon($type)</span>
    <div class="content">
        <div class="entity-list-item-name break-text">{{ $entity->name }}</div>
    </div>
</div>