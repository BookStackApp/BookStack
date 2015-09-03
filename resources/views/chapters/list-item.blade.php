<div class="chapter">
    <h3>
        <a href="{{ $chapter->getUrl() }}" class="text-chapter">
            <i class="zmdi zmdi-collection-bookmark"></i>{{ $chapter->name }}
        </a>
    </h3>
    @if(isset($chapter->searchSnippet))
        <p class="text-muted">{!! $chapter->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $chapter->getExcerpt() }}</p>
    @endif

    @if(count($chapter->pages) > 0 && !isset($hidePages))
        <p class="text-muted chapter-toggle open"><i class="zmdi zmdi-caret-right"></i> {{ count($chapter->pages) }} Pages</p>
        <div class="inset-list">
            @foreach($chapter->pages as $page)
                <h4><a href="{{$page->getUrl()}}"><i class="zmdi zmdi-file-text"></i>{{$page->name}}</a></h4>
            @endforeach
        </div>
    @endif
</div>