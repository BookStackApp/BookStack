@extends('layouts.simple')

@section('body')
    <div class="container small">
        <div class="my-s">
            <a href="{{ $entity->getUrl() }}" class="text-button">@icon('back') {{ trans('common.back') }}</a>
        </div>

        <div class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.share_links_manage') }}</h1>
            <p class="text-muted">{{ trans('entities.share_links_manage_desc') }}</p>

            @if ($errors->any())
                <div class="text-neg text-small mb-m">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ $entity->getUrl('/share-links') }}" method="POST" class="mt-m">
                {{ csrf_field() }}
                
                <div class="form-group">
                    <label for="name">{{ trans('entities.share_link_name') }}</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           placeholder="{{ trans('entities.share_link_name_placeholder') }}"
                           class="input-base">
                    <p class="small text-muted">{{ trans('entities.share_link_name_desc') }}</p>
                </div>

                <div class="form-group text-warn">
                    @icon('warning')
                    <strong>{{ trans('entities.share_link_warning') }}</strong>
                </div>

                <div class="form-group">
                    <button type="submit" class="button">{{ trans('entities.share_link_create') }}</button>
                </div>
            </form>

            @if(count($shareLinks) > 0)
                <hr class="my-m">
                
                <h5>{{ trans('entities.share_links_active') }}</h5>
                
                <div class="item-list mt-s">
                    @foreach($shareLinks as $shareLink)
                        <div class="item-list-row flex-container-row items-center wrap">
                            <div class="flex-2 py-s px-m">
                                @if($shareLink->name)
                                    <strong>{{ $shareLink->name }}</strong><br>
                                @endif
                                <span class="text-small text-muted">
                                    {{ trans('entities.share_link_created_by', ['user' => $shareLink->createdBy->name, 'date' => $shareLink->created_at->diffForHumans()]) }}
                                </span>
                            </div>
                            
                            <div class="flex py-s px-m">
                                <input type="text" 
                                       readonly 
                                       value="{{ $shareLink->getUrl() }}" 
                                       class="input-base share-link-input"
                                       data-share-url="{{ $shareLink->getUrl() }}">
                            </div>
                            
                            <div class="px-m py-xs">
                                <button type="button" 
                                        class="button outline share-link-copy-btn" 
                                        data-share-url="{{ $shareLink->getUrl() }}">
                                    @icon('copy') {{ trans('entities.share_link_copy') }}
                                </button>
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
                <p class="text-muted mt-m">{{ trans('entities.share_links_none') }}</p>
            @endif
        </div>
    </div>
@stop

@push('body-end')
@if(isset($cspNonce) && $cspNonce)
<script nonce="{{ $cspNonce }}">
(function() {
    const copiedText = {!! json_encode(trans('common.copied')) !!};
    const deleteConfirmText = {!! json_encode(trans('entities.share_link_delete_confirm')) !!};
    const checkIconHTML = {!! json_encode('<span>' . (new \BookStack\Util\SvgIcon('check'))->toHtml() . '</span>') !!};
    
    // Copy to clipboard functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.share-link-copy-btn')) {
            e.preventDefault();
            const button = e.target.closest('.share-link-copy-btn');
            const url = button.getAttribute('data-share-url');
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
                    // Fallback: select the input field
                    const input = button.closest('.item-list-row').querySelector('.share-link-input');
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
                // Fallback for older browsers
                const input = button.closest('.item-list-row').querySelector('.share-link-input');
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
    
    // Select input on click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.share-link-input')) {
            e.target.select();
        }
    });
})();
</script>
@endif
@endpush
