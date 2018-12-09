
<div class="content-wrap card">
    <div class="grid halves v-center">
        <h1 class="list-heading">{{ trans('entities.books') }}</h1>
        <div class="text-right">

            @include('partials.sort', ['options' => $sortOptions, 'order' => $order, 'sort' => $sort])

        </div>
    </div>
    @if(count($books) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($books as $book)
                    @include('books.list-item', ['book' => $book])
                @endforeach
            </div>
        @else
             <div class="grid third">
                @foreach($books as $key => $book)
                    @include('books.grid-item', ['book' => $book])
                @endforeach
             </div>
        @endif
        <div>
            {!! $books->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.books_empty') }}</p>
        @if(userCan('books-create-all'))
            <a href="{{ baseUrl("/create-book") }}" class="text-pos">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif
</div>