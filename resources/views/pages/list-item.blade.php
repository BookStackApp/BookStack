<div class="page">
    <h3>
        <a href="{{ $page->getUrl() }}" class="text-page"><i class="zmdi zmdi-file-text"></i>{{ $page->name }}</a>
    </h3>

    @if(isset($showMeta) && $showMeta)
        <div class="meta">
            <span class="text-book"><i class="zmdi zmdi-book"></i> {{ $page->book->name }}</span>
            @if($page->chapter)
                <span class="text-chapter"><i class="zmdi zmdi-collection-bookmark"></i> {{ $page->chapter->name }}</span>
            @endif
         </div>
    @endif

    @if(isset($page->searchSnippet))
        <p class="text-muted">{!! $page->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $page->getExcerpt() }}</p>
    @endif
</div>