<div component="attachments-list">
    <!-- Video Preview Component (always present for initialization) -->
    <div component="video-preview" style="display: none;" data-debug="video-preview-list"></div>
    
    @foreach($attachments as $attachment)
        <div class="attachment icon-list">
            <div class="split-icon-list-item attachment-{{ $attachment->external ? 'link' : 'file' }}">
                <a href="{{ $attachment->getUrl() }}"
                   refs="attachments-list@link-type-{{ $attachment->external ? 'link' : 'file' }}"
                   @if($attachment->external) target="_blank" @endif>
                    <div class="icon">@icon($attachment->external ? 'export' : ($attachment->isVideo() ? 'editor/media' : 'file'))</div>
                    <div class="label">{{ $attachment->name }}</div>
                </a>
                @if(!$attachment->external)
                    <div component="dropdown" class="icon-list-item-dropdown">
                        <button refs="dropdown@toggle" type="button" class="icon-list-item-dropdown-toggle">@icon('caret-down')</button>
                        <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                            @if($attachment->isVideo())
                                <li>
                                    <button type="button" 
                                            class="icon-item video-preview-btn" 
                                            data-video-url="{{ $attachment->getUrl(true) }}"
                                            data-video-name="{{ $attachment->name }}">
                                        @icon('editor/media')
                                        <div>{{ trans('common.preview') }}</div>
                                    </button>
                                </li>
                            @endif
                            <li>
                                <a href="{{ $attachment->getUrl(false) }}" class="icon-item">
                                    @icon('download')
                                    <div>{{ trans('common.download') }}</div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ $attachment->getUrl(true) }}" target="_blank" class="icon-item">
                                    @icon('export')
                                    <div>{{ trans('common.open_in_tab') }}</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>