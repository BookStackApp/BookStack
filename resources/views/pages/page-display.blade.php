<h1>{{$page->name}}</h1>
@if(count($page->children) > 0)
    <h4 class="text-muted">Sub-pages</h4>
    <div class="page-list">
        @foreach($page->children as $childPage)
            <a href="{{ $childPage->getUrl() }}">{{ $childPage->name }}</a>
        @endforeach
    </div>
@endif
{!! $page->html !!}