<div class="page">
    <h3>
        <a href="{{ $page->getUrl() }}" class="text-page"><i class="zmdi zmdi-file-text"></i>{{ $page->name }}</a>
    </h3>
    @if(isset($page->searchSnippet))
        <p class="text-muted">{!! $page->searchSnippet !!}</p>
    @else
        <p class="text-muted">{{ $page->getExcerpt() }}</p>
    @endif
</div>