<?php $type = $entity->getType(); ?>
<a href="{{ $entity->getUrl() }}" class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item" data-entity-type="{{$type}}" data-entity-id="{{$entity->id}}">
    <span role="presentation" class="icon text-{{$type}}">@icon($type)</span>
    <div class="content">
            <h4 class="entity-list-item-name break-text">
                {!! highlightText($entity->name, [request()->get('search'), request()->get('term')]) !!}
            </h4>
            {{ $slot ?? '' }}
    </div>
</a>