<div class="card book-tree" ng-non-bindable>
    <h3><i class="zmdi zmdi-book"></i> {{ trans('entities.books_navigation') }}</h3>
    <div class="body">
        <ul class="sidebar-page-list menu">

            @if (userCan('view', $book))
                <li class="book-header"><a href="{{ $book->getUrl() }}" class="book {{ $current->matches($book)? 'selected' : '' }}"><i class="zmdi zmdi-book"></i>{{$book->name}}</a></li>
            @endif

            @foreach($sidebarTree as $bookChild)
                <li class="list-item-{{ $bookChild->getClassName() }} {{ $bookChild->getClassName() }} {{ $bookChild->isA('page') && $bookChild->draft ? 'draft' : '' }}">
                    <a href="{{ $bookChild->getUrl() }}" class="{{ $bookChild->getClassName() }} {{ $current->matches($bookChild)? 'selected' : '' }}">
                        @if($bookChild->isA('chapter'))<i class="zmdi zmdi-collection-bookmark"></i>@else <i class="zmdi zmdi-file-text"></i>@endif{{ $bookChild->name }}
                    </a>

                    @if($bookChild->isA('chapter') && count($bookChild->pages) > 0)
                        <p chapter-toggle class="text-muted @if($bookChild->matchesOrContains($current)) open @endif">
                            <i class="zmdi zmdi-caret-right"></i> <i class="zmdi zmdi-file-text"></i> <span>{{ trans('entities.x_pages', ['count' => $bookChild->pages->count()]) }}</span>
                        </p>
                        <ul class="menu sub-menu inset-list @if($bookChild->matchesOrContains($current)) open @endif">
                            @foreach($bookChild->pages as $childPage)
                                <li class="list-item-page {{ $childPage->isA('page') && $childPage->draft ? 'draft' : '' }}">
                                    <a href="{{ $childPage->getUrl() }}" class="page {{ $current->matches($childPage)? 'selected' : '' }}">
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
</div>