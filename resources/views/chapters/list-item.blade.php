<a href="{{ $chapter->getUrl() }}" class="chapter entity-list-item @if($chapter->hasChildren()) has-children @endif" data-entity-type="chapter" data-entity-id="{{$chapter->id}}">
    <span class="icon text-chapter">@icon('chapter')</span>
    <div class="content">
        <h4 class="entity-list-item-name break-text">{{ $chapter->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-s">{{ $chapter->getExcerpt() }}</p>
        </div>
    </div>
</a>
@if ($chapter->hasChildren())
    <div class="chapter chapter-expansion">
        <span class="icon text-chapter">@icon('page')</span>
        <div class="content">
            <button type="button" chapter-toggle
                    aria-expanded="false"
                    class="text-muted chapter-expansion-toggle">@icon('caret-right') <span>{{ trans_choice('entities.x_pages', $chapter->pages->count()) }}</span></button>
            <div class="inset-list">
                <div class="entity-list-item-children">
                    @include('partials.entity-list', ['entities' => $chapter->pages])
                </div>
            </div>
        </div>
    </div>
@endif