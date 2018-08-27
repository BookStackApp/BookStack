<div class="bookshelf-grid-item grid-card"  data-entity-type="bookshelf" data-entity-id="{{$bookshelf->id}}">
    <div class="featured-image-container">
        <a href="{{$bookshelf->getUrl()}}" title="{{$bookshelf->name}}">
            <img src="{{$bookshelf->getBookCover()}}" alt="{{$bookshelf->name}}">
        </a>
    </div>
    <div class="grid-card-content">
        <h2><a class="break-text" href="{{$bookshelf->getUrl()}}" title="{{$bookshelf->name}}">{{$bookshelf->getShortName(35)}}</a></h2>
        @if(isset($bookshelf->searchSnippet))
            <p >{!! $bookshelf->searchSnippet !!}</p>
        @else
            <p >{{ $bookshelf->getExcerpt(130) }}</p>
        @endif
    </div>
    <div class="grid-card-footer text-muted text-small">
        <span>@include('partials.entity-meta', ['entity' => $bookshelf])</span>
    </div>
</div>