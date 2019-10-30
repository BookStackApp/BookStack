<a href="{{ $book->getUrl() }}" class="book entity-list-item" data-entity-type="book" data-entity-id="{{$book->id}}">
    <div class="entity-list-item-image bg-book" style="background-image: url('{{ $book->getBookCover() }}')">
        @icon('book')
    </div>
    <div class="content">
        <h4 class="entity-list-item-name break-text">{{ $book->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-s">{{ $book->getExcerpt() }}</p>
        </div>
    </div>
</a>

@if (setting()->get('app-show-pages-in-shelf-view'))
    <div class="entity-shelf-books grid third gap-y-xs entity-list-item-children">
        @foreach((new BookStack\Entities\Managers\BookContents($book))->getTree(true) as $bookChild)
            <div>
                @if ($bookChild->isA('chapter'))
                    <a href="{{$bookChild->getUrl()}}" class="entity-chip text-book" style="color: var(--color-chapter)">
                        @icon('chapter')
                        @elseif ($bookChild->draft)
                            <a href="{{$bookChild->getUrl()}}" class="entity-chip text-book" style="color: var(--color-page-draft)">
                                @icon('edit')
                                @else
                                    <a href="{{$bookChild->getUrl()}}" class="entity-chip text-book" style="color: var(--color-page)">
                                        @icon('page')
                                        @endif
                                        {{ $bookChild->name }}
                                    </a>
            </div>
        @endforeach
    </div>
@endif