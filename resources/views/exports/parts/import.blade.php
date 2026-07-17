<div class="item-list-row flex-container-row items-center justify-space-between wrap">
    <div class="px-m py-s">
        <a href="{{ $import->getUrl() }}"
           class="text-{{ $import->type }}">@icon($import->type) {{ $import->name }}</a>
    </div>
    <div class="px-m py-s flex-container-row gap-m items-center">
        <div class="bold opacity-80 text-muted">{{ $import->getSizeString() }}</div>
        <div class="bold opacity-80 text-muted min-width-xs text-right" title="{{ $dates->absolute($import->created_at) }}">@icon('time'){{ $dates->relative($import->created_at) }}</div>
    </div>
</div>