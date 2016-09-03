<div class="book entity-list-item"  data-entity-type="book" data-entity-id="{{$book->id}}">
    <h3 class="text-book"><a class="text-book entity-list-item-link" href="{{$book->getUrl()}}"><i class="zmdi zmdi-book"></i><span class="entity-list-item-name">{{$book->name}}</span></a></h3>
    @if(isset($book->searchSnippet))
        <p class="text-muted">{!! $book->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $book->getExcerpt() }}</p>
    @endif
</div>