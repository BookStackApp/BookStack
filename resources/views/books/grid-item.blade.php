<a href="{{$book->getUrl()}}" class="grid-card"  data-entity-type="book" data-entity-id="{{$book->id}}">
    <div class="featured-image-container bg-book">
        <img src="{{$book->getBookCover()}}" alt="{{$book->name}}">
    </div>
    <div class="grid-card-content">
        <h2>{{$book->getShortName(35)}}</h2>
        @if(isset($book->searchSnippet))
            <p class="text-muted">{!! $book->searchSnippet !!}</p>
        @else
            <p class="text-muted">{{ $book->getExcerpt(130) }}</p>
        @endif
    </div>
    <div class="grid-card-footer text-muted text-small">
        @icon('star')<span title="{{$book->created_at->toDayDateTimeString()}}">{{ trans('entities.meta_created', ['timeLength' => $book->created_at->diffForHumans()]) }}</span>
        <br>
        @icon('edit')<span title="{{ $book->updated_at->toDayDateTimeString() }}">{{ trans('entities.meta_updated', ['timeLength' => $book->updated_at->diffForHumans()]) }}</span>
    </div>
</a>