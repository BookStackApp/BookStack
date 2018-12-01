
<div class="content-wrap card {{ $booksViewType === 'list' ? 'thin' : '' }}">
    <h1 class="list-heading">{{ trans('entities.books') }}</h1>
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