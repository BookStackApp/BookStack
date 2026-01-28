<nav id="book-tree"
     class="book-tree mb-xl"
     aria-label="{{ trans('entities.books_navigation') }}">

    <h5>{{ trans('entities.books_navigation') }}</h5>

    <ul class="sidebar-page-list mt-xs menu entity-list">
        @if($shareToken)
            @php
                $isBookShareLink = $shareLink && $shareLink->entity instanceof \BookStack\Entities\Models\Book;
            @endphp
            @if($isBookShareLink)
                <li class="list-item-book book">
                    @if($current->matches($book))
                        <span class="book selected entity-list-item">
                            <span role="presentation" class="icon text-book">@icon('book')</span>
                            <div class="content">
                                <h4 class="entity-list-item-name break-text">{{ $book->name }}</h4>
                            </div>
                        </span>
                    @else
                        <a href="{{ url('/share/' . $shareToken) }}" class="book entity-list-item">
                            <span role="presentation" class="icon text-book">@icon('book')</span>
                            <div class="content">
                                <h4 class="entity-list-item-name break-text">{{ $book->name }}</h4>
                            </div>
                        </a>
                    @endif
                </li>
            @else
                <li class="list-item-book book">
                    <span class="book {{ $current->matches($book) ? 'selected' : '' }} entity-list-item">
                        <span role="presentation" class="icon text-book">@icon('book')</span>
                        <div class="content">
                            <h4 class="entity-list-item-name break-text">{{ $book->name }}</h4>
                        </div>
                    </span>
                </li>
            @endif
        @else
            @if (userCan(\BookStack\Permissions\Permission::BookView, $book))
                <li class="list-item-book book">
                    @include('entities.list-item-basic', ['entity' => $book, 'classes' => ($current->matches($book)? 'selected' : '')])
                </li>
            @endif
        @endif

        @foreach($sidebarTree as $bookChild)
            <li class="list-item-{{ $bookChild->getType() }} {{ $bookChild->getType() }} {{ $bookChild->isA('page') && $bookChild->draft ? 'draft' : '' }}">
                @if($shareToken)
                    @include('shared.list-item-basic', [
                        'entity' => $bookChild, 
                        'classes' => $current->matches($bookChild)? 'selected' : '', 
                        'shareToken' => $shareToken,
                        'shareLink' => $shareLink ?? null,
                        'currentEntityId' => $current->id
                    ])
                @else
                    @include('entities.list-item-basic', ['entity' => $bookChild, 'classes' => $current->matches($bookChild)? 'selected' : ''])
                @endif

                @if($bookChild->isA('chapter') && count($bookChild->visible_pages) > 0)
                    <div class="entity-list-item no-hover">
                        <span role="presentation" class="icon text-chapter"></span>
                        <div class="content">
                            @include('chapters.parts.child-menu', [
                                'chapter' => $bookChild,
                                'current' => $current,
                                'isOpen'  => $bookChild->matchesOrContains($current),
                                'shareToken' => $shareToken ?? null,
                                'shareLink' => $shareLink ?? null
                            ])
                        </div>
                    </div>
                @endif

            </li>
        @endforeach
    </ul>
</nav>
