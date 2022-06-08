<div class="page-break"></div>
<h1 id="chapter-{{$chapter->id}}">{{ $chapter->name }}</h1>

<p>{{ $chapter->description }}</p>

@if(count($chapter->visible_pages) > 0)
    @foreach($chapter->visible_pages as $page)
        @include('exports.parts.page-item', ['page' => $page, 'chapter' => $chapter])
    @endforeach
@endif