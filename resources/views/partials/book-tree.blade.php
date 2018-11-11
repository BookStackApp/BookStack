<div id="book-tree" class="card book-tree mb-m" v-pre>
    @if (userCan('view', $book))
        @include('partials.entity-list-item-basic', ['entity' => $book, 'classes' => ($current->matches($book)? 'selected' : '')])
    @else
        <h3>@icon('book') {{ trans('entities.books_navigation') }}</h3>
    @endif

    <ul class="sidebar-page-list menu">

        @foreach($sidebarTree as $bookChild)
            <li class="list-item-{{ $bookChild->getClassName() }} {{ $bookChild->getClassName() }} {{ $bookChild->isA('page') && $bookChild->draft ? 'draft' : '' }}">
                @include('partials.entity-list-item-basic', ['entity' => $bookChild, 'classes' => $current->matches($bookChild)? 'selected' : ''])

                @if($bookChild->isA('chapter') && count($bookChild->pages) > 0)
                    @include('chapters.child-menu', ['chapter' => $bookChild, 'current' => $current])
                @endif

            </li>
        @endforeach
    </ul>
</div>