<div id="book-tree" class="book-tree mb-xl" v-pre>
    <h5>{{ trans('entities.books_navigation') }}</h5>
    @if (userCan('view', $book))
        <div class="entity-list">
            @include('partials.entity-list-item-basic', ['entity' => $book, 'classes' => ($current->matches($book)? 'selected' : '')])
        </div>
    @endif

    <ul class="sidebar-page-list menu entity-list">

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