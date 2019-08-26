<div toolbox-tab-content="files" id="attachment-manager" page-id="{{ $page->id ?? 0 }}">

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

        <div id="file-list" v-show="!fileToEdit">
            <p class="text-muted small">{{ trans('entities.attachments_explain') }} <span class="text-warn">{{ trans('entities.attachments_explain_instant_save') }}</span></p>

            <div class="tab-container">
                <div class="nav-tabs">
                    <button type="button" @click="tab = 'list'" :class="{selected: tab === 'list'}"
                            class="tab-item">{{ trans('entities.attachments_items') }}</button>
                    <button type="button" @click="tab = 'file'" :class="{selected: tab === 'file'}"
                            class="tab-item">{{ trans('entities.attachments_upload') }}</button>
                    <button type="button" @click="tab = 'link'" :class="{selected: tab === 'link'}"
                            class="tab-item">{{ trans('entities.attachments_link') }}</button>
                </div>
                <div v-show="tab === 'list'">
                    <draggable style="width: 100%;" :options="{handle: '.handle'}" @change="fileSortUpdate" :list="files" element="div">
                        <div v-for="(file, index) in files" :key="file.id" class="card drag-card">
                            <div class="handle">@icon('grip')</div>
                            <div class="py-s">
                                <a :href="getFileUrl(file)" target="_blank" v-text="file.name"></a>
                                <div v-if="file.deleting">
                                    <span class="text-neg small">{{ trans('entities.attachments_delete_confirm') }}</span>
                                    <br>
                                    <button type="button" class="text-primary small" @click="file.deleting = false;">{{ trans('common.cancel') }}</button>
                                </div>
                            </div>
                            <button type="button" @click="startEdit(file)" class="drag-card-action text-center text-primary">@icon('edit')</button>
                            <button type="button" @click="deleteFile(file)" class="drag-card-action text-center text-neg">@icon('close')</button>
                        </div>
                    </draggable>
                    <p class="small text-muted" v-if="files.length === 0">
                        {{ trans('entities.attachments_no_files') }}
                    </p>
                </div>
                <div v-show="tab === 'file'">
                    <dropzone placeholder="{{ trans('entities.attachments_dropzone') }}" :upload-url="getUploadUrl()" :uploaded-to="pageId" @success="uploadSuccess"></dropzone>
                </div>
                <div v-show="tab === 'link'" @keypress.enter.prevent="attachNewLink(file)">
                    <p class="text-muted small">{{ trans('entities.attachments_explain_link') }}</p>
                    <div class="form-group">
                        <label for="attachment-via-link">{{ trans('entities.attachments_link_name') }}</label>
                        <input type="text" placeholder="{{ trans('entities.attachments_link_name') }}" v-model="file.name">
                        <p class="small text-neg" v-for="error in errors.link.name" v-text="error"></p>
                    </div>
                    <div class="form-group">
                        <label for="attachment-via-link">{{ trans('entities.attachments_link_url') }}</label>
                        <input type="text"  placeholder="{{ trans('entities.attachments_link_url_hint') }}" v-model="file.link">
                        <p class="small text-neg" v-for="error in errors.link.link" v-text="error"></p>
                    </div>
                    <button @click.prevent="attachNewLink(file)" class="button">{{ trans('entities.attach') }}</button>

                </div>
            </div>

        </div>

        <div id="file-edit" v-if="fileToEdit" @keypress.enter.prevent="updateFile(fileToEdit)">
            <h5>{{ trans('entities.attachments_edit_file') }}</h5>

            <div class="form-group">
                <label for="attachment-name-edit">{{ trans('entities.attachments_edit_file_name') }}</label>
                <input type="text" id="attachment-name-edit" placeholder="{{ trans('entities.attachments_edit_file_name') }}" v-model="fileToEdit.name">
                <p class="small text-neg" v-for="error in errors.edit.name" v-text="error"></p>
            </div>

            <div class="tab-container">
                <div class="nav-tabs">
                    <button type="button" @click="editTab = 'file'" :class="{selected: editTab === 'file'}" class="tab-item">{{ trans('entities.attachments_upload') }}</button>
                    <button type="button" @click="editTab = 'link'" :class="{selected: editTab === 'link'}" class="tab-item">{{ trans('entities.attachments_set_link') }}</button>
                </div>
                <div v-if="editTab === 'file'">
                    <dropzone :upload-url="getUploadUrl(fileToEdit)" :uploaded-to="pageId" placeholder="{{ trans('entities.attachments_edit_drop_upload') }}" @success="uploadSuccessUpdate"></dropzone>
                    <br>
                </div>
                <div v-if="editTab === 'link'">
                    <div class="form-group">
                        <label for="attachment-link-edit">{{ trans('entities.attachments_link_url') }}</label>
                        <input type="text" id="attachment-link-edit" placeholder="{{ trans('entities.attachment_link') }}" v-model="fileToEdit.link">
                        <p class="small text-neg" v-for="error in errors.edit.link" v-text="error"></p>
                    </div>
                </div>
            </div>

            <button type="button" class="button outline" @click="cancelEdit">{{ trans('common.back') }}</button>
            <button @click.enter.prevent="updateFile(fileToEdit)" class="button">{{ trans('common.save') }}</button>
        </div>

    </div>
</div>