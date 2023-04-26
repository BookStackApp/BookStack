<div style="display: block;"
     refs="editor-toolbox@tab-content"
     data-tab-content="files"
     component="attachments"
     option:attachments:page-id="{{ $page->id ?? 0 }}"
     class="toolbox-tab-content">

    <h4>{{ trans('entities.attachments') }}</h4>
    <div component="dropzone"
         option:dropzone:url="{{ url('/attachments/upload?uploaded_to=' . $page->id) }}"
         option:dropzone:success-message="{{ trans('entities.attachments_file_uploaded') }}"
         option:dropzone:error-message="{{ trans('errors.attachment_upload_error') }}"
         option:dropzone:upload-limit="{{ config('app.upload_limit') }}"
         option:dropzone:upload-limit-message="{{ trans('errors.server_upload_limit') }}"
         option:dropzone:zone-text="{{ trans('entities.attachments_dropzone') }}"
         option:dropzone:file-accept="*"
         class="px-l files">

        <div refs="attachments@list-container dropzone@drop-target" class="relative">
            <p class="text-muted small">{{ trans('entities.attachments_explain') }} <span
                        class="text-warn">{{ trans('entities.attachments_explain_instant_save') }}</span></p>

            <hr class="mb-s">

            <div class="flex-container-row">
                <button refs="dropzone@select-button" type="button" class="button outline small">{{ trans('entities.attachments_upload') }}</button>
                <button refs="attachments@attach-link-button" type="button" class="button outline small">{{ trans('entities.attachments_link') }}</button>
            </div>
            <div>
                <p class="text-muted text-small">{{ trans('entities.attachments_upload_drop') }}</p>
            </div>
            <div refs="dropzone@status-area" class="fixed top-right px-m py-m"></div>

            <hr>

            <div refs="attachments@list-panel">
                @include('attachments.manager-list', ['attachments' => $page->attachments->all()])
            </div>

        </div>

        <div refs="attachments@links-container" hidden class="link-form-container">
            @include('attachments.manager-link-form', ['pageId' => $page->id])
        </div>

        <div refs="attachments@edit-container" hidden class="attachment-edit-container"></div>

    </div>
</div>