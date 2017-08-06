
<div class="book-tree" ng-non-bindable>

    @if(isset($page) && $page->tags->count() > 0)
        <div class="tag-display">
            <h6 class="text-muted">{{ trans('entities.page_tags') }}</h6>
            <table>
                <tbody>
                @foreach($page->tags as $tag)
                    <tr class="tag">
                        <td @if(!$tag->value) colspan="2" @endif><a href="{{ baseUrl('/search?term=%5B' . urlencode($tag->name) .'%5D') }}">{{ $tag->name }}</a></td>
                        @if($tag->value) <td class="tag-value"><a href="{{ baseUrl('/search?term=%5B' . urlencode($tag->name) .'%3D' . urlencode($tag->value) . '%5D') }}">{{$tag->value}}</a></td> @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if (isset($page) && $page->attachments->count() > 0)
        <h6 class="text-muted">{{ trans('entities.pages_attachments') }}</h6>
        @foreach($page->attachments as $attachment)
            <div class="attachment">
                <a href="{{ $attachment->getUrl() }}" @if($attachment->external) target="_blank" @endif><i class="zmdi zmdi-{{ $attachment->external ? 'open-in-new' : 'file' }}"></i>{{ $attachment->name }}</a>
            </div>
        @endforeach
    @endif

    @if (isset($pageNav) && count($pageNav))
        <h6 class="text-muted">{{ trans('entities.pages_navigation') }}</h6>
        <div class="sidebar-page-nav menu">
            @foreach($pageNav as $navItem)
                <li class="page-nav-item h{{ $navItem['level'] }}">
                    <a href="{{ $navItem['link'] }}">{{ $navItem['text'] }}</a>
                </li>
            @endforeach
        </div>
    @endif

    <h6 class="text-muted">{{ trans('entities.books_navigation') }}</h6>
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
