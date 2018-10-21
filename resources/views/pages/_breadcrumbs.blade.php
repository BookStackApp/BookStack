<div class="breadcrumbs">
    @if (userCan('view', $page->book))
        <a href="{{ $page->book->getUrl() }}" class="entity-chip text-book">@icon('book'){{ $page->book->getShortName() }}</a>
        <div class="separator">@icon('chevron-right')</div>
    @endif
    @if($page->hasChapter() && userCan('view', $page->chapter))
        <a href="{{ $page->chapter->getUrl() }}" class="entity-chip text-chapter">
            @icon('chapter')
            {{ $page->chapter->getShortName() }}
        </a>
        <div class="separator">@icon('chevron-right')</div>
    @endif
    <a href="{{ $page->getUrl() }}" class="entity-chip text-page">@icon('page'){{ $page->getShortName() }}</a>
</div>