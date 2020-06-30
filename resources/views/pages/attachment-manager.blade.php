<div style="display: block;" toolbox-tab-content="files"
     component="attachments"
     option:attachments:page-id="{{ $page->id ?? 0 }}">

    @exposeTranslations([
        'entities.attachments_file_uploaded',
        'entities.attachments_file_updated',
        'entities.attachments_link_attached',
        'entities.attachments_updated_success',
        'errors.server_upload_limit',
        'components.image_upload_remove',
        'components.file_upload_timeout',
    ])

    <h4>{{ trans('entities.attachments') }}</h4>
    <div class="px-l files">

        <div id="file-list">
            <p class="text-muted small">{{ trans('entities.attachments_explain') }} <span class="text-warn">{{ trans('entities.attachments_explain_instant_save') }}</span></p>

            <div component="tabs" refs="attachments@mainTabs" class="tab-container">
                <div class="nav-tabs">
                    <button refs="tabs@toggleItems" type="button" class="selected tab-item">{{ trans('entities.attachments_items') }}</button>
                    <button refs="tabs@toggleUpload" type="button" class="tab-item">{{ trans('entities.attachments_upload') }}</button>
                    <button refs="tabs@toggleLinks" type="button" class="tab-item">{{ trans('entities.attachments_link') }}</button>
                </div>
                <div refs="tabs@contentItems attachments@list">
                    @include('pages.attachment-list', ['attachments' => $page->attachments->all()])
                </div>
                <div refs="tabs@contentUpload" class="hiden">
                    @include('components.dropzone', [
                        'placeholder' => trans('entities.attachments_dropzone'),
                        'url' =>  url('/attachments/upload?uploaded_to=' . $page->id)
                    ])
                </div>
                <div refs="tabs@contentLinks" class="hidden">
                    <p class="text-muted small">{{ trans('entities.attachments_explain_link') }}</p>
                    <div class="form-group">
                        <label for="attachment_link_name">{{ trans('entities.attachments_link_name') }}</label>
                        <input name="attachment_link_name" id="attachment_link_name" type="text" placeholder="{{ trans('entities.attachments_link_name') }}">
                        <p class="small text-neg"></p>
                    </div>
                    <div class="form-group">
                        <label for="attachment_link_url">{{ trans('entities.attachments_link_url') }}</label>
                        <input name="attachment_link_url" id="attachment_link_url" type="text" placeholder="{{ trans('entities.attachments_link_url_hint') }}">
                        <p class="small text-neg"></p>
                    </div>
                    <button class="button">{{ trans('entities.attach') }}</button>
                </div>
            </div>

        </div>

        <div refs="attachments@editContainer" class="hidden">
            <h5>{{ trans('entities.attachments_edit_file') }}</h5>

            <div class="form-group">
                <label for="attachment-name-edit">{{ trans('entities.attachments_edit_file_name') }}</label>
                <input type="text" id="attachment-name-edit"
                       name="attachment_name"
                       placeholder="{{ trans('entities.attachments_edit_file_name') }}">
                <p class="small text-neg"></p>
            </div>

            <div component="tabs" class="tab-container">
                <div class="nav-tabs">
                    <button refs="tabs@toggleFile" type="button" class="tab-item selected">{{ trans('entities.attachments_upload') }}</button>
                    <button refs="tabs@toggleLink" type="button" class="tab-item">{{ trans('entities.attachments_set_link') }}</button>
                </div>
                <div refs="tabs@contentFile">
                    @include('components.dropzone', [
                        'placeholder' => trans('entities.attachments_edit_drop_upload'),
                        'url' =>  url('/attachments')
                    ])
                    <dropzone :upload-url="getUploadUrl(fileToEdit)" :uploaded-to="pageId" placeholder="{{ trans('entities.attachments_edit_drop_upload') }}" @success="uploadSuccessUpdate"></dropzone>
                    <br>
                </div>
                <div refs="tabs@contentLink" class="hidden">
                    <div class="form-group">
                        <label for="attachment-link-edit">{{ trans('entities.attachments_link_url') }}</label>
                        <input type="text" id="attachment-link-edit" placeholder="{{ trans('entities.attachment_link') }}" v-model="fileToEdit.link">
                        <p class="small text-neg"></p>
                    </div>
                </div>
            </div>

            <button type="button" class="button outline">{{ trans('common.back') }}</button>
            <button class="button">{{ trans('common.save') }}</button>
        </div>

    </div>
</div>