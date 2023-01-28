<div style="display: block;"
     refs="editor-toolbox@tab-content"
     data-tab-content="files"
     component="attachments"
     option:attachments:page-id="{{ $page->id ?? 0 }}"
     class="toolbox-tab-content">

    <h4>{{ trans('entities.attachments') }}</h4>
    <div class="px-l files">

        <div refs="attachments@listContainer">
            <p class="text-muted small">{{ trans('entities.attachments_explain') }} <span
                        class="text-warn">{{ trans('entities.attachments_explain_instant_save') }}</span></p>

            <div component="tabs" refs="attachments@mainTabs" class="tab-container">
                <div role="tablist">
                    <button id="attachment-tab-items"
                            role="tab"
                            aria-selected="true"
                            aria-controls="attachment-panel-items"
                            type="button"
                            class="tab-item">{{ trans('entities.attachments_items') }}</button>
                    <button id="attachment-tab-upload"
                            role="tab"
                            aria-selected="false"
                            aria-controls="attachment-panel-upload"
                            type="button"
                            class="tab-item">{{ trans('entities.attachments_upload') }}</button>
                    <button id="attachment-tab-links"
                            role="tab"
                            aria-selected="false"
                            aria-controls="attachment-panel-links"
                            type="button"
                            class="tab-item">{{ trans('entities.attachments_link') }}</button>
                </div>
                <div id="attachment-panel-items"
                     tabindex="0"
                     role="tabpanel"
                     aria-labelledby="attachment-tab-items"
                     refs="attachments@list">
                    @include('attachments.manager-list', ['attachments' => $page->attachments->all()])
                </div>
                <div id="attachment-panel-upload"
                     tabindex="0"
                     role="tabpanel"
                     hidden
                     aria-labelledby="attachment-tab-upload">
                    @include('form.dropzone', [
                        'placeholder' => trans('entities.attachments_dropzone'),
                        'url' =>  url('/attachments/upload?uploaded_to=' . $page->id),
                        'successMessage' => trans('entities.attachments_file_uploaded'),
                    ])
                </div>
                <div id="attachment-panel-links"
                     tabindex="0"
                     role="tabpanel"
                     hidden
                     aria-labelledby="attachment-tab-links"
                     class="link-form-container">
                    @include('attachments.manager-link-form', ['pageId' => $page->id])
                </div>
            </div>

        </div>

        <div refs="attachments@editContainer" class="hidden attachment-edit-container">

        </div>

    </div>
</div>