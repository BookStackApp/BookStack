{{-- Shared version of page list item --}}
@php
    $shareLink = $shareLink ?? null;
    $token = $token ?? null;
    $allowsNavigation = $shareLink && ($shareLink->entity instanceof \BookStack\Entities\Models\Book || $shareLink->entity instanceof \BookStack\Entities\Models\Chapter);
@endphp
@if($allowsNavigation && $token)
    <a href="{{ url('/share/' . $token . '?entity=page&id=' . $page->id) }}" 
       class="page entity-list-item" 
       data-entity-type="page" 
       data-entity-id="{{$page->id}}">
        <span class="icon text-page">@icon('page')</span>
        <div class="content">
            <h4 class="entity-list-item-name break-text">{{ $page->name }}</h4>
            <div class="entity-item-snippet">
                <p class="text-muted break-text">{{ $page->getExcerpt() }}</p>
            </div>
        </div>
    </a>
@else
    <span class="page entity-list-item">
        <span class="icon text-page">@icon('page')</span>
        <div class="content">
            <h4 class="entity-list-item-name break-text">{{ $page->name }}</h4>
            <div class="entity-item-snippet">
                <p class="text-muted break-text">{{ $page->getExcerpt() }}</p>
            </div>
        </div>
    </span>
@endif
