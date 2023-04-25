<div components="image-manager dropzone"
     option:dropzone:url="{{ url('/images/gallery?' . http_build_query(['uploaded_to' => $uploaded_to ?? 0])) }}"
     option:dropzone:success-message="{{ trans('components.image_upload_success') }}"
     option:dropzone:error-message="{{ trans('components.image_upload_failure') }}"
     option:dropzone:upload-limit="{{ config('app.upload_limit') }}"
     option:dropzone:upload-limit-message="{{ trans('errors.server_upload_limit') }}"
     option:dropzone:zone-text="{{ trans('components.image_dropzone_drop') }}"
     option:dropzone:file-accept="image/*"
     option:image-manager:uploaded-to="{{ $uploaded_to ?? 0 }}"
     class="image-manager">

    <div component="popup"
         refs="image-manager@popup"
         class="popup-background">
        <div class="popup-body" tabindex="-1">

            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('components.image_select') }}</div>
                <button refs="dropzone@selectButton" type="button">
                    <span>@icon('upload')</span>
                    <span>{{ trans('components.image_upload') }}</span>
                </button>
                <button refs="popup@hide" type="button" class="popup-header-close">@icon('close')</button>
            </div>

            <div refs="dropzone@drop-target" class="flex-fill image-manager-body">

                <div class="image-manager-content">
                    <div role="tablist" class="image-manager-header primary-background-light grid third no-gap">
                        <button refs="image-manager@filterTabs"
                                data-filter="all"
                                role="tab"
                                aria-selected="true"
                                type="button" class="tab-item" title="{{ trans('components.image_all_title') }}">@icon('images') {{ trans('components.image_all') }}</button>
                        <button refs="image-manager@filterTabs"
                                data-filter="book"
                                role="tab"
                                aria-selected="false"
                                type="button" class="tab-item" title="{{ trans('components.image_book_title') }}">@icon('book', ['class' => 'svg-icon']) {{ trans('entities.book') }}</button>
                        <button refs="image-manager@filterTabs"
                                data-filter="page"
                                role="tab"
                                aria-selected="false"
                                type="button" class="tab-item" title="{{ trans('components.image_page_title') }}">@icon('page', ['class' => 'svg-icon']) {{ trans('entities.page') }}</button>
                    </div>
                    <div>
                        <form refs="image-manager@searchForm" class="contained-search-box">
                            <input refs="image-manager@searchInput"
                                   placeholder="{{ trans('components.image_search_hint') }}"
                                   type="text">
                            <button refs="image-manager@cancelSearch"
                                    title="{{ trans('common.search_clear') }}"
                                    type="button"
                                    class="cancel">@icon('close')</button>
                            <button type="submit" class="primary-background text-white"
                                    title="{{ trans('common.search') }}">@icon('search')</button>
                        </form>
                    </div>
                    <div refs="image-manager@listContainer" class="image-manager-list"></div>
                </div>

                <div class="image-manager-sidebar flex-container-column">

                    <div refs="image-manager@dropzoneContainer">
                        <div refs="dropzone@status-area"></div>
                    </div>

                    <div refs="image-manager@form-container-placeholder" class="p-m text-small text-muted">
                        <p>{{ trans('components.image_intro') }}</p>
                        <p>{{ trans('components.image_intro_upload') }}</p>
                    </div>

                    <div refs="image-manager@formContainer" class="inner flex">
                    </div>
                </div>

            </div>

            <div class="popup-footer">
                <button refs="image-manager@selectButton" type="button" class="hidden button">
                    {{ trans('components.image_select_image') }}
                </button>
            </div>

        </div>
    </div>
</div>