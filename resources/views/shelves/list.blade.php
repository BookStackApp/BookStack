
<div class="content-wrap mt-m card">

    <div class="grid half v-center">
        <h1 class="list-heading">{{ trans('entities.shelves') }}</h1>
        <div class="text-right">
            @include('partials.sort', ['options' => $sortOptions, 'order' => $order, 'sort' => $sort, 'type' => 'bookshelves'])
        </div>
    </div>

    @if(count($shelves) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($shelves as $index => $shelf)
                    @if ($index !== 0)
                        <hr class="my-m">
                    @endif
                    @include('shelves.list-item', ['shelf' => $shelf])
                @endforeach
            </div>
        @else
            <div class="grid third">
                @foreach($shelves as $key => $shelf)
                    @include('shelves.grid-item', ['shelf' => $shelf])
                @endforeach
            </div>
        @endif
        <div>
            {!! $shelves->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.shelves_empty') }}</p>
        @if(userCan('bookshelf-create-all'))
            <a href="{{ url("/create-shelf") }}" class="button outline">@icon('edit'){{ trans('entities.create_now') }}</a>
        @endif
    @endif

</div>
