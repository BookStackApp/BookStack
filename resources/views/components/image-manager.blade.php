<div id="image-manager" image-type="{{ $imageType }}" uploaded-to="{{ $uploaded_to ?? 0 }}">

    @exposeTranslations([
        'components.image_delete_success',
        'components.image_upload_success',
        'errors.server_upload_limit',
        'components.image_upload_remove',
        'components.file_upload_timeout',
    ])

    <div overlay v-cloak @click="hide">
        <div class="popup-body" tabindex="-1" @click.stop>

            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('components.image_select') }}</div>
                <button class="popup-header-close" @click="hide()">x</button>
            </div>

            <div class="flex-fill image-manager-body">

                <div class="image-manager-content">
                    <div v-if="imageType === 'gallery' || imageType === 'drawio'" class="image-manager-header primary-background-light nav-tabs grid third">
                        <div class="tab-item" title="{{ trans('components.image_all_title') }}" :class="{selected: !filter}" @click="setFilterType(null)">@icon('images') {{ trans('components.image_all') }}</div>
                        <div class="tab-item" title="{{ trans('components.image_book_title') }}" :class="{selected: (filter=='book')}" @click="setFilterType('book')">@icon('book', ['class' => 'text-book svg-icon']) {{ trans('entities.book') }}</div>
                        <div class="tab-item" title="{{ trans('components.image_page_title') }}" :class="{selected: (filter=='page')}" @click="setFilterType('page')">@icon('page', ['class' => 'text-page svg-icon']) {{ trans('entities.page') }}</div>
                    </div>
                    <div>
                        <form @submit.prevent="searchImages" class="contained-search-box">
                            <input placeholder="{{ trans('components.image_search_hint') }}" v-model="searchTerm" type="text">
                            <button :class="{active: searching}" title="{{ trans('common.search_clear') }}" type="button" @click="cancelSearch()" class="text-button cancel">@icon('close')</button>
                            <button title="{{ trans('common.search') }}" class="text-button">@icon('search')</button>
                        </form>
                    </div>
                    <div class="image-manager-list">
                        <div v-if="images.length > 0" v-for="(image, idx) in images">
                            <div class="image anim fadeIn" :style="{animationDelay: (idx > 26) ? '160ms' : ((idx * 25) + 'ms')}"
                                 :class="{selected: (image==selectedImage)}" @click="imageSelect(image)">
                                <img :src="image.thumbs.gallery" :alt="image.title" :title="image.name">
                                <div class="image-meta">
                                    <span class="name" v-text="image.name"></span>
                                    <span class="date">{{ trans('components.image_uploaded', ['uploadedDate' => "{{ getDate(image.created_at) }" . "}"]) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="load-more" v-show="hasMore" @click="fetchData">{{ trans('components.image_load_more') }}</div>
                    </div>
                </div>

                <div class="image-manager-sidebar">

                    <dropzone v-if="imageType !== 'drawio'" ref="dropzone" placeholder="{{ trans('components.image_dropzone') }}" :upload-url="uploadUrl" :uploaded-to="uploadedTo" @success="uploadSuccess"></dropzone>

                    <div class="inner">

                        <div class="image-manager-details anim fadeIn" v-if="selectedImage">

                            <form @submit.prevent="saveImageDetails">
                                <div class="image-manager-viewer">
                                    <a :href="selectedImage.url" target="_blank" style="display: block;">
                                        <img :src="selectedImage.thumbs.display" :alt="selectedImage.name"
                                             :title="selectedImage.name">
                                    </a>
                                </div>
                                <div class="form-group">
                                    <label for="name">{{ trans('components.image_image_name') }}</label>
                                    <input id="name" class="input-base" name="name" v-model="selectedImage.name">
                                </div>
                            </form>

                            <div class="clearfix">
                                <div class="float left">
                                    <button type="button" class="button icon outline" @click="deleteImage">@icon('delete')</button>

                                </div>
                                <button class="button anim fadeIn float right" v-show="selectedImage" @click="callbackAndHide(selectedImage)">
                                    {{ trans('components.image_select_image') }}
                                </button>
                                <div class="clearfix"></div>
                                <div v-show="dependantPages">
                                    <p class="text-neg text-small">
                                        {{ trans('components.image_delete_used') }}
                                    </p>
                                    <ul class="text-neg">
                                        <li v-for="page in dependantPages">
                                            <a :href="page.url" target="_blank" class="text-neg" v-text="page.name"></a>
                                        </li>
                                    </ul>
                                </div>
                                <div v-show="deleteConfirm" class="text-neg text-small">
                                    {{ trans('components.image_delete_confirm') }}
                                </div>
                            </div>

                        </div>



                    </div>
                </div>

            </div>

        </div>
    </div>
</div>