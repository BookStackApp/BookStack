<div class="sort-box" data-type="book" data-id="{{ $book->id }}" ng-non-bindable>
    <h3 class="text-book"><i class="zmdi zmdi-book"></i>{{ $book->name }}</h3>
    <ul class="sortable-page-list sort-list">
        @foreach($bookChildren as $bookChild)
            <li data-id="{{$bookChild->id}}" data-type="{{ $bookChild->getClassName() }}" class="text-{{ $bookChild->getClassName() }}">
                <i class="zmdi {{ $bookChild->isA('chapter') ? 'zmdi-collection-bookmark':'zmdi-file-text'}}"></i>{{ $bookChild->name }}
                @if($bookChild->isA('chapter'))
                    <ul>
                        @foreach($bookChild->pages as $page)
                            <li data-id="{{$page->id}}" class="text-page" data-type="page">
                                <i class="zmdi zmdi-file-text"></i>
                                {{ $page->name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    </ul>
</div>