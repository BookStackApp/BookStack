<div class="breadcrumbs">
    @if (userCan('view', $page->book))
        <a href="{{ $page->book->getUrl() }}" class="text-book text-button">@icon('book'){{ $page->book->getShortName() }}</a>
        <span class="sep">&raquo;</span>
    @endif
    @if($page->hasChapter() && userCan('view', $page->chapter))
        <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
            @icon('chapter')
            {{ $page->chapter->getShortName() }}
        </a>
        <span class="sep">&raquo;</span>
    @endif
    <a href="{{ $page->getUrl() }}" class="text-page text-button"><i class="zmdi zmdi-file"></i>{{ $page->getShortName() }}</a>
</div>