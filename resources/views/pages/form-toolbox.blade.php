
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

            <draggable class="fake-table no-style tag-table" :options="{handle: '.handle'}" :list="tags" element="div" style="width: 100%;">
                <transition-group name="test" tag="div">
                    <div v-for="(tag, i) in tags" :key="tag.key">
                        <div width="20" class="handle" ><i class="zmdi zmdi-menu"></i></div>
                        <div><input autosuggest="{{ baseUrl('/ajax/tags/suggest/names') }}" autosuggest-type="name" class="outline" :name="getTagFieldName(i, 'name')"
                                   v-model="tag.name" @change="tagChange(tag)" @blur="tagBlur(tag)" placeholder="{{ trans('entities.tag') }}"></div>
                        <div><input autosuggest="{{ baseUrl('/ajax/tags/suggest/values') }}" autosuggest-type="value" class="outline" :name="getTagFieldName(i, 'value')"
                                   v-model="tag.value" @change="tagChange(tag)" @blur="tagBlur(tag)" placeholder="{{ trans('entities.tag_value') }}"></div>
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
        <div toolbox-tab-content="files" ng-controller="PageAttachmentController" page-id="{{ $page->id or 0 }}">
            <h4>{{ trans('entities.attachments') }}</h4>
            <div class="padded files">

                <div id="file-list" ng-show="!editFile">
                    <p class="muted small">{{ trans('entities.attachments_explain') }} <span class="secondary">{{ trans('entities.attachments_explain_instant_save') }}</span></p>

                    <div tab-container>
                        <div class="nav-tabs">
                            <div tab-button="list" class="tab-item">{{ trans('entities.attachments_items') }}</div>
                            <div tab-button="file" class="tab-item">{{ trans('entities.attachments_upload') }}</div>
                            <div tab-button="link" class="tab-item">{{ trans('entities.attachments_link') }}</div>
                        </div>
                        <div tab-content="list">
                            <table class="file-table" style="width: 100%;">
                                <tbody ui-sortable="sortOptions" ng-model="files" >
                                <tr ng-repeat="file in files track by $index">
                                    <td width="20" ><i class="handle zmdi zmdi-menu"></i></td>
                                    <td>
                                        <a ng-href="@{{getFileUrl(file)}}" target="_blank" ng-bind="file.name"></a>
                                        <div ng-if="file.deleting">
                                            <span class="neg small">{{ trans('entities.attachments_delete_confirm') }}</span>
                                            <br>
                                            <span class="text-primary small" ng-click="file.deleting=false;">{{ trans('common.cancel') }}</span>
                                        </div>
                                    </td>
                                    <td width="10" ng-click="startEdit(file)" class="text-center text-primary" style="padding: 0;"><i class="zmdi zmdi-edit"></i></td>
                                    <td width="5"></td>
                                    <td width="10" ng-click="deleteFile(file)" class="text-center text-neg" style="padding: 0;"><i class="zmdi zmdi-close"></i></td>
                                </tr>
                                </tbody>
                            </table>
                            <p class="small muted" ng-if="files.length == 0">
                                {{ trans('entities.attachments_no_files') }}
                            </p>
                        </div>
                        <div tab-content="file">
                            <drop-zone message="{{ trans('entities.attachments_dropzone') }}" upload-url="@{{getUploadUrl()}}" uploaded-to="@{{uploadedTo}}" event-success="uploadSuccess"></drop-zone>
                        </div>
                        <div tab-content="link" sub-form="attachLinkSubmit(file)">
                            <p class="muted small">{{ trans('entities.attachments_explain_link') }}</p>
                            <div class="form-group">
                                <label for="attachment-via-link">{{ trans('entities.attachments_link_name') }}</label>
                                <input placeholder="{{ trans('entities.attachments_link_name') }}" ng-model="file.name">
                                <p class="small neg" ng-repeat="error in errors.link.name" ng-bind="error"></p>
                            </div>
                            <div class="form-group">
                                <label for="attachment-via-link">{{ trans('entities.attachments_link_url') }}</label>
                                <input placeholder="{{ trans('entities.attachments_link_url_hint') }}" ng-model="file.link">
                                <p class="small neg" ng-repeat="error in errors.link.link" ng-bind="error"></p>
                            </div>
                            <button class="button pos">{{ trans('entities.attach') }}</button>

                        </div>
                    </div>

                </div>

                <div id="file-edit" ng-if="editFile" sub-form="updateFile(editFile)">
                    <h5>{{ trans('entities.attachments_edit_file') }}</h5>

                    <div class="form-group">
                        <label for="attachment-name-edit">{{ trans('entities.attachments_edit_file_name') }}</label>
                        <input type="text" id="attachment-name-edit" placeholder="{{ trans('entities.attachments_edit_file_name') }}" ng-model="editFile.name">
                        <p class="small neg" ng-repeat="error in errors.edit.name" ng-bind="error"></p>
                    </div>

                    <div tab-container="@{{ editFile.external ? 'link' : 'file' }}">
                        <div class="nav-tabs">
                            <div tab-button="file" class="tab-item">{{ trans('entities.attachments_upload') }}</div>
                            <div tab-button="link" class="tab-item">{{ trans('entities.attachments_set_link') }}</div>
                        </div>
                        <div tab-content="file">
                            <drop-zone upload-url="@{{getUploadUrl(editFile)}}" uploaded-to="@{{uploadedTo}}" placeholder="{{ trans('entities.attachments_edit_drop_upload') }}" event-success="uploadSuccessUpdate"></drop-zone>
                            <br>
                        </div>
                        <div tab-content="link">
                            <div class="form-group">
                                <label for="attachment-link-edit">{{ trans('entities.attachments_link_url') }}</label>
                                <input id="attachment-link-edit" placeholder="{{ trans('entities.attachment_link') }}" ng-model="editFile.link">
                                <p class="small neg" ng-repeat="error in errors.edit.link" ng-bind="error"></p>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="button" ng-click="cancelEdit()">{{ trans('common.back') }}</button>
                    <button class="button pos">{{ trans('common.save') }}</button>
                </div>

            </div>
        </div>
    @endif

</div>