<a href="{{ $shelf->getUrl() }}" class="shelf entity-list-item" data-entity-type="bookshelf" data-entity-id="{{$shelf->id}}">
    <div class="entity-list-item-image bg-bookshelf @if($shelf->coverInfo()->exists()) has-image @endif" style="background-image: url('{{ $shelf->coverInfo()->getUrl() }}')">
        @icon('bookshelf')
    </div>
    <div class="content py-xs">
        <h4 class="entity-list-item-name break-text">{{ $shelf->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-none">{{ $shelf->getExcerpt() }}</p>
        </div>
    </div>
</a>
<div class="entity-shelf-books grid third gap-y-xs entity-list-item-children">
    @foreach($shelf->visibleBooks as $book)
        <div>
            <a href="{{ $book->getUrl('?shelf=' . $shelf->id) }}" class="entity-chip text-book">
                @icon('book')
                {{ $book->name }}
            </a>
        </div>
    @endforeach
</div>