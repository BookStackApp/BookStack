<?php $type = $entity->getType(); ?>
<a href="{{ $entity->getUrl() }}" class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} entity-list-item" data-entity-type="{{$type}}" data-entity-id="{{$entity->id}}">
        <div class="entity-icon text-{{$type}}">@icon($type)</div>
        <div class="content">

                <h4 class="entity-list-item-name break-text">{{ $entity->name }}</h4>

                <div class="entity-item-snippet">
                     <p class="text-muted break-text">{{ $entity->getExcerpt() }}</p>
                </div>

        </div>
</a>