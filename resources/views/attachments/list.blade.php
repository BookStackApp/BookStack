<div component="attachments-list">
    @foreach($attachments as $attachment)
        <div class="attachment icon-list">
            <div class="split-icon-list-item attachment-{{ $attachment->external ? 'link' : 'file' }}">
                <a href="{{ $attachment->getUrl() }}" @if($attachment->external) target="_blank" @endif>
                    <div class="icon">@icon($attachment->external ? 'export' : 'file')</div>
                    <div class="label">{{ $attachment->name }}</div>
                </a>
                @if(!$attachment->external)
                    <div component="dropdown" class="icon-list-item-dropdown">
                        <button refs="dropdown@toggle" type="button" class="icon-list-item-dropdown-toggle">@icon('caret-down')</button>
                        <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                            <a href="{{ $attachment->getUrl(false) }}" class="icon-item">
                                @icon('download')
                                <div>{{ trans('common.download') }}</div>
                            </a>
                            <a href="{{ $attachment->getUrl(true) }}" target="_blank" class="icon-item">
                                @icon('export')
                                <div>{{ trans('common.open_in_tab') }}</div>
                            </a>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>