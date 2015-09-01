
<div class="page-list">
    @if(count($pages) > 0)
        @foreach($pages as $page)
            <div class="book-child anim searchResult">
                <h3>
                    <a href="{{$page->getUrl() . '#' . $searchTerm}}" class="page">
                        <i class="zmdi zmdi-file-text"></i>{{$page->name}}
                    </a>
                </h3>

                <p class="text-muted">
                    {!! $page->searchSnippet !!}
                </p>
                <hr>
            </div>
        @endforeach
    @else
        <p class="text-muted">No pages matched this search</p>
    @endif
</div>

@if(count($chapters) > 0)
    <div class="page-list">
        @foreach($chapters as $chapter)
            <div class="book-child anim searchResult">
                <h3>
                    <a href="{{$chapter->getUrl()}}" class="text-chapter">
                        <i class="zmdi zmdi-collection-bookmark"></i>{{$chapter->name}}
                    </a>
                </h3>

                <p class="text-muted">
                    {!! $chapter->searchSnippet !!}
                </p>
                <hr>
            </div>
        @endforeach
    </div>
@endif

