<div id="image-manager" image-type="{{ $imageType }}" ng-controller="ImageManagerController" uploaded-to="{{ $uploaded_to or 0 }}">
    <div class="overlay anim-slide" ng-show="showing" ng-cloak ng-click="hide()">
        <div class="image-manager-body" ng-click="$event.stopPropagation()">

            <div class="image-manager-content">
                <div class="image-manager-list">
                    <div ng-repeat="image in images">
                        <div class="image anim fadeIn" ng-style="{animationDelay: ($index > 26) ? '160ms' : ($index * 25) + 'ms'}"
                             ng-class="{selected: (image==selectedImage)}" ng-click="imageSelect(image)">
                            <img ng-src="@{{image.thumbs.gallery}}" ng-attr-alt="@{{image.title}}" ng-attr-title="@{{image.name}}">
                            <div class="image-meta">
                                <span class="name" ng-bind="image.name"></span>
                                <span class="date">Uploaded @{{ getDate(image.created_at) | date:'mediumDate' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="load-more" ng-show="hasMore" ng-click="fetchData()">Load More</div>
                </div>
            </div>

            <button class="neg button image-manager-close" ng-click="hide()">x</button>

            <div class="image-manager-sidebar">
                <h2>Images</h2>
                <drop-zone upload-url="@{{getUploadUrl()}}" uploaded-to="@{{uploadedTo}}" event-success="uploadSuccess"></drop-zone>
                <div class="image-manager-details anim fadeIn" ng-show="selectedImage">

                    <hr class="even">

                    <form ng-submit="saveImageDetails($event)">
                        <div>
                            <a ng-href="@{{selectedImage.url}}" target="_blank" style="display: block;">
                                <img ng-src="@{{selectedImage.thumbs.gallery}}" ng-attr-alt="@{{selectedImage.title}}" ng-attr-title="@{{selectedImage.name}}">
                            </a>
                        </div>
                        <div class="form-group">
                            <label for="name">Image Name</label>
                            <input type="text" id="name" name="name" ng-model="selectedImage.name">
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

                <div class="image-manager-bottom">
                    <button class="button pos anim fadeIn" ng-show="selectedImage" ng-click="selectButtonClick()">
                        <i class="zmdi zmdi-square-right"></i>Select Image
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>