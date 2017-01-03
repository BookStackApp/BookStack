<div class="breadcrumbs">
    <a href="{{ $page->book->getUrl() }}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $page->book->getShortName() }}</a>
    @if($page->hasChapter())
        <span class="sep">&raquo;</span>
        <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
            <i class="zmdi zmdi-collection-bookmark"></i>
            {{ $page->chapter->getShortName() }}
        </a>
    @endif
    <span class="sep">&raquo;</span>
    <a href="{{ $page->getUrl() }}" class="text-page text-button"><i class="zmdi zmdi-file"></i>{{ $page->getShortName() }}</a>
</div>