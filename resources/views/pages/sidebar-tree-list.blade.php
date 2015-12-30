
<div class="book-tree" ng-non-bindable>
    <h6 class="text-muted">Book Navigation</h6>
    <ul class="sidebar-page-list menu">
        <li class="book-header"><a href="{{$book->getUrl()}}" class="book {{ $current->matches($book)? 'selected' : '' }}"><i class="zmdi zmdi-book"></i>{{$book->name}}</a></li>


        @foreach($sidebarTree as $bookChild)
            <li class="list-item-{{ $bookChild->getClassName() }} {{ $bookChild->getClassName() }}">
                <a href="{{$bookChild->getUrl()}}" class="{{ $bookChild->getClassName() }} {{ $current->matches($bookChild)? 'selected' : '' }}">
                    @if($bookChild->isA('chapter'))<i class="zmdi zmdi-collection-bookmark"></i>@else <i class="zmdi zmdi-file-text"></i>@endif{{ $bookChild->name }}
                </a>

                @if($bookChild->isA('chapter') && count($bookChild->pages) > 0)
                    <p class="text-muted chapter-toggle @if($bookChild->matchesOrContains($current)) open @endif">
                        <i class="zmdi zmdi-caret-right"></i> <i class="zmdi zmdi-file-text"></i> <span>{{ count($bookChild->pages) }} Pages</span>
                    </p>
                    <ul class="menu sub-menu inset-list @if($bookChild->matchesOrContains($current)) open @endif">
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
