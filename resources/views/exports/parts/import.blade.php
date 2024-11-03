@php
    $type = $import->getType();
@endphp
<div class="item-list-row flex-container-row items-center justify-space-between wrap">
    <div class="px-m py-s">
        <a href="{{ $import->getUrl() }}"
           class="text-{{ $type }}">@icon($type) {{ $import->name }}</a>
    </div>
    <div class="px-m py-s flex-container-row gap-m items-center">
        @if($type === 'book')
            <div class="text-chapter opacity-80 bold">@icon('chapter') {{ $import->chapter_count }}</div>
        @endif
        @if($type === 'book' || $type === 'chapter')
            <div class="text-page opacity-80 bold">@icon('page') {{ $import->page_count }}</div>
        @endif
        <div class="bold opacity-80">{{ $import->getSizeString() }}</div>
        <div class="bold opacity-80 text-muted" title="{{ $import->created_at->toISOString() }}">@icon('time'){{ $import->created_at->diffForHumans() }}</div>
    </div>
</div>