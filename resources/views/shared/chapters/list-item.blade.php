{{-- Shared version of chapter list item --}}
@php
    $shareLink = $shareLink ?? null;
    $token = $token ?? null;
    $allowsNavigation = $shareLink && ($shareLink->entity instanceof \BookStack\Entities\Models\Book || $shareLink->entity instanceof \BookStack\Entities\Models\Chapter);
@endphp
@if($allowsNavigation && $token)
    <a href="{{ url('/share/' . $token . '?entity=chapter&id=' . $chapter->id) }}" 
       class="chapter entity-list-item @if($chapter->visible_pages->count() > 0) has-children @endif" 
       data-entity-type="chapter" 
       data-entity-id="{{$chapter->id}}">
        <span class="icon text-chapter">@icon('chapter')</span>
        <div class="content">
            <h4 class="entity-list-item-name break-text">{{ $chapter->name }}</h4>
            <div class="entity-item-snippet">
                <p class="text-muted break-text">{{ $chapter->getExcerpt() }}</p>
            </div>
        </div>
    </a>
@else
    <span class="chapter entity-list-item @if($chapter->visible_pages->count() > 0) has-children @endif" 
          data-entity-type="chapter" 
          data-entity-id="{{$chapter->id}}">
        <span class="icon text-chapter">@icon('chapter')</span>
        <div class="content">
            <h4 class="entity-list-item-name break-text">{{ $chapter->name }}</h4>
            <div class="entity-item-snippet">
                <p class="text-muted break-text">{{ $chapter->getExcerpt() }}</p>
            </div>
        </div>
    </span>
@endif
@if ($chapter->visible_pages->count() > 0)
    <div class="chapter chapter-expansion">
        <span class="icon text-chapter">@icon('page')</span>
        <div component="chapter-contents" class="content">
            <button type="button"
                    refs="chapter-contents@toggle"
                    aria-expanded="false"
                    class="text-muted chapter-contents-toggle">@icon('caret-right') <span>{{ trans_choice('entities.x_pages', $chapter->visible_pages->count()) }}</span></button>
            <div refs="chapter-contents@list" class="inset-list chapter-contents-list">
                <div class="entity-list-item-children">
                    @include('shared.entities.list', [
                        'entities' => $chapter->visible_pages,
                        'shareLink' => $shareLink,
                        'token' => $token
                    ])
                </div>
            </div>
        </div>
    </div>
@endif
