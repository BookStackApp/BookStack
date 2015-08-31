
<div class="book-tree">
    <h6 class="text-muted">Book Navigation</h6>
    <ul class="sidebar-page-list menu">
        <li class="book-header"><a href="{{$book->getUrl()}}" class="book {{ $current->matches($book)? 'selected' : '' }}"><i class="zmdi zmdi-book"></i>{{$book->name}}</a></li>
        @foreach($book->children() as $bookChild)
            <li class="list-item-{{ $bookChild->getName() }}">
                <a href="{{$bookChild->getUrl()}}" class="{{ $bookChild->getName() }} {{ $current->matches($bookChild)? 'selected' : '' }}">
                    @if($bookChild->isA('chapter'))<i class="zmdi zmdi-collection-bookmark chapter-toggle"></i>@else <i class="zmdi zmdi-file-text"></i>@endif{{ $bookChild->name }}
                </a>

                @if($bookChild->isA('chapter') && count($bookChild->pages) > 0)
                    <ul class="menu">
                        @foreach($bookChild->pages as $childPage)
                            <li class="list-item-page">
                                <a href="{{$childPage->getUrl()}}" class="page {{ $current->matches($childPage)? 'selected' : '' }}">
                                    <i class="zmdi zmdi-file-text"></i> {{ $childPage->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>
