<div class="item-list-row flex-container-row py-xs px-m gap-m items-center">
    <div class="py-xs flex">
        <a href="{{ $set->getUrl() }}">{{ $set->name }}</a>
    </div>
    <div class="px-m text-small text-muted ml-auto">
        {{ implode(', ', array_map(fn ($op) => $op->getLabel(), $set->getOperations())) }}
    </div>
    <div>
        <span title="{{ trans('entities.tags_assigned_books') }}"
              class="flex fill-area min-width-xxs bold text-right text-book"><span class="opacity-60">@icon('book')</span>{{ $set->books_count ?? 0 }}</span>
    </div>
</div>