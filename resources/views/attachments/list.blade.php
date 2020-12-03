@foreach($attachments as $attachment)
    <div class="attachment icon-list">
        <a class="icon-list-item py-xs" href="{{ $attachment->getUrl() }}" @if($attachment->external) target="_blank" @endif>
            <span class="icon">@icon($attachment->external ? 'export' : 'file')</span>
            <span>{{ $attachment->name }}</span>
        </a>
    </div>
@endforeach