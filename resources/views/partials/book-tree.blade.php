<nav id="book-tree"
     class="book-tree mb-xl"
     aria-label="{{ trans('entities.books_navigation') }}">

    <h5>{{ trans('entities.books_navigation') }}</h5>

    <ul class="sidebar-page-list mt-xs menu entity-list">
        @if (userCan('view', $book))
            <li class="list-item-book book">
                @include('partials.entity-list-item-basic', ['entity' => $book, 'classes' => ($current->matches($book)? 'selected' : '')])
            </li>
        @endif

        @foreach($sidebarTree as $bookChild)
            <li class="list-item-{{ $bookChild->getClassName() }} {{ $bookChild->getClassName() }} {{ $bookChild->isA('page') && $bookChild->draft ? 'draft' : '' }}">
                @include('partials.entity-list-item-basic', ['entity' => $bookChild, 'classes' => $current->matches($bookChild)? 'selected' : ''])

                @if($bookChild->isA('chapter') && count($bookChild->pages) > 0)
                    <div class="entity-list-item no-hover">
                        <span role="presentation" class="icon text-chapter"></span>
                        <div class="content">
                            @include('chapters.child-menu', [
                                'chapter' => $bookChild,
                                'current' => $current,
                                'isOpen'  => $bookChild->matchesOrContains($current)
                            ])
                        </div>
                    </div>

                @endif

            </li>
        @endforeach
    </ul>
</nav>