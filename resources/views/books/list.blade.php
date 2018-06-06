
@if($booksViewType === 'list')
    <div class="container small">
@else
    <div class="container">
@endif
    <h1>{{ trans('entities.books') }}</h1>
    @if(count($books) > 0)
        @if($booksViewType === 'list')
            @foreach($books as $book)
                @include('books/list-item', ['book' => $book])
                <hr>
            @endforeach
            {!! $books->render() !!}
        @else
             <div class="grid third">
                @foreach($books as $key => $book)
                        @include('books/grid-item', ['book' => $book])
                @endforeach
             </div>
            <div>
                {!! $books->render() !!}
            </div>
        @endif
    @else
        <p class="text-muted">{{ trans('entities.books_empty') }}</p>
        @if(userCan('books-create-all'))
            <a href="{{ baseUrl("/create-book") }}" class="text-pos">@icon('edit'){{ trans('entities.create_one_now') }}</a>
        @endif
    @endif
</div>