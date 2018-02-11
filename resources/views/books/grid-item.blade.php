<div class="book-grid-item grid-card"  data-entity-type="book" data-entity-id="{{$book->id}}">
    <div class="featured-image-container">
        <a href="{{$book->getUrl()}}" title="{{$book->name}}">
            <img src="{{$book->getBookCover()}}" alt="{{$book->name}}">
        </a>
    </div>
    <div class="grid-card-content">
        <h2><a href="{{$book->getUrl()}}" title="{{$book->name}}">{{$book->getShortName(35)}}</a></h2>
        @if(isset($book->searchSnippet))
            <p >{!! $book->searchSnippet !!}</p>
        @else
            <p >{{ $book->getExcerpt(130) }}</p>
        @endif
    </div>
    <div class="grid-card-footer">
        <span>@include('partials.entity-meta', ['entity' => $book])</span>
    </div>
</div>