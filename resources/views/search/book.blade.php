<div class="page-list">
    @if(count($pages) > 0)
        @foreach($pages as $pageIndex => $page)
            <div class="anim searchResult" style="animation-delay: {{$pageIndex*50 . 'ms'}};">
                @include('pages.list-item', ['page' => $page])
                <hr>
            </div>
        @endforeach
    @else
        <p class="text-muted">{{ trans('entities.search_no_pages') }}</p>
    @endif
</div>

@if(count($chapters) > 0)
    <div class="page-list">
        @foreach($chapters as $chapterIndex => $chapter)
            <div class="anim searchResult" style="animation-delay: {{($chapterIndex+count($pages))*50 . 'ms'}};">
                @include('chapters.list-item', ['chapter' => $chapter, 'hidePages' => true])
                <hr>
            </div>
        @endforeach
    </div>
@endif

