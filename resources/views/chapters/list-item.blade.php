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

    @if(!isset($hidePages) && count($chapter->pages) > 0)
        <p class="text-muted chapter-toggle"><i class="zmdi zmdi-caret-right"></i> <i class="zmdi zmdi-file-text"></i> <span>{{ count($chapter->pages) }} Pages</span></p>
        <div class="inset-list">
            @foreach($chapter->pages as $page)
                <h4 class="@if($page->draft) draft @endif"><a href="{{$page->getUrl()}}" class="text-page @if($page->draft) draft @endif"><i class="zmdi zmdi-file-text"></i>{{$page->name}}</a></h4>
            @endforeach
        </div>
    @endif
</div>