
<div toolbox class="floating-toolbox">

    <div class="tabs primary-background-light">
        <span toolbox-toggle><i class="zmdi zmdi-caret-left-circle"></i></span>
        <span tab-button="tags" title="Page Tags" class="active"><i class="zmdi zmdi-tag"></i></span>
        <span tab-button="files" title="Attachments"><i class="zmdi zmdi-attachment"></i></span>
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

    <div tab-content="files" ng-controller="PageAttachmentController" page-id="{{ $page->id or 0 }}">
        <h4>Attached Files</h4>
        <div class="padded files">
            <p class="muted small">Upload some files to display on your page. This are visible in the page sidebar.</p>
            <drop-zone upload-url="@{{getUploadUrl()}}" uploaded-to="@{{uploadedTo}}" event-success="uploadSuccess"></drop-zone>

            <hr class="even">

            <div class="form-group">
                <label for="attachment-via-link">File Name</label>
                <input type="text" placeholder="File name" ng-model="fileName">
            </div>
            <div class="form-group">
                <label for="attachment-via-link">Link to file</label>
                <input type="text" placeholder="File url" ng-model="fileLink">
            </div>
            <button type="button" ng-click="attachLinkSubmit(fileName, fileLink)" class="button pos">Attach</button>


            <table class="no-style" tag-autosuggestions style="width: 100%;">
                <tbody ui-sortable="sortOptions" ng-model="files" >
                <tr ng-repeat="file in files track by $index">
                    <td width="20" ><i class="handle zmdi zmdi-menu"></i></td>
                    <td ng-bind="file.name"></td>
                    <td width="10" ng-click="deleteFile(file)" class="text-center text-neg" style="padding: 0;"><i class="zmdi zmdi-close"></i></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>