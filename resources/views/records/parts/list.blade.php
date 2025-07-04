<main class="content-wrap mt-m card">
    <div class="grid half v-center no-row-gap">
        <h1 class="list-heading">{{ trans('entities.records') }}</h1>
        <div class="text-m-right my-m">
            @include('common.sort', $listOptions->getSortControlData())
        </div>
    </div>
    @if(count($records) > 0)
        @if($view === 'list')
            <div class="entity-list">
                @foreach($records as $record)
                    @include('records.parts.list-item', ['record' => $record])
                @endforeach
            </div>
        @else
            <div class="grid third">
                @foreach($records as $key => $record)
                    @include('entities.grid-item', ['entity' => $record])
                @endforeach
            </div>
        @endif
        <div>
            {!! $records->render() !!}
        </div>
    @else
        <p class="text-muted">{{ trans('entities.records_empty') }}</p>
        @if(userCan('book-create-all'))
            <div class="icon-list block inline">
                <a href="{{ url("/create-record") }}"
                   class="icon-list-item text-record">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.create_now') }}</span>
                </a>
            </div>
        @endif
    @endif
</main>
