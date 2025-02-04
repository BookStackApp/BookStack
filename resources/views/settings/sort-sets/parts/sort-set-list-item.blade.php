<div class="item-list-row flex-container-row py-xs items-center">
    <div class="py-xs px-m flex-2">
        <a href="{{ $set->getUrl() }}">{{ $set->name }}</a>
    </div>
    <div class="px-m text-small text-muted">
        {{ implode(', ', array_map(fn ($op) => $op->getLabel(), $set->getOperations())) }}
    </div>
</div>