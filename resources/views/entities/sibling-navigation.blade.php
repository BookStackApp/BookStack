<div id="sibling-navigation" class="grid half collapse-xs items-center mb-m px-m no-row-gap fade-in-when-active print-hidden">
    <div>
        @if($previous)
            <a href="{{  $previous->getUrl()  }}" data-shortcut="prev" class="outline-hover no-link-style block rounded">
                <div class="px-m pt-xs text-muted">{{ trans('common.previous') }}</div>
                <div class="inline-block">
                    <div class="icon-list-item no-hover">
                        <span class="text-{{ $previous->getType() }} ">@icon($previous->getType())</span>
                        <span>{{ $previous->getShortName(48) }}</span>
                    </div>
                </div>
            </a>
        @endif
    </div>
    <div>
        @if($next)
            <a href="{{  $next->getUrl()  }}" data-shortcut="next" class="outline-hover no-link-style block rounded text-xs-right">
                <div class="px-m pt-xs text-muted text-xs-right">{{ trans('common.next') }}</div>
                <div class="inline block">
                    <div class="icon-list-item no-hover">
                        <span class="text-{{ $next->getType() }} ">@icon($next->getType())</span>
                        <span>{{ $next->getShortName(48) }}</span>
                    </div>
                </div>
            </a>
        @endif
    </div>
</div>