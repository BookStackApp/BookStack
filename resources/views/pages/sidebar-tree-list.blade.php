{{--Requires an array of pages to be passed as $pageTree--}}

<ul class="sidebar-page-list">
    @foreach($book->children() as $bookChild)
        <li>
            {{ $bookChild->name }}
            @if(is_a($bookChild, 'Oxbow\Chapter') && count($bookChild->pages) > 0)
                <ul>
                    @foreach($pages as $page)
                        <li>{{ $page->name }}</li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>