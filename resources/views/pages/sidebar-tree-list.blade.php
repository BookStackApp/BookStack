
<ul class="sidebar-page-list menu">
    <li class="book-header"><a href="{{$book->getUrl()}}" class="book"><i class="zmdi zmdi-book"></i>{{$book->name}}</a></li>
    @foreach($book->children() as $bookChild)
        <li>
            <a href="{{$bookChild->getUrl()}}" class="@if(is_a($bookChild, 'Oxbow\Chapter')) chapter @else page @endif">
                @if(is_a($bookChild, 'Oxbow\Chapter'))
                    <i class="zmdi zmdi-collection-bookmark chapter-toggle"></i>
                @else
                    <i class="zmdi zmdi-file-text"></i>
                @endif
                {{ $bookChild->name }}
            </a>

            @if(is_a($bookChild, 'Oxbow\Chapter') && count($bookChild->pages) > 0)
                <ul class="menu">
                    @foreach($bookChild->pages as $page)
                        <li>
                            <a href="{{$page->getUrl()}}" class="@if(is_a($page, 'Oxbow\Chapter')) chapter @else page @endif">
                                @if(is_a($page, 'Oxbow\Chapter'))
                                    <i class="zmdi zmdi-collection-bookmark chapter-toggle"></i>
                                @else
                                    <i class="zmdi zmdi-file-text"></i>
                                @endif
                                {{ $page->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>