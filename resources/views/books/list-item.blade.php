<div class="book entity-list-item"  data-entity-type="book" data-entity-id="{{$book->id}}">
    <h4 class="text-book"><a class="text-book entity-list-item-link" href="{{$book->getUrl()}}">@icon('book')<span class="entity-list-item-name break-text">{{$book->name}}</span></a></h4>
    <div class="entity-item-snippet">
        @if(isset($book->searchSnippet))
            <p class="text-muted break-text">{!! $book->searchSnippet !!}</p>
        @else
            <p class="text-muted break-text">{{ $book->getExcerpt() }}</p>
        @endif
    </div>
</div>