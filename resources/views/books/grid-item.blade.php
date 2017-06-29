<div class="galleryContainer"  data-entity-type="book" data-entity-id="{{$book->id}}">
<div class="col-sm-3 galleryItem">
    <h3>
        <a class="text-book entity-list-item-link" href="{{$book->getUrl()}}"><i class="zmdi zmdi-book"></i><span class="entity-list-item-name">{{$book->name}}</span>
        <br>
        <img @if($book->image === NULL) src="{{baseUrl('/default.jpg')}}" @else src="{{$book->image}}" @endif alt="{{$book->name}}">
        </a>
    </h3>
    @if(isset($book->searchSnippet))
        <p class="text-muted">{!! $book->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $book->getExcerpt() }}</p>
    @endif
    </div>
</div>