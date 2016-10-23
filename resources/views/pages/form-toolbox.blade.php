
<div toolbox class="floating-toolbox">

    <div class="tabs primary-background-light">
        <span toolbox-toggle><i class="zmdi zmdi-caret-left-circle"></i></span>
        <span toolbox-tab-button="tags" title="Page Tags" class="active"><i class="zmdi zmdi-tag"></i></span>
        @if(userCan('file-create-all'))
            <span toolbox-tab-button="files" title="Attachments"><i class="zmdi zmdi-attachment"></i></span>
        @endif
    </div>

    <div toolbox-tab-content="tags" ng-controller="PageTagController" page-id="{{ $page->id or 0 }}">
        <h4>Page Tags</h4>
        <div class="padded tags">
            <p class="muted small">Add some tags to better categorise your content. <br> You can assign a value to a tag for more in-depth organisation.</p>
            <table class="no-style" tag-autosuggestions style="width: 100%;">
                <tbody ui-sortable="sortOptions" ng-model="tags" >
                    <tr ng-repeat="tag in tags track by $index">
                        <td width="20" ><i class="handle zmdi zmdi-menu"></i></td>
                        <td><input autosuggest="{{ baseUrl('/ajax/tags/suggest/names') }}" autosuggest-type="name" class="outline" ng-attr-name="tags[@{{$index}}][name]" type="text" ng-model="tag.name" ng-change="tagChange(tag)" ng-blur="tagBlur(tag)" placeholder="Tag"></td>
                        <td><input autosuggest="{{ baseUrl('/ajax/tags/suggest/values') }}" autosuggest-type="value" class="outline" ng-attr-name="tags[@{{$index}}][value]" type="text" ng-model="tag.value" ng-change="tagChange(tag)" ng-blur="tagBlur(tag)" placeholder="Tag Value (Optional)"></td>
                        <td width="10" ng-show="tags.length != 1" class="text-center text-neg" style="padding: 0;" ng-click="removeTag(tag)"><i class="zmdi zmdi-close"></i></td>
                    </tr>
                </tbody>
            </table>
            <table class="no-style" style="width: 100%;">
                <tbody>
                <tr class="unsortable">
                    <td  width="34"></td>
                    <td ng-click="addEmptyTag()">
                        <button type="button" class="text-button">Add another tag</button>
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    @if(userCan('file-create-all'))
        <div toolbox-tab-content="files" ng-controller="PageAttachmentController" page-id="{{ $page->id or 0 }}">
            <h4>Attachments</h4>
            <div class="padded files">

                <div id="file-list" ng-show="!editFile">
                    <p class="muted small">Upload some files or attach some link to display on your page. This are visible in the page sidebar.</p>

                    <div tab-container>
                        <div class="nav-tabs">
                            <div tab-button="list" class="tab-item">File List</div>
                            <div tab-button="file" class="tab-item">Upload File</div>
                            <div tab-button="link" class="tab-item">Attach Link</div>
                        </div>
                        <div tab-content="list">
                            <table class="file-table" style="width: 100%;">
                                <tbody ui-sortable="sortOptions" ng-model="files" >
                                <tr ng-repeat="file in files track by $index">
                                    <td width="20" ><i class="handle zmdi zmdi-menu"></i></td>
                                    <td>
                                        <a ng-href="@{{getFileUrl(file)}}" target="_blank" ng-bind="file.name"></a>
                                        <div ng-if="file.deleting">
                                            <span class="neg small">Click delete again to confirm you want to delete this attachment.</span>
                                            <br>
                                            <span class="text-primary small" ng-click="file.deleting=false;">Cancel</span>
                                        </div>
                                    </td>
                                    <td width="10" ng-click="startEdit(file)" class="text-center text-primary" style="padding: 0;"><i class="zmdi zmdi-edit"></i></td>
                                    <td width="5"></td>
                                    <td width="10" ng-click="deleteFile(file)" class="text-center text-neg" style="padding: 0;"><i class="zmdi zmdi-close"></i></td>
                                </tr>
                                </tbody>
                            </table>
                            <p class="small muted" ng-if="files.length == 0">
                                No files have been uploaded.
                            </p>
                        </div>
                        <div tab-content="file">
                            <drop-zone upload-url="@{{getUploadUrl()}}" uploaded-to="@{{uploadedTo}}" event-success="uploadSuccess"></drop-zone>
                        </div>
                        <div tab-content="link" sub-form="attachLinkSubmit(file)">
                            <p class="muted small">You can attach a link if you'd prefer not to upload a file. This can be a link to another page or a link to a file in the cloud.</p>
                            <div class="form-group">
                                <label for="attachment-via-link">Link Name</label>
                                <input type="text" placeholder="Link name" ng-model="file.name">
                                <p class="small neg" ng-repeat="error in errors.link.name" ng-bind="error"></p>
                            </div>
                            <div class="form-group">
                                <label for="attachment-via-link">Link to file</label>
                                <input type="text" placeholder="Url of site or file" ng-model="file.link">
                                <p class="small neg" ng-repeat="error in errors.link.link" ng-bind="error"></p>
                            </div>
                            <button type="submit" class="button pos">Attach</button>

                        </div>
                    </div>

                </div>

                <div id="file-edit" ng-if="editFile" sub-form="updateFile(editFile)">
                    <h5>Edit File</h5>

                    <div class="form-group">
                        <label for="attachment-name-edit">File Name</label>
                        <input type="text" id="attachment-name-edit" placeholder="File name" ng-model="editFile.name">
                        <p class="small neg" ng-repeat="error in errors.edit.name" ng-bind="error"></p>
                    </div>

                    <div tab-container="@{{ editFile.external ? 'link' : 'file' }}">
                        <div class="nav-tabs">
                            <div tab-button="file" class="tab-item">Upload File</div>
                            <div tab-button="link" class="tab-item">Set Link</div>
                        </div>
                        <div tab-content="file">
                            <drop-zone upload-url="@{{getUploadUrl(editFile)}}" uploaded-to="@{{uploadedTo}}" placeholder="Drop files or click here to upload and overwrite" event-success="uploadSuccessUpdate"></drop-zone>
                            <br>
                        </div>
                        <div tab-content="link">
                            <div class="form-group">
                                <label for="attachment-link-edit">Link to file</label>
                                <input type="text" id="attachment-link-edit" placeholder="Attachment link" ng-model="editFile.link">
                                <p class="small neg" ng-repeat="error in errors.edit.link" ng-bind="error"></p>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="button" ng-click="cancelEdit()">Back</button>
                    <button type="submit" class="button pos">Save</button>
                </div>

            </div>
        </div>
    @endif

</div>