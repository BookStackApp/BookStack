@if(userCan(\BookStack\Permissions\Permission::BookUpdate, $book) || $book->attachments->count() > 0)
    <div id="book-attachments" class="mb-l">
        <h5>{{ trans('entities.attachments') }}</h5>
        <div class="body">
            @if(userCan(\BookStack\Permissions\Permission::BookUpdate, $book) && userCan(\BookStack\Permissions\Permission::AttachmentCreateAll))
                <div component="attachments"
                     option:attachments:entity-id="{{ $book->id }}"
                     option:attachments:entity-type="book">
                    @php
                        $uploadUrl = url('/attachments/upload?uploaded_to=' . $book->id . '&entity_type=book');
                    @endphp
                    <div component="dropzone"
                         option:dropzone:url="{{ $uploadUrl }}"
                         option:dropzone:success-message="{{ trans('entities.attachments_file_uploaded') }}"
                         option:dropzone:error-message="{{ trans('errors.attachment_upload_error') }}"
                         option:dropzone:upload-limit="{{ config('app.upload_limit') }}"
                         option:dropzone:upload-limit-message="{{ trans('errors.server_upload_limit') }}"
                         option:dropzone:zone-text="{{ trans('entities.attachments_dropzone') }}"
                         option:dropzone:file-accept="*"
                         option:dropzone:allow-multiple="true"
                         class="mb-m">
                        <div refs="dropzone@drop-target" class="relative">
                            <div class="flex-container-row mb-s">
                                <button refs="dropzone@select-button" type="button" class="button outline small">{{ trans('entities.attachments_upload') }}</button>
                                <button refs="attachments@attach-link-button" type="button" class="button outline small">{{ trans('entities.attachments_link') }}</button>
                            </div>
                            <p class="text-muted text-small">{{ trans('entities.attachments_upload_drop') }}</p>
                            <div refs="dropzone@status-area" class="fixed top-right px-m py-m"></div>
                        </div>
                    </div>

                    <div refs="attachments@list-container attachments@list-panel">
                        @include('attachments.manager-list', ['attachments' => $book->attachments->all()])
                    </div>

                    <div id="link-form-container" refs="attachments@links-container" hidden class="mt-m">
                        @include('attachments.manager-link-form', [
                            'entityId' => $book->id,
                            'entityType' => 'book'
                        ])
                    </div>

                    <div id="edit-form-container" refs="attachments@edit-container" hidden class="mt-m"></div>
                </div>
            @else
                @include('attachments.list', ['attachments' => $book->attachments])
            @endif
        </div>
    </div>
@endif
