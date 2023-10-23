<main class="content-wrap mt-m card">
    <div class="grid half v-center no-row-gap">
        <h1 class="list-heading">{{ trans('entities.books') }}</h1>
        <div class="text-m-right my-m">
            @include('common.sort', $listOptions->getSortControlData())
        </div>
    </div>
    @if(count($books) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($books as $book)
                    @include('books.parts.list-item', ['book' => $book])
                @endforeach
            </div>
        @else
            <div class="grid third">
                @foreach($books as $key => $book)
                    @include('entities.grid-item', ['entity' => $book])
                @endforeach
            </div>
        @endif
        <div>
            {!! $books->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.books_empty') }}</p>
        @if(userCan('book-create-all'))
            <div class="icon-list block inline">
                <a href="{{ url("/create-book") }}"
                   class="icon-list-item text-book">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.create_now') }}</span>
                </a>
            </div>
        @endif
    @endif
</main>