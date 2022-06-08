<div class="page-break"></div>

@if (isset($chapter))
    <div class="chapter-hint">{{$chapter->name}}</div>
@endif

<h1 id="page-{{$page->id}}">{{ $page->name }}</h1>
{!! $page->html !!}