@extends('settings.layout')

@section('card')
    <h1 class="list-heading">{{ trans('entities.share_links') }}</h1>
    <p class="text-muted">{{ trans('settings.share_links_desc') }}</p>

    @php
        $shareLinkService = app(\BookStack\Entities\EntityShareLinkService::class);
        $allShareLinks = $shareLinkService->getAllShareLinks();
    @endphp

    @if(count($allShareLinks) > 0)
        <div class="item-list mt-m">
            @foreach($allShareLinks as $shareLink)
                <div class="item-list-row flex-container-row items-center wrap">
                    <div class="flex py-s px-m">
                        <div>
                            @if($shareLink->name)
                                <strong>{{ $shareLink->name }}</strong><br>
                            @endif
                            <span class="text-small">
                                <strong>{{ $shareLink->entity->name }}</strong>
                                ({{ $shareLink->entity->getType() }})
                            </span>
                            <br>
                            <span class="text-small text-muted">
                                {{ trans('entities.share_link_created_by', ['user' => $shareLink->createdBy->name, 'date' => $shareLink->created_at->diffForHumans()]) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex py-s px-m">
                        <input type="text" 
                               readonly 
                               value="{{ $shareLink->getUrl() }}" 
                               class="input-base"
                               onclick="this.select()">
                    </div>
                    
                    <div class="px-m py-xs">
                        <button type="button" 
                                class="button outline" 
                                data-copy-share-link="{{ $shareLink->getUrl() }}">
                            @icon('copy') {{ trans('entities.share_link_copy') }}
                        </button>
                    </div>
                    
                    <div class="px-m py-xs">
                        <a href="{{ $shareLink->entity->getUrl() }}" class="button outline" target="_blank">
                            @icon('open-book') {{ trans('common.view') }}
                        </a>
                    </div>
                    
                    <div class="px-m py-xs">
                        <form action="{{ url('/share-links/' . $shareLink->id) }}" 
                              method="POST" 
                              class="share-link-delete-form">
                            {{ csrf_field() }}
                            {{ method_field('DELETE') }}
                            <button type="submit" class="button outline">
                                @icon('delete') {{ trans('common.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-muted mt-m">{{ trans('settings.share_links_none') }}</p>
    @endif
@endsection

@push('body-end')
@if(isset($cspNonce) && $cspNonce)
<script nonce="{{ $cspNonce }}">
(function() {
    const copiedText = {!! json_encode(trans('common.copied')) !!};
    const deleteConfirmText = {!! json_encode(trans('entities.share_link_delete_confirm')) !!};
    const checkIconHTML = {!! json_encode('<span>' . (new \BookStack\Util\SvgIcon('check'))->toHtml() . '</span>') !!};
    
    // Copy to clipboard functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('[data-copy-share-link]')) {
            e.preventDefault();
            const button = e.target.closest('[data-copy-share-link]');
            const url = button.getAttribute('data-copy-share-link');
            const originalHTML = button.innerHTML;
            
            function showSuccess() {
                button.innerHTML = checkIconHTML + ' <span>' + copiedText + '</span>';
                button.classList.add('success');
                setTimeout(function() {
                    button.innerHTML = originalHTML;
                    button.classList.remove('success');
                }, 2000);
            }
            
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(function() {
                    showSuccess();
                }).catch(function(err) {
                    console.error('Clipboard API failed:', err);
                    const input = button.closest('.item-list-row').querySelector('input[readonly]');
                    if (input) {
                        input.focus();
                        input.select();
                        input.setSelectionRange(0, 99999);
                        try {
                            if (document.execCommand('copy')) {
                                showSuccess();
                            }
                        } catch (err) {
                            console.error('execCommand failed:', err);
                        }
                    }
                });
            } else {
                const input = button.closest('.item-list-row').querySelector('input[readonly]');
                if (input) {
                    input.focus();
                    input.select();
                    input.setSelectionRange(0, 99999);
                    try {
                        if (document.execCommand('copy')) {
                            showSuccess();
                        }
                    } catch (err) {
                        console.error('execCommand failed:', err);
                    }
                }
            }
        }
    });
    
    // Delete confirmation
    document.addEventListener('submit', function(e) {
        if (e.target.closest('.share-link-delete-form')) {
            if (!confirm(deleteConfirmText)) {
                e.preventDefault();
            }
        }
    });
})();
</script>
@endif
@endpush
