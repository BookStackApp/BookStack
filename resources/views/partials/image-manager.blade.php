<div id="image-manager" image-type="{{ $imageType }}" ng-controller="ImageManagerController">
    <div class="overlay anim-slide" ng-show="showing" ng-cloak ng-click="hide()">
        <div class="image-manager-body" ng-click="$event.stopPropagation()">

            <div class="image-manager-content">
                <div class="image-manager-list">
                    <div ng-repeat="image in images">
                        <img class="anim fadeIn"
                             ng-class="{selected: (image==selectedImage)}"
                             ng-src="@{{image.thumbs.gallery}}" ng-attr-alt="@{{image.title}}" ng-attr-title="@{{image.name}}"
                            ng-click="imageSelect(image)"
                            ng-style="{animationDelay: ($index > 26) ? '160ms' : ($index * 25) + 'ms'}">
                    </div>
                    <div class="load-more" ng-show="hasMore" ng-click="fetchData()">Load More</div>
                </div>
            </div>

            <button class="neg button image-manager-close" ng-click="hide()">x</button>

            <div class="image-manager-sidebar">
                <h2>Images</h2>
                <hr class="even">
                <drop-zone upload-url="@{{getUploadUrl()}}" event-success="uploadSuccess"></drop-zone>
                <div class="image-manager-details anim fadeIn" ng-show="selectedImage">

                    <hr class="even">

                    <form ng-submit="saveImageDetails($event)">
                        <div class="form-group">
                            <label for="name">Image Name</label>
                            <input type="text" id="name" name="name" ng-model="selectedImage.name">
                            <p class="text-pos text-small" ng-show="imageUpdateSuccess"><i class="fa fa-check"></i> Image name updated</p>
                            <p class="text-neg text-small" ng-show="imageUpdateFailure"><i class="fa fa-times"></i> <span ng-bind="imageUpdateFailure"></span></p>
                        </div>
                    </form>

                    <hr class="even">

                    <div ng-show="dependantPages">
                        <p class="text-neg text-small">
                            This image is used in the pages below, Click delete again to confirm you want to delete
                            this image.
                        </p>
                        <ul class="text-neg">
                            <li ng-repeat="page in dependantPages">
                                <a ng-href="@{{ page.url }}" target="_blank" class="text-neg" ng-bind="page.name"></a>
                            </li>
                        </ul>
                    </div>

                    <form ng-submit="deleteImage($event)">
                        <button class="button neg"><i class="zmdi zmdi-delete"></i>Delete Image</button>
                    </form>
                </div>

                <p class="text-pos" ng-show="imageDeleteSuccess"><i class="fa fa-check"></i> Image deleted</p>

                <div class="image-manager-bottom">
                    <button class="button pos anim fadeIn" ng-show="selectedImage" ng-click="selectButtonClick()">
                        <i class="zmdi zmdi-square-right"></i>Select Image
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>