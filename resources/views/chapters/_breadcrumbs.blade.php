<div class="breadcrumbs">
    @if (userCan('view', $chapter->book))
    <a href="{{ $chapter->book->getUrl() }}" class="text-book text-button">@icon('book'){{ $chapter->book->getShortName() }}</a>
    <span class="sep">&raquo;</span>
    @endif
    <a href="{{ $chapter->getUrl() }}" class="text-chapter text-button">@icon('chapter'){{$chapter->getShortName()}}</a>
</div>