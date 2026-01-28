<div component="attachments-list">
    @foreach($attachments as $attachment)
        <div class="attachment icon-list">
            <div class="split-icon-list-item attachment-{{ $attachment->external ? 'link' : 'file' }}">
                @php
                    $attachmentUrl = $attachment->getUrl();
                    if (isset($shareToken)) {
                        $attachmentUrl .= (str_contains($attachmentUrl, '?') ? '&' : '?') . 'share_token=' . $shareToken;
                    }
                @endphp
                <a href="{{ $attachmentUrl }}"
                   refs="attachments-list@link-type-{{ $attachment->external ? 'link' : 'file' }}"
                   @if($attachment->external) target="_blank" @endif>
                    <div class="icon">@icon($attachment->external ? 'export' : 'file')</div>
                    <div class="label">{{ $attachment->name }}</div>
                </a>
                @if(!$attachment->external)
                    <div component="dropdown" class="icon-list-item-dropdown">
                        <button refs="dropdown@toggle" type="button" class="icon-list-item-dropdown-toggle">@icon('caret-down')</button>
                        <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                            @php
                                $downloadUrl = $attachment->getUrl(false);
                                $openUrl = $attachment->getUrl(true);
                                if (isset($shareToken)) {
                                    $downloadUrl .= (str_contains($downloadUrl, '?') ? '&' : '?') . 'share_token=' . $shareToken;
                                    $openUrl .= (str_contains($openUrl, '?') ? '&' : '?') . 'share_token=' . $shareToken;
                                }
                            @endphp
                            <a href="{{ $downloadUrl }}" class="icon-item">
                                @icon('download')
                                <div>{{ trans('common.download') }}</div>
                            </a>
                            <a href="{{ $openUrl }}" target="_blank" class="icon-item">
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