
<div class="content-wrap card {{ $booksViewType === 'list' ? 'thin' : '' }}">
    <div class="grid halves v-center">
        <h1 class="list-heading">{{ trans('entities.books') }}</h1>
        <div class="text-right">

            <div class="list-sort-container">
                <div class="list-sort-label">Sort</div>
                <div class="list-sort">
                    <div class="list-sort-type dropdown-container" dropdown>
                        <div dropdown-toggle>Name</div>
                        <ul>
                            <li><a href="#">Name</a></li>
                            <li><a href="#">Created Date</a></li>
                            <li><a href="#">Popularity</a></li>
                        </ul>
                    </div>
                    <div class="list-sort-dir">
                        @icon('sort-up')
                    </div>
                </div>
            </div>

        </div>
    </div>
    @if(count($books) > 0)
        @if($booksViewType === 'list')
            <div class="entity-list">
                @foreach($books as $book)
                    <a href="{{ $book->getUrl() }}" class="book entity-list-item" data-entity-type="book" data-entity-id="{{$book->id}}">
                        <div class="entity-list-item-image bg-book" style="background-image: url('{{ $book->getBookCover() }}')">
                        </div>
                        <div class="content">
                            <h4 class="entity-list-item-name break-text">{{ $book->name }}</h4>
                            <div class="entity-item-snippet">
                                <p class="text-muted break-text">{{ $book->getExcerpt() }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
                {!! $books->render() !!}
            </div>
        @else
             <div class="grid third">
                @foreach($books as $key => $book)
                    @include('books.grid-item', ['book' => $book])
                @endforeach
             </div>
            <div>
                {!! $books->render() !!}
            </div>
        @endif
    @else
        <p class="text-muted">{{ trans('entities.books_empty') }}</p>
        @if(userCan('books-create-all'))
            <a href="{{ baseUrl("/create-book") }}" class="text-pos">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif
</div>