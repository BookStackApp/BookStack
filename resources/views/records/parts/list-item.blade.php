<a href="{{ $record->getUrl() }}" class="record entity-list-item" data-entity-type="record" data-entity-id="{{$record->id}}">
    <div class="entity-list-item-image bg-book" style="background-image: url('{{ $record->getRecordCover() }}')">
        @icon('book')
    </div>
    <div class="content">
        <h4 class="entity-list-item-name break-text">{{ $record->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text mb-s text-limit-lines-1">{{ $record->description }}</p>
        </div>
    </div>
</a>
