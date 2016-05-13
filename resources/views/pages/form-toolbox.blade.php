<div class="floating-toolbox">
    <div class="tabs primary-background-light">
        <span tab-button class="active"><i class="zmdi zmdi-tag"></i></span>
        <span tab-button><i class="zmdi zmdi-wrench"></i></span>
    </div>
    <div tab-content ng-controller="PageTagController" page-id="{{ $page->id or 0 }}">
        <form ng-submit="saveTags()" >
            <h4>Page Tags</h4>
            <div class="padded tags">
                <table class="no-style" style="width: 100%;">
                    <tr ng-repeat="tag in tags">
                        <td><input class="outline" type="text" ng-model="tag.name" ng-change="tagChange(tag)" ng-blur="tagBlur(tag)" placeholder="Tag"></td>
                        <td><input class="outline" type="text" ng-model="tag.value" ng-change="tagChange(tag)" ng-blur="tagBlur(tag)" placeholder="Tag Value (Optional)"></td>
                    </tr>
                </table>
            </div>
            <button class="button pos" type="submit">Save Tags</button>
        </form>
    </div>
</div>