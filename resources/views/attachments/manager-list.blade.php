<div component="sortable-list" option:sortable-list:handle-selector=".handle">
    @foreach($attachments as $attachment)
        <div component="ajax-delete-row"
             option:ajax-delete-row:url="{{ url('/attachments/' . $attachment->id) }}"
             data-id="{{ $attachment->id }}"
             data-drag-content="{{ json_encode(['text/html' => $attachment->htmlLink(), 'text/plain' => $attachment->markdownLink()]) }}"
             class="card drag-card">
            <div class="handle">@icon('grip')</div>
            <div class="py-s">
                <a href="{{ $attachment->getUrl() }}" target="_blank" rel="noopener">{{ $attachment->name }}</a>
            </div>
            <div class="flex-fill justify-flex-end">
                <button component="event-emit-select"
                        option:event-emit-select:name="insert"
                        type="button"
                        title="{{ trans('entities.attachments_insert_link') }}"
                        class="drag-card-action text-center text-primary">@icon('link')                 </button>
                <button component="event-emit-select"
                        option:event-emit-select:name="edit"
                        option:event-emit-select:id="{{ $attachment->id }}"
                        type="button"
                        title="{{ trans('common.edit') }}"
                        class="drag-card-action text-center text-primary">@icon('edit')</button>
                <div component="dropdown" class="flex-fill relative">
                    <button refs="dropdown@toggle"
                            type="button"
                            title="{{ trans('common.delete') }}"
                            class="drag-card-action text-center text-neg">@icon('close')</button>
                    <div refs="dropdown@menu" class="dropdown-menu">
                        <p class="text-neg small px-m mb-xs">{{ trans('entities.attachments_delete') }}</p>
                        <button refs="ajax-delete-row@delete" type="button" class="text-primary small delete">{{ trans('common.confirm') }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @if (count($attachments) === 0)
        <p class="small text-muted">
            {{ trans('entities.attachments_no_files') }}
        </p>
    @endif
</div>