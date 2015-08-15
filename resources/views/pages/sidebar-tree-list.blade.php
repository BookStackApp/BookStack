
<ul class="sidebar-page-list menu">
    <li class="book-header"><a href="{{$book->getUrl()}}" class="book"><i class="zmdi zmdi-book"></i>{{$book->name}}</a></li>
    @foreach($book->children() as $bookChild)
        <li class="list-item-{{is_a($bookChild, 'Oxbow\Chapter') ? 'chapter' : 'page' }}">
            <a href="{{$bookChild->getUrl()}}" class="{{is_a($bookChild, 'Oxbow\Chapter') ? 'chapter' : 'page' }}">
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
                        <li class="list-item-page">
                            <a href="{{$page->getUrl()}}" class="page">
                                <i class="zmdi zmdi-file-text"></i> {{ $page->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endforeach
</ul>