<div class="shelf entity-list-item"  data-entity-type="bookshelf" data-entity-id="{{$bookshelf->id}}">
    <h4 class="text-shelf"><a class="text-bookshelf entity-list-item-link" href="{{$bookshelf->getUrl()}}">@icon('bookshelf')<span class="entity-list-item-name break-text">{{$bookshelf->name}}</span></a></h4>
    <div class="entity-item-snippet">
        @if(isset($bookshelf->searchSnippet))
            <p class="text-muted break-text">{!! $bookshelf->searchSnippet !!}</p>
        @else
            <p class="text-muted break-text">{{ $bookshelf->getExcerpt() }}</p>
        @endif
    </div>
</div>