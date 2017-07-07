<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3"  data-entity-type="book" data-entity-id="{{$book->id}}">
    <div class="galleryItem">
    <h3>
        <a class="text-book entity-list-item-link" href="{{$book->getUrl()}}"><i class="zmdi zmdi-book"></i><span class="entity-list-item-name">{{$book->name}}</span>
        <br>
        <img src="{{$book->getBookCover(192)}}" alt="{{$book->name}}">
        </a>
    </h3>
    @if(isset($book->searchSnippet))
        <p class="text-muted">{!! $book->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $book->getExcerpt() }}</p>
    @endif
</div>
</div>