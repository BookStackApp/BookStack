<?php 
$type = $entity->getType();
$shareToken = $shareToken ?? null;
$shareLink = $shareLink ?? null;
$currentEntityId = $currentEntityId ?? null;
$allowsNavigation = $shareLink && ($shareLink->entity instanceof \BookStack\Entities\Models\Book || $shareLink->entity instanceof \BookStack\Entities\Models\Chapter);
?>
@if($shareToken && $allowsNavigation)
    {{-- We're in a shared book context, all child entities are accessible via query params --}}
    @if($currentEntityId && $entity->id == $currentEntityId)
        {{-- Current entity being viewed --}}
        <span class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item selected"
              data-entity-type="{{$type}}"
              data-entity-id="{{$entity->id}}">
            <span role="presentation" class="icon text-{{$type}}">@icon($type)</span>
            <div class="content">
                <h4 class="entity-list-item-name break-text">{{ $entity->preview_name ?? $entity->name }}</h4>
                {{ $slot ?? '' }}
            </div>
        </span>
    @else
        {{-- Link to child entity within shared book --}}
        <a href="{{ url('/share/' . $shareToken . '?entity=' . $type . '&id=' . $entity->id) }}"
           class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item"
           data-entity-type="{{$type}}"
           data-entity-id="{{$entity->id}}">
            <span role="presentation" class="icon text-{{$type}}">@icon($type)</span>
            <div class="content">
                <h4 class="entity-list-item-name break-text">{{ $entity->preview_name ?? $entity->name }}</h4>
                {{ $slot ?? '' }}
            </div>
        </a>
    @endif
@elseif($shareToken)
    <?php
    // Find share link for this entity
    $shareLink = \BookStack\Entities\Models\EntityShareLink::query()
        ->where('entity_id', '=', $entity->id)
        ->where('entity_type', '=', $entity->getMorphClass())
        ->first();
    ?>
    @if($shareLink && $shareLink->token === $shareToken)
        {{-- Current entity being viewed --}}
        <span class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item selected"
              data-entity-type="{{$type}}"
              data-entity-id="{{$entity->id}}">
            <span role="presentation" class="icon text-{{$type}}">@icon($type)</span>
            <div class="content">
                <h4 class="entity-list-item-name break-text">{{ $entity->preview_name ?? $entity->name }}</h4>
                {{ $slot ?? '' }}
            </div>
        </span>
    @else
        {{-- No navigation allowed when viewing a page/chapter share link --}}
        <span class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item"
              data-entity-type="{{$type}}"
              data-entity-id="{{$entity->id}}">
            <span role="presentation" class="icon text-{{$type}}">@icon($type)</span>
            <div class="content">
                <h4 class="entity-list-item-name break-text">{{ $entity->preview_name ?? $entity->name }}</h4>
                {{ $slot ?? '' }}
            </div>
        </span>
    @endif
@else
    <a href="{{ $entity->getUrl() }}"
       class="{{$type}} {{$type === 'page' && $entity->draft ? 'draft' : ''}} {{$classes ?? ''}} entity-list-item"
       data-entity-type="{{$type}}"
       data-entity-id="{{$entity->id}}">
        <span role="presentation" class="icon text-{{$type}}">@icon($type)</span>
        <div class="content">
            <h4 class="entity-list-item-name break-text">{{ $entity->preview_name ?? $entity->name }}</h4>
            {{ $slot ?? '' }}
        </div>
    </a>
@endif
