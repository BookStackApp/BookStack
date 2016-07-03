<div class="book entity-list-item"  data-entity-type="book" data-entity-id="{{$book->id}}">
    <h3 class="text-book"><a class="text-book" href="{{$book->getUrl()}}"><i class="zmdi zmdi-book"></i>{{$book->name}}</a></h3>
    @if(isset($book->searchSnippet))
        <p class="text-muted">{!! $book->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $book->getExcerpt() }}</p>
    @endif
</div>