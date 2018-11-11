<div class="breadcrumbs">
    @if (isset($book) && userCan('view', $book))
        <a href="{{ $book->getUrl() }}" class="entity-chip text-book">
            @icon('book'){{ $book->getShortName() }}
        </a>
        <div class="separator">@icon('chevron-right')</div>
    @endif
    @if(isset($chapter) && userCan('view', $chapter))
        <a href="{{ $chapter->getUrl() }}" class="entity-chip text-chapter">
            @icon('chapter'){{ $chapter->getShortName() }}
        </a>
        <div class="separator">@icon('chevron-right')</div>
    @endif
    @if(isset($page) && userCan('view', $page))
        <a href="{{ $page->getUrl() }}" class="entity-chip text-page">
            @icon('page'){{ $page->getShortName() }}
        </a>
    @endif
</div>