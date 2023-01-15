<div style="display: block;"
     refs="editor-toolbox@tab-content"
     data-tab-content="files"
     component="attachments"
     option:attachments:page-id="{{ $page->id ?? 0 }}"
     class="toolbox-tab-content">

    <h4>{{ trans('entities.attachments') }}</h4>
    <div class="px-l files">

        <div refs="attachments@listContainer">
            <p class="text-muted small">{{ trans('entities.attachments_explain') }} <span class="text-warn">{{ trans('entities.attachments_explain_instant_save') }}</span></p>

            <div component="tabs" refs="attachments@mainTabs" class="tab-container">
                <div class="nav-tabs">
                    <button refs="tabs@toggleItems" type="button" class="selected tab-item">{{ trans('entities.attachments_items') }}</button>
                    <button refs="tabs@toggleUpload" type="button" class="tab-item">{{ trans('entities.attachments_upload') }}</button>
                    <button refs="tabs@toggleLinks" type="button" class="tab-item">{{ trans('entities.attachments_link') }}</button>
                </div>
                <div refs="tabs@contentItems attachments@list">
                    @include('attachments.manager-list', ['attachments' => $page->attachments->all()])
                </div>
                <div refs="tabs@contentUpload" class="hidden">
                    @include('form.dropzone', [
                        'placeholder' => trans('entities.attachments_dropzone'),
                        'url' =>  url('/attachments/upload?uploaded_to=' . $page->id),
                        'successMessage' => trans('entities.attachments_file_uploaded'),
                    ])
                </div>
                <div refs="tabs@contentLinks" class="hidden link-form-container">
                    @include('attachments.manager-link-form', ['pageId' => $page->id])
                </div>
            </div>

        </div>

        <div refs="attachments@editContainer" class="hidden attachment-edit-container">

        </div>

    </div>
</div>