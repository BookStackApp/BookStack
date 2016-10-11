
<div toolbox class="floating-toolbox">

    <div class="tabs primary-background-light">
        <span toolbox-toggle><i class="zmdi zmdi-caret-left-circle"></i></span>
        <span tab-button="tags" title="Page Tags" class="active"><i class="zmdi zmdi-tag"></i></span>
        @if(userCan('file-create-all'))
            <span tab-button="files" title="Attachments"><i class="zmdi zmdi-attachment"></i></span>
        @endif
    </div>

    <div tab-content="tags" ng-controller="PageTagController" page-id="{{ $page->id or 0 }}">
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
        <div tab-content="files" ng-controller="PageAttachmentController" page-id="{{ $page->id or 0 }}">
            <h4>Attached Files</h4>
            <div class="padded files">

                <div id="file-list" ng-show="!editFile">
                    <p class="muted small">Upload some files to display on your page. This are visible in the page sidebar.</p>
                    <drop-zone upload-url="@{{getUploadUrl()}}" uploaded-to="@{{uploadedTo}}" event-success="uploadSuccess"></drop-zone>

                    <hr class="even">

                    <div class="form-group">
                        <label for="attachment-via-link">File Name</label>
                        <input type="text" placeholder="File name" ng-model="file.name">
                    </div>
                    <div class="form-group">
                        <label for="attachment-via-link">Link to file</label>
                        <input type="text" placeholder="File url" ng-model="file.link">
                    </div>
                    <button type="button" ng-click="attachLinkSubmit(file)" class="button pos">Attach</button>


                    <table class="no-style" tag-autosuggestions style="width: 100%;">
                        <tbody ui-sortable="sortOptions" ng-model="files" >
                        <tr ng-repeat="file in files track by $index">
                            <td width="20" ><i class="handle zmdi zmdi-menu"></i></td>
                            <td ng-bind="file.name"></td>
                            <td width="10" ng-click="deleteFile(file)" class="text-center text-neg" style="padding: 0;"><i class="zmdi zmdi-close"></i></td>
                            <td width="10" ng-click="startEdit(file)" class="text-center text-neg" style="padding: 0;"><i class="zmdi zmdi-edit"></i></td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <div id="file-edit" ng-if="editFile">
                    <h5>Edit File</h5>
                    <div class="form-group">
                        <label for="attachment-name-edit">File Name</label>
                        <input type="text" id="attachment-name-edit" placeholder="File name" ng-model="editFile.name">
                    </div>
                    <hr class="even">
                    <drop-zone upload-url="@{{getUploadUrl(editFile)}}" uploaded-to="@{{uploadedTo}}" placeholder="Drop files or click here to upload and overwrite" event-success="uploadSuccessUpdate"></drop-zone>
                    <hr class="even">
                    <div class="form-group">
                        <label for="attachment-link-edit">Link to file</label>
                        <input type="text" id="attachment-link-edit" placeholder="File url" ng-model="editFile.link">
                    </div>

                    <button type="button" class="button" ng-click="cancelEdit()">Back</button>
                    <button type="button" class="button pos" ng-click="updateFile(editFile)">Save</button>
                </div>

            </div>
        </div>
    @endif

</div>