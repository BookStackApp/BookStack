<div class="item-list-row flex-container-row py-xs px-m gap-m items-center">
    <div class="py-xs flex">
        <a href="{{ $rule->getUrl() }}">{{ $rule->name }}</a>
    </div>
    <div class="px-m text-small text-muted ml-auto">
        {{ implode(', ', array_map(fn ($op) => $op->getLabel(), $rule->getOperations())) }}
    </div>
    <div>
        <span title="{{ trans_choice('settings.sort_rule_assigned_to_x_books', $rule->books_count ?? 0) }}"
              class="flex fill-area min-width-xxs bold text-right text-book"><span class="opacity-60">@icon('book')</span>{{ $rule->books_count ?? 0 }}</span>
    </div>
</div>