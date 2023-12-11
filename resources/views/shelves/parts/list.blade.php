<main class="content-wrap mt-m card">

    <div class="grid half v-center">
        <h1 class="list-heading">{{ trans('entities.shelves') }}</h1>
        <div class="text-right">
            @include('common.sort', $listOptions->getSortControlData())
        </div>
    </div>

    @if(count($shelves) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($shelves as $index => $shelf)
                    @if ($index !== 0)
                        <hr class="my-m">
                    @endif
                    @include('shelves.parts.list-item', ['shelf' => $shelf])
                @endforeach
            </div>
        @else
            <div class="grid third">
                @foreach($shelves as $key => $shelf)
                    @include('entities.grid-item', ['entity' => $shelf])
                @endforeach
            </div>
        @endif
        <div>
            {!! $shelves->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.shelves_empty') }}</p>
        @if(userCan('bookshelf-create-all'))
            <div class="icon-list block inline">
                <a href="{{ url("/create-shelf") }}"
                   class="icon-list-item text-bookshelf">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.create_now') }}</span>
                </a>
            </div>
        @endif
    @endif

</main>
