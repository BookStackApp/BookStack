<div id="image-manager" image-type="{{ $imageType }}" ng-controller="ImageManagerController" uploaded-to="{{ $uploaded_to or 0 }}">
    <div class="overlay" ng-cloak ng-click="hide()">
        <div class="popup-body" ng-click="$event.stopPropagation()">

            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('components.imagem_select') }}</div>
                <button class="popup-close neg corner-button button">x</button>
            </div>

            <div class="flex-fill image-manager-body">

                <div class="image-manager-content">
                    <div ng-if="imageType === 'gallery'" class="container">
                        <div class="image-manager-header row faded-small nav-tabs">
                            <div class="col-xs-4 tab-item" title="{{ trans('components.imagem_all_title') }}" ng-class="{selected: (view=='all')}" ng-click="setView('all')"><i class="zmdi zmdi-collection-image"></i> {{ trans('components.imagem_all') }}</div>
                            <div class="col-xs-4 tab-item" title="{{ trans('components.imagem_book_title') }}" ng-class="{selected: (view=='book')}" ng-click="setView('book')"><i class="zmdi zmdi-book text-book"></i> {{ trans('entities.book') }}</div>
                            <div class="col-xs-4 tab-item" title="{{ trans('components.imagem_page_title') }}" ng-class="{selected: (view=='page')}" ng-click="setView('page')"><i class="zmdi zmdi-file-text text-page"></i> {{ trans('entities.page') }}</div>
                        </div>
                    </div>
                    <div ng-show="view === 'all'" >
                        <form ng-submit="searchImages()" class="contained-search-box">
                            <input type="text" placeholder="{{ trans('components.imagem_search_hint') }}" ng-model="searchTerm">
                            <button ng-class="{active: searching}" title="{{ trans('common.search_clear') }}" type="button" ng-click="cancelSearch()" class="text-button cancel"><i class="zmdi zmdi-close-circle-o"></i></button>
                            <button title="{{ trans('common.search') }}" class="text-button" type="submit"><i class="zmdi zmdi-search"></i></button>
                        </form>
                    </div>
                    <div class="image-manager-list">
                        <div ng-repeat="image in images">
                            <div class="image anim fadeIn" ng-style="{animationDelay: ($index > 26) ? '160ms' : ($index * 25) + 'ms'}"
                                 ng-class="{selected: (image==selectedImage)}" ng-click="imageSelect(image)">
                                <img ng-src="@{{image.thumbs.gallery}}" ng-attr-alt="@{{image.title}}" ng-attr-title="@{{image.name}}">
                                <div class="image-meta">
                                    <span class="name" ng-bind="image.name"></span>
                                    <span class="date">{{ trans('components.imagem_uploaded', ['uploadedDate' => "{{ getDate(image.created_at) }" . "}"]) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="load-more" ng-show="hasMore" ng-click="fetchData()">{{ trans('components.imagem_load_more') }}</div>
                    </div>
                </div>

                <div class="image-manager-sidebar">
                    <div class="inner">

                        <div class="image-manager-details anim fadeIn" ng-show="selectedImage">

                            <form ng-submit="saveImageDetails($event)">
                                <div>
                                    <a ng-href="@{{selectedImage.url}}" target="_blank" style="display: block;">
                                        <img ng-src="@{{selectedImage.thumbs.gallery}}" ng-attr-alt="@{{selectedImage.title}}" ng-attr-title="@{{selectedImage.name}}">
                                    </a>
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ trans('components.imagem_image_name') }}</label>
                                    <input type="text" id="name" name="name" ng-model="selectedImage.name">
                                </div>
                            </form>

                            <div ng-show="dependantPages">
                                <p class="text-neg text-small">
                                    {{ trans('components.imagem_delete_confirm') }}
                                </p>
                                <ul class="text-neg">
                                    <li ng-repeat="page in dependantPages">
                                        <a ng-href="@{{ page.url }}" target="_blank" class="text-neg" ng-bind="page.name"></a>
                                    </li>
                                </ul>
                            </div>

                            <div class="clearfix">
                                <form class="float left" ng-submit="deleteImage($event)">
                                    <button class="button icon neg"><i class="zmdi zmdi-delete"></i></button>
                                </form>
                                <button class="button pos anim fadeIn float right" ng-show="selectedImage" ng-click="selectButtonClick()">
                                    <i class="zmdi zmdi-square-right"></i>{{ trans('components.imagem_select_image') }}
                                </button>
                            </div>

                        </div>

                        <drop-zone upload-url="@{{getUploadUrl()}}" uploaded-to="@{{uploadedTo}}" event-success="uploadSuccess"></drop-zone>


                    </div>
                </div>



            </div>

        </div>
    </div>
</div>