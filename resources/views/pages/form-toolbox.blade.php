
<div toolbox class="floating-toolbox">

    <div class="tabs primary-background-light">
        <span toolbox-toggle><i class="zmdi zmdi-caret-left-circle"></i></span>
        <span toolbox-tab-button="tags" title="{{ trans('entities.page_tags') }}" class="active"><i class="zmdi zmdi-tag"></i></span>
        @if(userCan('attachment-create-all'))
            <span toolbox-tab-button="files" title="{{ trans('entities.attachments') }}"><i class="zmdi zmdi-attachment"></i></span>
        @endif
    </div>

    <div toolbox-tab-content="tags" id="tag-manager" page-id="{{ $page->id or 0 }}">
        <h4>{{ trans('entities.page_tags') }}</h4>
        <div class="padded tags">
            <p class="muted small">{!! nl2br(e(trans('entities.tags_explain'))) !!}</p>

            <draggable class="fake-table no-style tag-table" :options="{handle: '.handle'}" :list="tags" element="div">
                <transition-group tag="div">
                    <div v-for="(tag, i) in tags" :key="tag.key">
                        <div width="20" class="handle" ><i class="zmdi zmdi-menu"></i></div>
                        <div>
                            <autosuggest url="/ajax/tags/suggest/names" type="name" class="outline" :name="getTagFieldName(i, 'name')"
                                   v-model="tag.name" @input="tagChange(tag)" @blur="tagBlur(tag)" placeholder="{{ trans('entities.tag') }}"/>
                        </div>
                        <div>
                            <autosuggest url="/ajax/tags/suggest/values" type="value" class="outline" :name="getTagFieldName(i, 'value')"
                                         v-model="tag.value" @change="tagChange(tag)" @blur="tagBlur(tag)" placeholder="{{ trans('entities.tag') }}"/>
                        </div>
                        <div width="10" v-show="tags.length !== 1" class="text-center text-neg" style="padding: 0;" @click="removeTag(tag)"><i class="zmdi zmdi-close"></i></div>
                    </div>
                </transition-group>
            </draggable>

            <table class="no-style" style="width: 100%;">
                <tbody>
                <tr class="unsortable">
                    <td width="34"></td>
                    <td @click="addEmptyTag">
                        <button type="button" class="text-button">{{ trans('entities.tags_add') }}</button>
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>

    @if(userCan('attachment-create-all'))
        <div toolbox-tab-content="files" id="attachment-manager" page-id="{{ $page->id or 0 }}">
            <h4>{{ trans('entities.attachments') }}</h4>
            <div class="padded files">

                <div id="file-list" v-show="!fileToEdit">
                    <p class="muted small">{{ trans('entities.attachments_explain') }} <span class="secondary">{{ trans('entities.attachments_explain_instant_save') }}</span></p>

                    <div class="tab-container">
                        <div class="nav-tabs">
                            <div @click="tab = 'list'" :class="{selected: tab === 'list'}" class="tab-item">{{ trans('entities.attachments_items') }}</div>
                            <div @click="tab = 'file'" :class="{selected: tab === 'file'}" class="tab-item">{{ trans('entities.attachments_upload') }}</div>
                            <div @click="tab = 'link'" :class="{selected: tab === 'link'}" class="tab-item">{{ trans('entities.attachments_link') }}</div>
                        </div>
                        <div v-show="tab === 'list'">
                            <draggable class="fake-table no-style " style="width: 100%;" :options="{handle: '.handle'}" @change="fileSortUpdate" :list="files" element="div">
                                <transition-group tag="div">
                                <div v-for="(file, index) in files" :key="file.id">
                                    <div width="20" ><i class="handle zmdi zmdi-menu"></i></div>
                                    <div>
                                        <a :href="getFileUrl(file)" target="_blank" v-text="file.name"></a>
                                        <div v-if="file.deleting">
                                            <span class="neg small">{{ trans('entities.attachments_delete_confirm') }}</span>
                                            <br>
                                            <span class="text-primary small" @click="file.deleting = false;">{{ trans('common.cancel') }}</span>
                                        </div>
                                    </div>
                                    <div width="10" @click="startEdit(file)" class="text-center text-primary" style="padding: 0;"><i class="zmdi zmdi-edit"></i></div>
                                    <div width="5"></div>
                                    <div width="10" @click="deleteFile(file)" class="text-center text-neg" style="padding: 0;"><i class="zmdi zmdi-close"></i></div>
                                </div>
                                </transition-group>
                            </draggable>
                            <p class="small muted" v-if="files.length === 0">
                                {{ trans('entities.attachments_no_files') }}
                            </p>
                        </div>
                        <div v-show="tab === 'file'">
                            <dropzone placeholder="{{ trans('entities.attachments_dropzone') }}" :upload-url="getUploadUrl()" :uploaded-to="pageId" @success="uploadSuccess"></dropzone>
                        </div>
                        <div v-show="tab === 'link'" @keypress.enter.prevent="attachNewLink(file)">
                            <p class="muted small">{{ trans('entities.attachments_explain_link') }}</p>
                            <div class="form-group">
                                <label for="attachment-via-link">{{ trans('entities.attachments_link_name') }}</label>
                                <input type="text" placeholder="{{ trans('entities.attachments_link_name') }}" v-model="file.name">
                                <p class="small neg" v-for="error in errors.link.name" v-text="error"></p>
                            </div>
                            <div class="form-group">
                                <label for="attachment-via-link">{{ trans('entities.attachments_link_url') }}</label>
                                <input type="text"  placeholder="{{ trans('entities.attachments_link_url_hint') }}" v-model="file.link">
                                <p class="small neg" v-for="error in errors.link.link" v-text="error"></p>
                            </div>
                            <button @click.prevent="attachNewLink(file)" class="button pos">{{ trans('entities.attach') }}</button>

                        </div>
                    </div>

                </div>

                <div id="file-edit" v-if="fileToEdit" @keypress.enter.prevent="updateFile(fileToEdit)">
                    <h5>{{ trans('entities.attachments_edit_file') }}</h5>

                    <div class="form-group">
                        <label for="attachment-name-edit">{{ trans('entities.attachments_edit_file_name') }}</label>
                        <input type="text" id="attachment-name-edit" placeholder="{{ trans('entities.attachments_edit_file_name') }}" v-model="fileToEdit.name">
                        <p class="small neg" v-for="error in errors.edit.name" v-text="error"></p>
                    </div>

                    <div class="tab-container">
                        <div class="nav-tabs">
                            <div @click="editTab = 'file'" :class="{selected: editTab === 'file'}" class="tab-item">{{ trans('entities.attachments_upload') }}</div>
                            <div @click="editTab = 'link'" :class="{selected: editTab === 'link'}" class="tab-item">{{ trans('entities.attachments_set_link') }}</div>
                        </div>
                        <div v-if="editTab === 'file'">
                            <dropzone :upload-url="getUploadUrl(fileToEdit)" :uploaded-to="pageId" placeholder="{{ trans('entities.attachments_edit_drop_upload') }}" @success="uploadSuccessUpdate"></dropzone>
                            <br>
                        </div>
                        <div v-if="editTab === 'link'">
                            <div class="form-group">
                                <label for="attachment-link-edit">{{ trans('entities.attachments_link_url') }}</label>
                                <input type="text" id="attachment-link-edit" placeholder="{{ trans('entities.attachment_link') }}" v-model="fileToEdit.link">
                                <p class="small neg" v-for="error in errors.edit.link" v-text="error"></p>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="button" @click="cancelEdit">{{ trans('common.back') }}</button>
                    <button @click.enter.prevent="updateFile(fileToEdit)" class="button pos">{{ trans('common.save') }}</button>
                </div>

            </div>
        </div>
    @endif

</div>