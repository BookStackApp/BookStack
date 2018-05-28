<div class="sort-box" data-type="book" data-id="{{ $book->id }}">
    <h3 class="text-book">@icon('book'){{ $book->name }}</h3>
    <ul class="sortable-page-list sort-list">
        @foreach($bookChildren as $bookChild)
            <li data-id="{{$bookChild->id}}" data-type="{{ $bookChild->getClassName() }}" class="text-{{ $bookChild->getClassName() }}">
                @icon($bookChild->isA('chapter') ? 'chapter' : 'page'){{ $bookChild->name }}
                @if($bookChild->isA('chapter'))
                    <ul>
                        @foreach($bookChild->pages as $page)
                            <li data-id="{{$page->id}}" class="text-page" data-type="page">
                                @icon('page')
                                {{ $page->name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>