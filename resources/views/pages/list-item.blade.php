<div class="page">
    <h3>
        <a href="{{ $page->getUrl() }}" class="text-page"><i class="zmdi zmdi-file-text"></i>{{ $page->name }}</a>
    </h3>

    @if(isset($page->searchSnippet))
        <p class="text-muted">{!! $page->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $page->getExcerpt() }}</p>
    @endif

    @if(isset($style) && $style === 'detailed')
        <div class="row meta text-muted text-small">
            <div class="col-md-4">
                Created {{$page->created_at->diffForHumans()}} @if($page->createdBy)by {{$page->createdBy->name}}@endif <br>
                Last updated {{ $page->updated_at->diffForHumans() }} @if($page->updatedBy)by {{$page->updatedBy->name}} @endif
            </div>
            <div class="col-md-8">
                <a class="text-book" href="{{ $page->book->getUrl() }}"><i class="zmdi zmdi-book"></i>{{ $page->book->getShortName(30) }}</a>
                <br>
                @if($page->chapter)
                    <a class="text-chapter" href="{{ $page->chapter->getUrl() }}"><i class="zmdi zmdi-collection-bookmark"></i>{{ $page->chapter->getShortName(30) }}</a>
                @else
                    <i class="zmdi zmdi-collection-bookmark"></i> Page is not in a chapter
                @endif
            </div>
        </div>
    @endif


</div>