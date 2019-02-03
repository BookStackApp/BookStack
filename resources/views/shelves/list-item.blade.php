<a href="{{ $shelf->getUrl() }}" class="shelf entity-list-item" data-entity-type="bookshelf" data-entity-id="{{$shelf->id}}">
    <div class="entity-list-item-image bg-shelf" style="background-image: url('{{ $shelf->getBookCover() }}')">
    </div>
    <div class="content">
        <h4 class="entity-list-item-name break-text">{{ $shelf->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-s">{{ $shelf->getExcerpt() }}</p>
        </div>
    </div>
</a>