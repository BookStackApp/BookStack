<div class="breadcrumbs">
    @if (userCan('view', $chapter->book))
    <a href="{{ $chapter->book->getUrl() }}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $chapter->book->getShortName() }}</a>
    <span class="sep">&raquo;</span>
    @endif
    <a href="{{ $chapter->getUrl() }}" class="text-chapter text-button"><i class="zmdi zmdi-collection-bookmark"></i>{{$chapter->getShortName()}}</a>
</div>